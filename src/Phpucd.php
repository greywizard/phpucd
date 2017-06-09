<?php

namespace Greywizard\Phpucd;

use Greywizard\Phpucd\Errors\BacktickError;
use Greywizard\Phpucd\Errors\SourceError;
use Greywizard\Phpucd\Rules\RulesInterface;
use Greywizard\Phpucd\Tokens\TokenInterface;
use Greywizard\Phpucd\Tokens\Tokens;
use Greywizard\Phpucd\Tokens\TokensFilter;

class Phpucd
{
    private static $skipTokens = [
        T_OPEN_TAG,
        T_CLOSE_TAG,
        T_COMMENT,
        T_DOC_COMMENT,
        T_INLINE_HTML,
    ];

    /**
     * @param TokenInterface[]|\Traversable $tokens
     * @param RulesInterface $rules
     *
     * @return SourceError[]|\Traversable
     */
    public function getUnsafeTokens($tokens, RulesInterface $rules)
    {
        $backtickDisabled = $rules->isBacktickDisabled();

        $context = new Tokens();
        foreach ($this->filterTokens($tokens) as $token) {
            $context->add($token);
            if ($token->isString()) {
                if ($backtickDisabled && $token->getSource() === '`') {
                    yield new BacktickError($token->getLine());
                }

                if ($token->getSource() === '(') {
                    $processedContex = Tokens::createFromTokens(
                        (new TokensFilter($context))->skip(T_WHITESPACE)
                    );
                    $first = $processedContex->get(0);
                    $skipped = [T_FUNCTION, T_PRIVATE, T_PROTECTED, T_PUBLIC];
                    if (!$first->isString() && !in_array($first->getType(), $skipped, true)) {

                        $prev = $context->has(-2) ? $context->get(-2) : false;
                        $isPrevTString = $prev && !$prev->isString() && $prev->getType() === T_STRING;

                        $prevPrev = $context->has(-3) ? $context->get(-3) : false;
                        $isPrevPrevDoubleColon = $prevPrev
                            && !$prevPrev->isString() && $prevPrev->getType() === T_DOUBLE_COLON;

                        if (!$isPrevPrevDoubleColon && $isPrevTString && $rules->isFunctionDisabled($prev->getSource())) {
                            yield SourceError::createFromTokens($context, $prev->getSource());
                        }
                    }
                }

                if (in_array($token->getSource(), [';', '}', '{'], true)) {
                    $context->clear();
                }
                continue;
            }

            switch ($token->getType()) {
                case T_EXIT:
                    if ($rules->isExitDisabled()) {
                        yield SourceError::createFromToken($token);
                        continue;
                    }
                    break;

                case T_EVAL:
                    if ($rules->isEvalDisabled()) {
                        yield SourceError::createFromToken($token);
                        continue;
                    }
                    break;

                case T_VARIABLE:
                    if ($rules->isVariableDisabled($token->getSource())) {
                        yield SourceError::createFromToken($token);
                        continue;

                    }
                    if ($rules->isPrintVariableDisabled($token->getSource()) && $context->has(-3)) {
                        $prev = $context->get(-3);
                        if (!$prev->isString() && in_array($prev->getType(), [T_ECHO, T_PRINT, T_OPEN_TAG_WITH_ECHO], true)) {
                            yield SourceError::createFromTokens($context);
                            continue;
                        }
                    }
                    break;
            }
        }
    }

    /**
     * @param TokenInterface[]|\Traversable $tokens
     * @return TokenInterface[]|\Traversable
     */
    private function filterTokens($tokens)
    {
        return (new TokensFilter($tokens))->skipMany(self::$skipTokens);
    }
}
