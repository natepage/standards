<?php

namespace PHP_CodeSniffer\Standards\EoneoPay\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class NoElseSniff implements Sniff
{
    /***
     * {@inheritdoc}
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $phpcsFile->addError('Else statement must not be used', $stackPtr, 'NoElse');
    }

    /***
     * {@inheritdoc}
     */
    public function register()
    {
        return [T_ELSE];
    }
}
