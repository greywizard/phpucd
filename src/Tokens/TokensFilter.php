<?php

namespace Greywizard\Phpucd\Tokens;

class TokensFilter implements \IteratorAggregate
{
    private $tokens;

    private $skipTypes = [];

    /**
     * TokensFilter constructor.
     * @param TokenInterface[]|\Traversable $tokens
     */
    public function __construct($tokens)
    {
        $this->tokens = $tokens;
    }

    public function getIterator()
    {
        foreach ($this->tokens as $token) {
            if (!$token->isString() && in_array($token->getType(), $this->skipTypes, true)) {
                continue;
            }

            yield $token;
        }
    }

    public function skip($type)
    {
        return $this->skipMany([$type]);
    }

    /**
     * @param int[] $types e.g. [T_OPEN_TAG, T_CLOSE_TAG]
     * @return $this
     */
    public function skipMany(array $types)
    {
        $this->skipTypes = array_merge($this->skipTypes, $types);

        return $this;
    }
}
