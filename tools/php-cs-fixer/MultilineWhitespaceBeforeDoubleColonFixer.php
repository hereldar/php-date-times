<?php

declare(strict_types=1);

namespace Hereldar\Tools\PhpCsFixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

use function count;

final class MultilineWhitespaceBeforeDoubleColonFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Move the double colon to the new line for chained calls.',
            [
                new CodeSample(<<<'PHP'
                    <?php
                    Class::method1()
                        ->method2()
                        ->method(3);
                    PHP),
            ]
        );
    }

    public function getName(): string
    {
        return 'Hereldar/multiline_whitespace_before_double_colon';
    }

    /**
     * Must run after NoSpaceAroundDoubleColonFixer.
     */
    public function getPriority(): int
    {
        return -10;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOUBLE_COLON);
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        for ($index = 0, $count = count($tokens); $index < $count; ++$index) {
            if (!$tokens[$index]->isGivenKind(T_DOUBLE_COLON)) {
                continue;
            }

            $indent = $this->findIndentBeforeNextObjectOperators($index, $tokens);
            if (null === $indent) {
                continue;
            }

            $newline = new Token([T_WHITESPACE, $lineEnding.$indent]);

            $previousIndex = $index - 1;
            $previous = $tokens[$previousIndex];

            if ($previous->isWhitespace()) {
                if ($previous->getContent() !== $newline->getContent()) {
                    $tokens[$previousIndex] = $newline;
                }
                continue;
            }

            $tokens->insertAt($index, [$newline]);
        }
    }

    private function findIndentBeforeNextObjectOperators(int $index, Tokens $tokens): ?string
    {
        while (true) {
            $index = $tokens->getNextMeaningfulToken($index);

            if ($index === null) {
                return null;
            }

            $token = $tokens[$index];

            if ($token->equalsAny([';', ':', '{', '}', [T_CLOSE_TAG]])) {
                return null;
            }

            if ($token->isObjectOperator()) {
                $previousIndex = $index - 1;
                $previous = $tokens[$previousIndex];

                if (str_contains($previous->getContent(), "\n")) {
                    return WhitespacesAnalyzer::detectIndent($tokens, $index);
                }
            }
        }
    }
}
