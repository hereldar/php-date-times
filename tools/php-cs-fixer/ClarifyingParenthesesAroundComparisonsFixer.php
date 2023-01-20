<?php

declare(strict_types=1);

namespace Hereldar\Tools\PhpCsFixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class ClarifyingParenthesesAroundComparisonsFixer extends AbstractFixer
{
    private const TARGET_TOKENS = [
        T_AND_EQUAL,
        T_COALESCE_EQUAL,
        T_CONCAT_EQUAL,
        T_DIV_EQUAL,
        T_MINUS_EQUAL,
        T_MOD_EQUAL,
        T_MUL_EQUAL,
        T_OR_EQUAL,
        T_PLUS_EQUAL,
        T_POW_EQUAL,
        T_RETURN,
        T_XOR_EQUAL,
        T_YIELD,
        '=',
    ];

    private const COMPARISON_TOKENS = [
        T_BOOLEAN_AND,
        T_BOOLEAN_OR,
        T_IS_EQUAL,
        T_IS_GREATER_OR_EQUAL,
        T_IS_IDENTICAL,
        T_IS_NOT_EQUAL,
        T_IS_NOT_IDENTICAL,
        T_IS_SMALLER_OR_EQUAL,
        '>',
        '<',
    ];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Adds clarifying parentheses around comparisons.',
            [
                new CodeSample(
                    <<<'CODE'
                    $foo = $bar === null;
                    return 1 + 2;
                    CODE
                ),
            ]
        );
    }

    public function getName(): string
    {
        return 'Hereldar/clarifying_parentheses_around_comparisons';
    }

    /**
     * Must run after NoUnneededControlParenthesesFixer.
     */
    public function getPriority(): int
    {
        return 10;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::COMPARISON_TOKENS);
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        /**
         * @var int $blockStartIndex
         * @var Token $token
         */
        foreach ($tokens as $index => $token) {

            if (!$token->isGivenKind(self::TARGET_TOKENS)) {
                continue;
            }

            $blockStartIndex = $tokens->getNextMeaningfulToken($index);

            if (null === $blockStartIndex) {
                continue;
            }

            $blockStart = $tokens[$blockStartIndex];

            if ($blockStart->equalsAny(['(', [CT::T_BRACE_CLASS_INSTANTIATION_OPEN]])) {
                continue;
            }

            $blockEndIndex = $tokens->getNextTokenOfKind($blockStartIndex, [';', [T_CLOSE_TAG]]);

            if (null === $blockEndIndex
                || !$this->containsAnyComparisonToken($tokens, $blockStartIndex, $blockEndIndex)) {
                continue;
            }

            $tokens->insertAt($blockStartIndex, new Token('('));
            $tokens->insertAt($blockEndIndex + 1, new Token(')'));
        }
    }

    private function containsAnyComparisonToken(Tokens $tokens, int $startIndex, int $endIndex): bool
    {
        for ($i = $startIndex; $i < $endIndex; ++$i) {

            /** @var Token $token */
            $token = $tokens[$i];

            if ($token->isGivenKind(self::COMPARISON_TOKENS)) {
                return true;
            }
        }

        return false;
    }
}
