<?php

/**
 * Checks the class has a strict declaration type immediately preceding the opening tag and there
 * are no spaces within the declaration
 *
 * @author Scott Dawson <scott@loyaltycorp.com.au>
 * @copyright 2018 Loyalty Corp Pty Ltd (ABN 39 615 958 873)
 * @license https://github.com/loyaltycorp/standards/blob/master/licence BSD Licence
 */

namespace PHP_CodeSniffer\Standards\EoneoPay\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class StrictDeclarationFormatSniff implements Sniff
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

        // If declaration doesn't exist, skip
        $declarationPtr = $phpcsFile->findNext(T_DECLARE, $stackPtr);
        if (!\is_int($declarationPtr)) {
            return;
        }

        /** @var int $declarationPtr */
        $openingTag = $tokens[$stackPtr];
        $declaration = $tokens[$declarationPtr];

        // If not a strict type declaration, skip
        $declarationType = $tokens[(int)$phpcsFile->findNext(T_STRING, $declarationPtr)]['content'] ?? '';
        if (\mb_strtolower($declarationType) !== 'strict_types') {
            return;
        }

        // Check that the declaration immediately follows the opening tag
        if ($declaration['line'] !== $openingTag['line'] + 1) {
            $phpcsFile->addError(
                'Strict type declaration must be on the line immediately following the opening tag',
                $stackPtr,
                'StrictDeclarationFormat'
            );
        }

        // Ensure there are no leading spaces
        if ($declaration['column'] !== 1) {
            $phpcsFile->addError(
                'Strict type declaration must be on a new line with no leading whitespace',
                $stackPtr,
                'StrictDeclarationFormat'
            );
        }

        // Get pointers
        $openParenthesisPtr = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $declarationPtr);
        $stringPtr = $phpcsFile->findNext(T_STRING, (int)$openParenthesisPtr);
        $equalsPtr = $phpcsFile->findNext(T_EQUAL, (int)$stringPtr);
        $valuePtr = $phpcsFile->findNext(T_LNUMBER, (int)$equalsPtr);
        $closeParenthesisPtr = $phpcsFile->findNext(T_CLOSE_PARENTHESIS, (int)$valuePtr);
        $semicolonPtr = $phpcsFile->findNext(T_SEMICOLON, (int)$closeParenthesisPtr);

        // Get data
        $openParenthesis = $tokens[(int)$openParenthesisPtr];
        $string = $tokens[(int)$stringPtr];
        $equals = $tokens[(int)$equalsPtr];
        $value = $tokens[(int)$valuePtr];
        $closeParenthesis = $tokens[(int)$closeParenthesisPtr];
        $semicolon = $tokens[(int)$semicolonPtr];

        // Ensure declaration is exactly as expected
        if (!\is_int($openParenthesisPtr) ||
            !\is_int($stringPtr) ||
            !\is_int($equalsPtr) ||
            !\is_int($valuePtr) ||
            !\is_int($closeParenthesisPtr) ||
            !\is_int($semicolonPtr) ||
            $string['content'] !== 'strict_types' ||
            $value['content'] !== '1' ||
            $declaration['line'] !== $openParenthesis['line'] ||
            $declaration['line'] !== $string['line'] ||
            $declaration['line'] !== $equals['line'] ||
            $declaration['line'] !== $value['line'] ||
            $declaration['line'] !== $closeParenthesis['line'] ||
            $declaration['line'] !== $semicolon['line'] ||
            $declaration['column'] + $declaration['length'] !== $openParenthesis['column'] ||
            $openParenthesis['column'] + $openParenthesis['length'] !== $string['column'] ||
            $string['column'] + $string['length'] !== $equals['column'] ||
            $equals['column'] + $equals['length'] !== $value['column'] ||
            $value['column'] + $value['length'] !== $closeParenthesis['column'] ||
            $closeParenthesis['column'] + $closeParenthesis['length'] !== $semicolon['column']
        ) {
            $phpcsFile->addError(
                'Strict type declaration invalid, the only acceptable format is `declare(strict_types=1);`',
                $stackPtr,
                'StrictDeclarationFormat'
            );

            return;
        }
    }

    /**
     * Returns the token types that this sniff is interested in
     *
     * @return array
     */
    public function register()
    {
        return [T_OPEN_TAG];
    }
}
