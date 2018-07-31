<?php
declare(strict_types=1);

/**
 * Checks doc comment blocks follow our standards.
 *
 * @author Nathan Page <nathan.page@loyaltycorp.com.au>
 * @copyright 2018 Loyalty Corp Pty Ltd (ABN 39 615 958 873)
 * @license https://github.com/loyaltycorp/standards/blob/master/licence BSD Licence
 */

namespace PHP_CodeSniffer\Standards\EoneoPay\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\Annotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class DocCommentSpacingSniff implements Sniff
{
    /**
     * @var \PHP_CodeSniffer\Files\File
     */
    private $phpcsFile;

    /**
     * @var mixed[]
     */
    private $tokens;

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile
     * @param int $openPointer
     *
     * @return void
     *
     * @throws \SlevomatCodingStandard\Helpers\EmptyFileException
     */
    public function process(File $phpcsFile, $openPointer)
    {
        if (DocCommentHelper::isInline($phpcsFile, $openPointer)) {
            return;
        }

        $this->phpcsFile = $phpcsFile;
        $this->tokens = $phpcsFile->getTokens();

        $commentCloser = $this->tokens[$openPointer]['comment_closer'];

        $fcStartPointer = TokenHelper::findNextExcluding(
            $phpcsFile,
            [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR],
            $openPointer + 1,
            $commentCloser
        );

        if ($fcStartPointer === null) {
            return;
        }

        $annotations = $this->getAnnotations($openPointer);
        $annotationsCount = \count($annotations);
        $firstAnnotation = $annotationsCount > 0 ? $annotations[0] : null;
        $fcEndPointer = $this->getFirstContentEndPointer($fcStartPointer, $openPointer);
        $lcEndPointer = $annotationsCount > 0
            ? $annotations[$annotationsCount - 1]->getEndPointer()
            : $fcEndPointer;

        $this->checkLinesBeforeFirstContent($openPointer, $fcStartPointer);
        $this->checkLinesBetweenDescriptionAndFirstAnnotation($fcStartPointer, $fcEndPointer, $firstAnnotation);
        $this->checkLinesBetweenAnnotationsTypes($annotations);
        $this->checkLinesAfterLastContent($commentCloser, $lcEndPointer);
    }

    /**
     * @return mixed[]
     */
    public function register(): array
    {
        return [
            T_DOC_COMMENT_OPEN_TAG,
        ];
    }

    /**
     * Check lines between different annotations.
     *
     * @param \SlevomatCodingStandard\Helpers\Annotation $annotation
     * @param int $linesCount
     *
     * @return void
     */
    private function checkDifferentAnnotations(Annotation $annotation, $linesCount)
    {
        if ($linesCount === 1) {
            return;
        }

        $this->phpcsFile->addError(
            \sprintf('Expected 1 lines between different annotations types, found %d.', $linesCount),
            $annotation->getStartPointer(),
            'LineBetweenDifferentTags'
        );
    }

    /**
     * Check lines after last content.
     *
     * @param int $closePointer
     * @param int $lastPointer
     *
     * @return void
     *
     * @throws \SlevomatCodingStandard\Helpers\EmptyFileException
     */
    private function checkLinesAfterLastContent($closePointer, $lastPointer)
    {
        $lines = TokenHelper::getContent($this->phpcsFile, $lastPointer + 1, $closePointer);
        $linesCount = $this->countLines($lines);

        if ($linesCount === 0) {
            return;
        }

        $this->phpcsFile->addError(
            \sprintf('No lines allowed after last content, found %d.', $linesCount),
            $lastPointer,
            'LinesAfterLastContent'
        );
    }

    /**
     * Check lines before first content.
     *
     * @param int $openPointer
     * @param int $startPointer
     *
     * @throws \SlevomatCodingStandard\Helpers\EmptyFileException
     */
    private function checkLinesBeforeFirstContent($openPointer, $startPointer)
    {
        $tokens = $this->tokens;

        $lines = \substr($tokens[$openPointer]['content'], 0, \strlen('/**'));
        $lines .= TokenHelper::getContent($this->phpcsFile, $openPointer + 1, $startPointer - 1);
        $linesCount = $this->countLines($lines);

        if ($linesCount === 0) {
            return;
        }

        $this->phpcsFile->addError(
            \sprintf('No lines allowed before first content, found %d.', $linesCount),
            $startPointer,
            'LineBeforeFirstContent'
        );
    }

    /**
     * Check lines between annotations.
     *
     * @param \SlevomatCodingStandard\Helpers\Annotation[] $annotations
     *
     * @throws \SlevomatCodingStandard\Helpers\EmptyFileException
     */
    private function checkLinesBetweenAnnotationsTypes($annotations)
    {
        if (empty($annotations)) {
            return;
        }

        $previousAnnotation = null;

        foreach ($annotations as $annotation) {
            if ($previousAnnotation === null) {
                $previousAnnotation = $annotation;
                continue;
            }

            $lines = $this->countLinesBetweenAnnotations($annotation, $previousAnnotation);
            $currentName = $this->getAnnotationName($annotation);
            $previousName = $this->getAnnotationName($previousAnnotation);

            $currentName === $previousName
                ? $this->checkSameAnnotations($annotation, $lines)
                : $this->checkDifferentAnnotations($annotation, $lines);

            $previousAnnotation = $annotation;
        }
    }

    /**
     * Check lines between description and first annotation.
     *
     * @param int $startPointer
     * @param int $endPointer
     * @param null|\SlevomatCodingStandard\Helpers\Annotation $firstAnnotation
     *
     * @return void
     *
     * @throws \SlevomatCodingStandard\Helpers\EmptyFileException
     */
    private function checkLinesBetweenDescriptionAndFirstAnnotation(
        int $startPointer,
        int $endPointer,
        ?Annotation $firstAnnotation
    ) {
        if ($firstAnnotation === null || $startPointer === $firstAnnotation->getStartPointer()) {
            return;
        }

        \preg_match('~(\\s+)$~', $this->tokens[$endPointer]['content'], $matches);

        $lines = $matches[1] ?? '';
        $lines .= TokenHelper::getContent(
            $this->phpcsFile,
            $endPointer + 1,
            $firstAnnotation->getStartPointer() - 1
        );

        $linesCount = $this->countLines($lines);

        if ($linesCount === 1) {
            return;
        }

        $this->phpcsFile->addError(
            \sprintf('Expected 1 lines between description and annotations, found %d.', $linesCount),
            $firstAnnotation->getStartPointer(),
            'LinesBetweenDescriptionAndFirstAnnotation'
        );
    }

    /**
     * Check lines between same annotations.
     *
     * @param \SlevomatCodingStandard\Helpers\Annotation $annotation
     * @param int $linesCount
     *
     * @return void
     */
    private function checkSameAnnotations(Annotation $annotation, $linesCount)
    {
        if ($linesCount === 0) {
            return;
        }

        $this->phpcsFile->addError(
            \sprintf('No blank lines allowed between same tags. %d found.', $linesCount),
            $annotation->getStartPointer(),
            'LineBetweenSameTags'
        );
    }

    /**
     * Count lines in given contents.
     *
     * @param string $contents
     *
     * @return int
     */
    private function countLines($contents)
    {
        return \max(\substr_count($contents, $this->phpcsFile->eolChar) - 1, 0);
    }

    /**
     * Count lines between current and previous annotations.
     *
     * @param \SlevomatCodingStandard\Helpers\Annotation $current
     * @param \SlevomatCodingStandard\Helpers\Annotation $previous
     *
     * @return int
     *
     * @throws \SlevomatCodingStandard\Helpers\EmptyFileException
     */
    private function countLinesBetweenAnnotations(Annotation $current, Annotation $previous)
    {
        $tokens = $this->tokens;

        \preg_match('~(\\s+)$~', $tokens[$previous->getEndPointer()]['content'], $matches);

        $lines = $matches[1] ?? '';
        $lines .= TokenHelper::getContent(
            $this->phpcsFile,
            $previous->getEndPointer() + 1,
            $current->getStartPointer() - 1
        );

        return $this->countLines($lines);
    }

    /**
     * Get annotation name.
     *
     * @param \SlevomatCodingStandard\Helpers\Annotation $annotation
     *
     * @return string
     */
    private function getAnnotationName(Annotation $annotation)
    {
        $exploded = \explode('\\', $annotation->getName());

        return \reset($exploded);
    }

    /**
     * Get annotations.
     *
     * @param int $openPointer
     *
     * @return \SlevomatCodingStandard\Helpers\Annotation[]
     */
    private function getAnnotations($openPointer)
    {
        $annotations = \array_merge(
            [],
            ...\array_values(AnnotationHelper::getAnnotations($this->phpcsFile, $openPointer))
        );

        \uasort($annotations, function (Annotation $annotation1, Annotation $annotation2) {
            return $annotation1->getStartPointer() <=> $annotation2->getEndPointer();
        });

        return $annotations;
    }

    /**
     * Get first content end pointer.
     *
     * @param int $firstPointer
     * @param int $openPointer
     *
     * @return int
     */
    private function getFirstContentEndPointer($firstPointer, $openPointer)
    {
        $endPointer = $actualPointer = $firstPointer;

        do {
            /** @var int $actualPointer */
            $actualPointer = TokenHelper::findNextExcluding(
                $this->phpcsFile,
                [T_DOC_COMMENT_STAR, T_DOC_COMMENT_WHITESPACE],
                $actualPointer + 1,
                $this->tokens[$openPointer]['comment_closer'] + 1
            );

            if ($this->tokens[$actualPointer]['code'] !== T_DOC_COMMENT_STRING) {
                break;
            }

            $endPointer = $actualPointer;
        } while (true);

        return $endPointer;
    }
}
