<?php

namespace Greywizard\Phpucd\Tokens;

class Token implements TokenInterface
{
    private $line;

    private $type;

    private $source;

    public function __construct($line, $type, $source)
    {
        $this->line = $line;
        $this->type = $type;
        $this->source = $source;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function getType()
    {
        return $this->type;
    }

    public function isString()
    {
        return false;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getName()
    {
        return token_name($this->type);
    }

    public function isSuperGlobal()
    {
        return in_array($this->getSource(), ['$_SESSION', '$_GET', '$_POST', '$_SERVER'], true);
    }

    public function __debugInfo()
    {
        return [
            'name' => $this->getName(),
            'type' => $this->type,
            'source' => $this->source,
            'line' => $this->line,
        ];
    }
}
