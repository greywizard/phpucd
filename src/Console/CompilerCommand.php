<?php

namespace Greywizard\Phpucd\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
class CompilerCommand extends Command
{
    const COMMAND_NAME = 'compile';

    const DEFAULT_OUTPUT = 'phpucd.phar';

    protected function configure()
    {
        $this
            ->setName(static::COMMAND_NAME)
            ->addOption('output', '', InputOption::VALUE_OPTIONAL, 'Output file', static::DEFAULT_OUTPUT)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkDependencies();
        $phar = new \Phar( $input->getOption('output'));
        $phar->startBuffering();

        $files = (new Finder())
            ->files()
            ->in($this->getRootDir())
        ;

        $progressBar = new ProgressBar($output, count($files));
        $progressBar->setRedrawFrequency(1);
        $progressBar->setFormatDefinition('custom', ' %current%/%max% -- %message%');
        $progressBar->setFormat('custom');

        foreach ($files as $file) {
            /** @var SplFileInfo $file */
            $this->addFile($phar, $file);
            $progressBar->setMessage($file->getRelativePathname());
            $progressBar->advance();
        }
        $progressBar->setMessage('Complete');
        $progressBar->display();

        /** @var OutputInterface $output */
        $output->writeln('');

        $phar->setStub($this->getStub());
        $phar->stopBuffering();
    }

    private function checkDependencies()
    {
        $pharReadonly = strtolower((string) ini_get('phar.readonly'));
        if (!in_array($pharReadonly, ['', '0', 'off'], true)) {
            throw new \RuntimeException('Value phar.readonly must be disabled in php.ini');
        }
    }

    private function addFile(\Phar $phar, SplFileInfo $file)
    {
        switch ($file->getRelativePathname()) {
            case 'bin/compile':
            case 'src/Console/CompilerCommand.php':
                break;

            case 'bin/phpucd':
                $contents = preg_replace('{^#!/usr/bin/env php\s*}', '', $file->getContents());
                $phar->addFromString($file->getRelativePathname(), $contents);
                break;

            default:
                $phar->addFromString($file->getRelativePathname(), $file->getContents());
                break;
        }
    }

    private function getRootDir()
    {
        return dirname(dirname(__DIR__));
    }

    /**
     * @string
     */
    private function getStub()
    {
        return <<<'STUB'
#!/usr/bin/env php
<?php

Phar::mapPhar('phpucd.phar');

require 'phar://phpucd.phar/bin/phpucd';
__HALT_COMPILER();
STUB;
    }
}
