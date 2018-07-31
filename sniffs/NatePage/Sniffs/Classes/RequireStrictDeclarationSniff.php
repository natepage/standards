<?php

/**
 * Checks the class has a strict declaration type defined
 *
 * @author Scott Dawson <scott@loyaltycorp.com.au>
 * @copyright 2018 Loyalty Corp Pty Ltd (ABN 39 615 958 873)
 * @license https://github.com/loyaltycorp/standards/blob/master/licence BSD Licence
 */

namespace PHP_CodeSniffer\Standards\EoneoPay\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class RequireStrictDeclarationSniff implements Sniff
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

        // From opening tag, find declaration
        $declarationPtr = $phpcsFile->findNext(T_DECLARE, $stackPtr);

        // If file doesn't contain any declarations, error out
        if (!\is_int($declarationPtr)) {
            $phpcsFile->addError('Strict type declaration not found in file', $stackPtr, 'RequireStrictDeclaration');

            return;
        }

        // Cycle through declarations and attempt to find strict_types declaration
        $pointer = $stackPtr;
        while (false !== $pointer) {
            $stringPtr = $phpcsFile->findNext(T_STRING, $pointer);

            // If string isn't found, skip
            if (!\is_int($stringPtr)) {
                ++$pointer;
                continue;
            }

            // Get declaration string
            $declarationType = isset($tokens[$stringPtr]['content']) ?
                $tokens[$stringPtr]['content'] :
                '';

            // If not strict, skip
            if (\mb_strtolower($declarationType) !== 'strict_types') {
                $pointer = $phpcsFile->findNext(T_DECLARE, $stringPtr);
                continue;
            }

            // Found
            break;
        }

        // If not found, error out
        if ($pointer === false) {
            $phpcsFile->addError('Strict type declaration not found in file', $stackPtr, 'RequireStrictDeclaration');

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
