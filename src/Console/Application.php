<?php

namespace Greywizard\Phpucd\Console;

/**
 * @internal
 */
class Application extends \Symfony\Component\Console\Application
{
    const APPLICATION_NAME = 'Greywizard PHP Unsafe Code Detector';

    const APPLICATION_VERSION = '0.1.0';

    final public function __construct()
    {
        parent::__construct(static::APPLICATION_NAME, static::APPLICATION_VERSION);
        $this->initCommands();
    }

    private function initCommands()
    {
        $this->add(new AnalyzeCommand());
    }
}
