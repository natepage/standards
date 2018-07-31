<?php
declare(strict_types=1);

/**
 * Checks no arrays have a trailing comma.
 *
 * @author Nathan Page <nathan.page@loyaltycorp.com.au>
 * @copyright 2018 Loyalty Corp Pty Ltd (ABN 39 615 958 873)
 * @license https://github.com/loyaltycorp/standards/blob/master/licence BSD Licence
 */

namespace PHP_CodeSniffer\Standards\EoneoPay\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;

class ForbiddenArrayTrailingCommaSniff implements Sniff
{
    /**
     * Returns the token types that this sniff is interested in
     *
     * @return int[]
     */
    public function register()
    {
        return [
            T_OPEN_SHORT_ARRAY
        ];
    }

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
        $tokens = $phpcsFile->getTokens();
        $arrayToken = $tokens[$stackPtr];
        $closeParenthesisPointer = $arrayToken['bracket_closer'];
        /** @var int $previousToCloseParenthesisPointer */
        $previousToCloseParenthesisPointer = TokenHelper::findPreviousEffective($phpcsFile, $closeParenthesisPointer - 1);
        $previousToCloseParenthesisToken = $tokens[$previousToCloseParenthesisPointer];

        if ($previousToCloseParenthesisToken['code'] !== T_COMMA) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            'Forbidden trailing comma after the last element.',
            $previousToCloseParenthesisPointer,
            'ForbiddenArrayTrailingComma'
        );

        if ($fix === false) {
            return;
        }

        $phpcsFile->fixer->beginChangeset();
        $phpcsFile->fixer->replaceToken($previousToCloseParenthesisPointer, '');
        $phpcsFile->fixer->endChangeset();
    }
}
