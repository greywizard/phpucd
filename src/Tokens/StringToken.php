<?php

namespace Greywizard\Phpucd\Tokens;

class StringToken implements TokenInterface
{
    private $line;

    private $source;

    public function __construct($line, $source)
    {
        $this->line = $line;
        $this->source = $source;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function getType()
    {
        throw new \LogicException('Token is string!');
    }

    public function isString()
    {
        return true;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getName()
    {
        throw new \LogicException('Token is string!');
    }

    public function isSuperGlobal()
    {
        throw new \LogicException('Token is string!');
    }
}
