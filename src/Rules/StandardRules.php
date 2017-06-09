<?php

namespace Greywizard\Phpucd\Rules;

class StandardRules implements RulesInterface
{
    public function isFunctionDisabled($function)
    {
        return in_array(strtolower($function), $this->getDisabledFunctions(), true);
    }

    public function isBacktickDisabled()
    {
        return $this->isFunctionDisabled('shell_exec');
    }

    public function isExitDisabled()
    {
        return $this->isFunctionDisabled('exit');
    }

    public function isEvalDisabled()
    {
        return $this->isFunctionDisabled('eval');
    }

    public function isVariableDisabled($name)
    {
        return in_array($name, $this->getDisabledVariables(), true);
    }

    public function isPrintVariableDisabled($name)
    {
        return in_array($name, $this->getPrintVarDisabled(), true);
    }

    protected function getPrintVarDisabled()
    {
        return ['$_GET', '$_POST'];
    }

    protected function getDisabledVariables()
    {
        return [
            '$GLOBALS',
        ];
    }

    protected function getDisabledFunctions()
    {
        return [
            'system',
            'exec',
            'popen',
            'pcntl_exec',
            'eval',
            'create_function',
            'preg_replace', // e
            'override_function',
            'rename_function',
            'var_dump',
            'var_export',
            'print_r',
            'mb_ereg_replace', // e
            'mb_eregi_replace', // e
            'shell_exec',
            'phpinfo',
            'ini_set',
            'exit',
            'die',
            'set_magic_quotes_runtime',
            'ini_get_all',
            'passthru',
            'assert',
        ];
    }
}
