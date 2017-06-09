<?php

namespace Greywizard\Phpucd\Rules;

interface RulesInterface
{
    /**
     * @param $function
     * @return bool
     */
    public function isFunctionDisabled($function);

    /**
     * @return bool
     */
    public function isExitDisabled();

    /**
     * @return bool
     */
    public function isBacktickDisabled();

    /**
     * @return bool
     */
    public function isEvalDisabled();

    /**
     * @param string $name with leading $
     * @return bool
     */
    public function isVariableDisabled($name);

    /**
     * @param string $name with leading $
     * @return bool
     */
    public function isPrintVariableDisabled($name);
}
