<?php

namespace Greywizard\Phpucd\Errors;

use Greywizard\Phpucd\Tokens\TokenInterface;
use Greywizard\Phpucd\Tokens\Tokens;

class SourceError implements ErrorInterface
{
    private $source;

    private $line;

    private $toHighlight = false;

    public static function createFromToken(TokenInterface $token)
    {
        return new static($token->getSource(), $token->getLine());
    }

    public static function createFromTokens(Tokens $tokens, $toHighlight = false)
    {
        $result = new static((string) $tokens, $tokens->get(0)->getLine());
        $result->toHighlight = $toHighlight;

        return $result;
    }

    public function __construct($source, $line)
    {
        $this->source = $source;
        $this->line = $line;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function getMessage()
    {
        return $this->source;
    }

    public function hasHighlightedPart()
    {
        return $this->toHighlight !== false;
    }

    public function getHighlightedPart()
    {
        if (!$this->hasHighlightedPart()) {
            throw new \LogicException('Nothing to highlight');
        }

        return $this->toHighlight;
    }
}
