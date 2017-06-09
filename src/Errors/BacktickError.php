<?php

namespace Greywizard\Phpucd\Errors;

class BacktickError implements ErrorInterface
{
    private $line;

    public function __construct($line)
    {
        $this->line = $line;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function getMessage()
    {
        return '` - backtick operator @see http://php.net/manual/language.operators.execution.php';
    }

    public function hasHighlightedPart()
    {
        return false;
    }

    public function getHighlightedPart()
    {
        throw new \LogicException('Nothing to highlight');
    }
}
