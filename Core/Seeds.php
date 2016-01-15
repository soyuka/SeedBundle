<?php

namespace Soyuka\SeedBundle\Core;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Soyuka\SeedBundle\Model\AlterationExtension;
use Soyuka\SeedBundle\Model\ConfigurableExtension;

abstract class Seeds extends Command
{
    private $separator = ':';
    private $prefix;

    /**
     * __construct.
     *
     * @param string $prefix - prefix can be changed through configuration
     *                       Note: Prefix is in the contructor because we need it in the "configure()" method
     *                       to build the seed name. The container is not available in the configure state.
     */
    public function __construct($prefix, $separator, array $extensions = [])
    {
        $this->prefix = $prefix;
        $this->separator = $separator;
        $this->extensions = $extensions;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName($this->prefix.$this->separator.$this->method)
            ->setDescription('Load requested seeds')
            ->addOption('break', '-b', InputOption::VALUE_NONE)
            ->addOption('debug', '-d', InputOption::VALUE_NONE);

        $help = <<<EOT

This command loads/unloads a list of seeds

If you want to break on a bad exit code use -b

Want to debug seeds ordering? You can launch a simulation by using the -d option:

  <info>php app/console seeds:load -d</info>
EOT;

        foreach ($this->extensions as $extension) {
            if ($extension instanceof ConfigurableExtension) {
                $extension->configure($this);
                $help .= $extension->getHelp();
            }
        }

        $this->setHelp($help);
    }

    /**
     * This is wrapping every seed in a single command based on $this->method
     * it's also handling arguments and options to launch multiple seeds.
     * {@inheritdoc}
     *
     * @see LoadSeedsCommand
     * @see UnloadSeedsCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $break = $input->getOption('break');
        $debug = $input->getOption('debug');

        $commands = $this->getSeedsCommands();

        foreach ($this->extensions as $extension) {
            if ($extension instanceof AlterationExtension) {
                $extension->apply($commands, $input);
            }
        }

        $l = count($commands);

        //No seeds? Stop.
        if ($l == 0) {
            $output->writeln('<info>No seeds</info>');

            return 1;
        }

        foreach ($this->extensions as $extension) {
            if (!($extension instanceof AlterationExtension)) {
                $extension->apply($commands, $input);
            }
        }

        //Prepare arguments
        $arguments = new ArrayInput(['method' => $this->method]);
        $returnCode = 0;

        //Loop and execute every seed by printing tstart/tend
        for ($i = 0; $i < $l; ++$i) {
            $output->writeln(sprintf(
                '<info>[%d] Starting %s</info>',
                $commands[$i]->getOrder(), $commands[$i]->getName()
            ));

            $tstart = microtime(true);

            if ($debug) {
                $code = 0;
            } else {
                $code = $commands[$i]->run($arguments, $output);
            }

            $time = microtime(true) - $tstart;

            if ($code === 0) {
                $output->writeln(sprintf(
                    '<info>[%d] Seed %s done (+%d seconds)</info>',
                    $commands[$i]->getOrder(), $commands[$i]->getName(), $time
                ));

                continue;
            }

            $output->writeln(sprintf(
                '<error>[%d] Seed %s failed (+%d seconds)</error>',
                $commands[$i]->getOrder(), $commands[$i]->getName(), $time
            ));

            if ($break === true) {
                $returnCode = 1;
                break;
            }
        }

        return $returnCode;
    }

    /**
     * Get seeds from app commands.
     *
     * @param array $seeds Input Option
     *
     * @return array commands
     */
    private function getSeedsCommands()
    {
        $app = $this->getApplication();
        $commands = [];

        //Get every command, if no seeds argument we take all available seeds
        foreach ($app->all() as $command) {
            //is this a seed?
            if ($command instanceof Seed) {
                $commands[] = $command;
                continue;
            }
        }

        return $commands;
    }
}
