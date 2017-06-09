<?php

namespace Greywizard\Phpucd\Tokens;

use Traversable;

class Tokens implements \IteratorAggregate
{
    /**
     * @var TokenInterface[]
     */
    private $tokens = [];

    public static function createFromTokens($tokens)
    {
        $result = new static();
        $result->tokens = \Greywizard\Phpucd\toArray($tokens);

        return $result;
    }

    public function add(TokenInterface $token)
    {
        $this->tokens[] = $token;
    }

    public function clear()
    {
        $this->tokens = [];
    }

    public function trim()
    {
        $copy = $this->ltrim($this->tokens);
        $copy = array_reverse($copy);
        $copy = $this->ltrim($copy);
        $copy = array_reverse($copy);

        $result = new static();
        $result->tokens = $copy;

        return $result;
    }

    /**
     * @param int $index e.g. -1
     * @return bool
     */
    public function has($index)
    {
        return isset($this->tokens[$this->convertIndex($index)]);
    }

    /**
     * @param int $index e.g. -1
     * @return TokenInterface
     */
    public function get($index)
    {
        return $this->tokens[$this->convertIndex($index)];
    }

    public function __toString()
    {
        $result = '';
        foreach ($this->trim() as $token) {
            /** @var TokenInterface $token */
            $result .= $token->getSource();
        }

        return $result;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->tokens);
    }

    private function convertIndex($index)
    {
        if ($index >= 0) {
            return $index;
        }

        return count($this->tokens) + $index;
    }

    /**
     * @param TokenInterface[] $tokens
     * @return TokenInterface[]
     */
    private function ltrim(array $tokens)
    {
        while ($tokens) {
            $token = $tokens[0];
            if (!$token->isString() && $token->getType() === T_WHITESPACE) {
                array_shift($tokens);
                continue;
            }
            break;
        }

        return $tokens;
    }
}
