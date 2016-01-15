<?php

namespace Soyuka\SeedBundle\Extensions;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Soyuka\SeedBundle\Model\SeedExtension;
use Soyuka\SeedBundle\Model\AlterationExtension;
use Soyuka\SeedBundle\Model\ConfigurableExtension;

class Skip implements SeedExtension, AlterationExtension, ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    public function apply(array &$commands, InputInterface $input)
    {
        $skip = $input->getOption('skip');

        if (!$skip) {
            return;
        }

        $skip = is_array($skip) ?: [$skip];

        $skip = array_map(function ($v) {
            return strtolower($v);
        }, $skip);

        //array_filter keeps keys
        $commands = array_values(array_filter($commands, function ($command) use ($skip) {
            return !in_array($command->getSeedName(), $skip);
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Command $command)
    {
        $command->addOption('skip', '', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY);
    }

    /**
     * {@inheritdoc}
     */
    public function getHelp()
    {
        return <<<EOT
   
You can skip some seeds:

  <info>php app/console seeds:load --skip=Country --skip=Town</info>
EOT;
    }
}
