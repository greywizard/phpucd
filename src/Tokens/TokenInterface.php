<?php

namespace Greywizard\Phpucd\Tokens;

interface TokenInterface
{
    /**
     * @return int
     */
    public function getLine();

    /**
     * @return int e.g. T_EXIT
     */
    public function getType();

    /**
     * @return bool
     */
    public function isString();

    /**
     * @return string
     */
    public function getSource();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return bool
     */
    public function isSuperGlobal();
}
