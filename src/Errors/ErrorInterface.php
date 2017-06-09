<?php

namespace Greywizard\Phpucd\Errors;

interface ErrorInterface
{
    /**
     * @return int
     */
    public function getLine();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return bool
     */
    public function hasHighlightedPart();

    /**
     * @return string
     */
    public function getHighlightedPart();
}
