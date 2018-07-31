<?php

namespace PHP_CodeSniffer\Standards\EoneoPay\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class NoNotOperatorSniff implements Sniff
{
    /***
     * {@inheritdoc}
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $phpcsFile->addError('Strict comparision operator should be used instead', $stackPtr, 'NoNotOperator');
    }

    /***
     * {@inheritdoc}
     */
    public function register()
    {
        return [T_BOOLEAN_NOT, T_IS_NOT_EQUAL];
    }
}
