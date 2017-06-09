<?php

namespace Greywizard\Phpucd\Console;

use Greywizard\Phpucd\Errors\ErrorInterface;
use Greywizard\Phpucd\Phpucd;
use Greywizard\Phpucd\Rules\StandardRules;
use Greywizard\Phpucd\Tokens\Tokenizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
class AnalyzeCommand extends Command
{
    const COMMAND_NAME = 'analyze';
    const COMMAND_ALIAS = 'analyse';
    const COMMAND_DESCRIPTION = 'Analyze PHP code';

    const DEFAULT_EXT = '*.php';

    protected function configure()
    {
        $this
            ->setName(static::COMMAND_NAME)
            ->setAliases([static::COMMAND_ALIAS])
            ->setDescription(static::COMMAND_DESCRIPTION)
            ->addArgument('directory', InputArgument::REQUIRED)
            ->addOption(
                'name',
                null,
                InputOption::VALUE_OPTIONAL,
                'Pattern e.g. *.php',
                static::DEFAULT_EXT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf(
            '%s <info>%s</info>',
            Application::APPLICATION_NAME,
            Application::APPLICATION_VERSION
        ));
        $output->writeln('');

        $finder = new Finder();
        $dirArgument = rtrim($input->getArgument('directory'), '/\\');
        $extOption = $input->getOption('name');
        /** @var SplFileInfo[] $files */
        $files = $finder->files()->in($dirArgument)->name($extOption);
        $progressBar = new ProgressBar($output, count($files));
        $progressBar->setRedrawFrequency(1);
        $rules = new StandardRules();
        $errors = [];
        $countErrors = 0;
        $countFiles = 0;
        $phpucd = new Phpucd();

        $progressBar->display();
        foreach ($files as $file) {
            $output->write(' ' . $file->getRelativePathname());
            $fileErrors = \Greywizard\Phpucd\toArray($phpucd->getUnsafeTokens(
                Tokenizer::createFromFile($file),
                $rules
            ));
            if (count($fileErrors)) {
                $countErrors += count($fileErrors);
                $errors[$file->getPathname()] = $fileErrors;
            }
            $countFiles++;
            $progressBar->advance();
        }

        $output->writeln('');
        $output->writeln('');

        if ($countErrors) {
            $this->handleErrors($output, $countErrors, $errors, $countFiles);
            return 1;
        }

        $this->handleNoErrors($output, $countFiles);
    }

    /**
     * @param OutputInterface $output
     * @param int $countErrors
     * @param array $errors e.g. [filename => [TokenInterface, TokenInterface, ...], ...]
     * @param int $countFiles
     */
    private function handleErrors(OutputInterface $output, $countErrors, $errors, $countFiles)
    {
        $margin = 2;
        $errorMsg = sprintf('Ooops... Found %d errors in %d files', $countErrors, $countFiles);
        $emptyLine = '<error>' . str_pad('', strlen($errorMsg) + $margin * 2, ' ') . '</error>';
        $errorMsgFormat = '<error>' . str_pad('', $margin, ' ') . $errorMsg . str_pad('', $margin, ' ') . '</error>';

        $output->writeln($emptyLine);
        $output->writeln($errorMsgFormat);
        $output->writeln($emptyLine);
        $output->writeln('');

        $indent = '  * ';
        foreach ($errors as $filename => $fileErrors) {
            $output->writeln('<comment>' . $filename . ':</comment>');
            foreach ($fileErrors as $error) {
                /** @var ErrorInterface $error */
                $output->writeln($indent . '<comment>' . $error->getLine() . '</comment>: ' . $this->decorateSourceToken($error));
            }
        }
    }

    /**
     * @param OutputInterface $output
     * @param int $countFiles
     */
    private function handleNoErrors(OutputInterface $output, $countFiles)
    {
        $output->writeln(sprintf('<info>Success - 0 errors in %d files.</info>', $countFiles));
    }

    private function decorateSourceToken(ErrorInterface $error)
    {
        $result = preg_replace('{\\s+}', ' ', $error->getMessage());
        if ($error->hasHighlightedPart()) {
            $result = str_replace(
                $error->getHighlightedPart(),
                '<error>' . $error->getHighlightedPart() . '</error>',
                $result
            );
        }

        return $result;
    }
}
