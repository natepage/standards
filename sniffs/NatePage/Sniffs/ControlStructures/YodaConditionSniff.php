<?php

/**
 * Checks the class does not use yoda conditions to evaluate conditional expressions
 *
 * @author Scott Dawson <scott@loyaltycorp.com.au>
 * @copyright 2018 Loyalty Corp Pty Ltd (ABN 39 615 958 873)
 * @license https://github.com/loyaltycorp/standards/blob/master/licence BSD Licence
 */

namespace PHP_CodeSniffer\Standards\EoneoPay\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class YodaConditionSniff implements Sniff
{
    /**
     * Processes this test, when one of its tokens is encountered
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned
     * @param int $stackPtr The position of the current token in the stack passed in $tokens
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        // Get tokens
        $tokens = $phpcsFile->getTokens();

        // Find open and closing parenthesis
        $openParenthesisPtr = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr + 1);
        $closeParenthesisPtr = $this->findClosingParenthesis($phpcsFile, $tokens, (int)$openParenthesisPtr);
        if (!\is_int($openParenthesisPtr) || !\is_int($closeParenthesisPtr)) {
            return;
        }

        // Loop through comparisons in statement
        $statementPtr = $openParenthesisPtr;
        while (false !== $statementPtr && $statementPtr < $closeParenthesisPtr) {
            // Process comparison
            $dividerPtr = $phpcsFile->findNext([T_BOOLEAN_AND, T_BOOLEAN_OR], $statementPtr);

            // Don't allow divider to exceed statement boundary
            if (!\is_int($dividerPtr) || $dividerPtr > $closeParenthesisPtr) {
                $dividerPtr = $closeParenthesisPtr;
            }

            // Find operator
            $operatorPtr = $phpcsFile->findNext([
                T_BITWISE_AND,
                T_BITWISE_OR,
                T_BITWISE_XOR,
                T_GREATER_THAN,
                T_IS_EQUAL,
                T_IS_GREATER_OR_EQUAL,
                T_IS_IDENTICAL,
                T_IS_NOT_EQUAL,
                T_IS_NOT_IDENTICAL,
                T_LESS_THAN,
                T_IS_SMALLER_OR_EQUAL
            ], $statementPtr, $dividerPtr);

            // If no operator is found there is nothing to do with this statement
            if (!\is_int($operatorPtr)) {
                $statementPtr = $dividerPtr + 1;
                continue;
            }

            // Get token(s) to the left and right of the operator
            $leftTokens = $this->getAllTokens($phpcsFile, $tokens, $statementPtr, $operatorPtr);
            $rightTokens = $this->getAllTokens(
                $phpcsFile,
                $tokens,
                $operatorPtr,
                $dividerPtr
            );

            // If tokens are identical (e.g. $variable === $variable), skip
            if ($leftTokens === $rightTokens) {
                $statementPtr = $dividerPtr + 1;
                continue;
            }

            // If there is a variable on the right but not the left, throw error
            if (!\array_key_exists(T_VARIABLE, $leftTokens) && \array_key_exists(T_VARIABLE, $rightTokens)) {
                $phpcsFile->addError(
                    'Yoda conditions must not be used',
                    $rightTokens[T_VARIABLE],
                    'YodaConditions'
                );
            }

            // Move to statement after the divider
            $statementPtr = $dividerPtr + 1;
        }
    }

    /**
     * Returns the token types that this sniff is interested in
     *
     * @return array
     */
    public function register()
    {
        return [T_ELSEIF, T_IF];
    }

    /**
     * Find matching closing parenthesis for an opening parenthesis
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being checked
     * @param array $tokens The tokens found in file
     * @param int $start The position in the token stack to start searching from
     *
     * @return int|bool
     */
    private function findClosingParenthesis(File $phpcsFile, array $tokens, $start)
    {
        // Start with one open and zero closes since the start will include an open
        $openCount = 1;
        $closeCount = 0;

        // Iterate until close and open count matches
        $pointer = $start + 1;
        while ($openCount > $closeCount) {
            $tokenPtr = $phpcsFile->findNext([T_CLOSE_PARENTHESIS, T_OPEN_PARENTHESIS], $pointer);

            // If token isn't found, return
            if (!\is_int($tokenPtr)) {
                return false;
            }

            // Increment pointer
            $pointer = $tokenPtr + 1;

            // Increment counters
            switch ($tokens[$tokenPtr]['code']) {
                case T_CLOSE_PARENTHESIS:
                    ++$closeCount;
                    break;

                case T_OPEN_PARENTHESIS:
                    ++$openCount;
                    break;
            }

            // If counts match, return
            if ($openCount === $closeCount) {
                return $pointer;
            }
        }
    }

    /**
     * Get all tokens matching types between two pointers
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being checked
     * @param array $tokens The tokens found in file
     * @param int $start The position in the token stack to start searching from
     * @param int $end The position in the token stack to stop search at
     *
     * @return array
     */
    private function getAllTokens(File $phpcsFile, array $tokens, $start, $end): array
    {
        $found = [];

        $pointer = $start + 1;
        while (false !== $pointer && $pointer < $end) {
            $tokenPtr = $phpcsFile->findNext([T_VARIABLE], $pointer, $end - 1);

            // If no more tokens are found, return
            if (!\is_int($tokenPtr)) {
                return $found;
            }

            // Capture token
            $found[$tokens[$tokenPtr]['code']] = $tokenPtr;

            // Move pointer to next token
            $pointer = $tokenPtr + 1;
        }

        return $found;
    }
}
