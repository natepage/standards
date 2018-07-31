<?php
declare(strict_types=1);

/**
 * Checks only short syntax is used to define arrays.
 *
 * @author Nathan Page <nathan.page@loyaltycorp.com.au>
 * @copyright 2018 Loyalty Corp Pty Ltd (ABN 39 615 958 873)
 * @license https://github.com/loyaltycorp/standards/blob/master/licence BSD Licence
 */

namespace PHP_CodeSniffer\Standards\EoneoPay\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ForbiddenArrayLongSyntaxSniff implements Sniff
{
    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $error = 'Short array syntax must be used to define arrays';
        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'ForbiddenArrayLongSyntax');

        if ($fix === true) {
            $tokens = $phpcsFile->getTokens();
            $opener = $tokens[$stackPtr]['parenthesis_opener'];
            $closer = $tokens[$stackPtr]['parenthesis_closer'];

            $phpcsFile->fixer->beginChangeset();

            if ($opener === null) {
                $phpcsFile->fixer->replaceToken($stackPtr, '[]');

                return;
            }

            $phpcsFile->fixer->replaceToken($stackPtr, '');
            $phpcsFile->fixer->replaceToken($opener, '[');
            $phpcsFile->fixer->replaceToken($closer, ']');
            $phpcsFile->fixer->endChangeset();
        }
    }

    /**
     * Returns the token types that this sniff is interested in
     *
     * @return int[]
     */
    public function register()
    {
        return [
            T_ARRAY
        ];
    }
}
