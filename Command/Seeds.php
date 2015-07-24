<?php

namespace Soyuka\SeedBundle\Command;

use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class Seeds extends Command
{
    
    /**
     * __construct
     *
     * @param string $prefix - prefix can be changed through configuration
     * Note: Prefix is in the contructor because we need it in the "configure()" method
     * to build the seed name. The container is not available in the configure state.
     */
    public function __construct($prefix) 
    {
        $this->prefix = $prefix;
        parent::__construct(); 
    }

    /**
     * @inheritdoc
     */
    protected function configure() 
    {
        $this->setName($this->prefix . ':' . $this->method) 
            ->setDescription('Load requested seeds')
            ->addArgument('seeds', InputArgument::IS_ARRAY | InputArgument::OPTIONAL)
            ->addOption('break', '-b', InputOption::VALUE_NONE)
            ->addOption('debug', '-d', InputOption::VALUE_NONE)
            ->addOption('skip', '', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY);

        $this->setHelp(<<<EOT

This command loads/unloads a list of seeds

You can specify seeds:

  <info>php app/console seeds:load Country Town Geography</info>

Without arguments, all seeds are loaded :

  <info>php app/console seeds:load</info>

You can skip some seeds:

  <info>php app/console seeds:load --skip=Country --skip=Town</info>

If you want to break on a bad exit code use -b

Want to debug seeds ordering? You can launch a simulation by using the -d option:

  <info>php app/console seeds:load -d</info>

EOT
            );
    }

    /**
     * This is wrapping every seed in a single command based on $this->method
     * it's also handling arguments and options to launch multiple seeds
     * @inheritdoc
     * @see LoadSeedsCommand
     * @see UnloadSeedsCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output) 
    { 

        $seeds = $input->getArgument('seeds');
        $break = $input->getOption('break');
        $skip = $input->getOption('skip');
        $debug = $input->getOption('debug');

        if($skip) {
            $skip = is_array($skip) ?: [$skip];

            $skip = array_map(function($v) {
                return strtolower($v);
            }, $skip);
        }

        //Lowercase seeds names
        if($seeds) {
            $seeds = array_map(function($v) {
                return strtolower($v);
            }, $seeds);
        }

        $commands = [];
        $app = $this->getApplication();

        //Get every command, if no seeds argument we take all available seeds
        foreach($app->all() as $command) {
            //is this a seed?
            if(method_exists($command, 'getSeedName')) {

                $name = $command->getSeedName();

                if(!$seeds) {
                    $commands[] = $command;
                } else if(in_array($name, $seeds)) {
                    $commands[] = $command; 
                }
            }
        }

        $l = count($commands);

        //No seeds? Stop.
        if($l == 0) {
           $output->writeln('<info>No seeds</info>');
           return 0; 
        }

        //Filter seeds to be skipped
        if($skip) {
            //array_filter keeps keys
            $commands = array_values(array_filter($commands, function($command) use ($skip) {
                return !in_array($command->getSeedName(), $skip);
            }));

            $l = count($commands);
        }

        //Sort through getOrder
        usort($commands, function($a, $b) {
            return $a->getOrder() - $b->getOrder();
        });

        //Prepare arguments
        $arguments = new ArrayInput(['method' => $this->method]);
        $returnCode = 0;

        //Loop and execute every seed by printing tstart/tend
        for($i = 0; $i < $l; $i++) {

            $output->writeln(sprintf(
                '<info>[%d] Starting %s</info>',
                $commands[$i]->getOrder(), $commands[$i]->getName()
            ));

            $tstart = microtime(true);
            
            if($debug) {
                $code = 0;
            } else {
                $code = $commands[$i]->run($arguments, $output);
            }

            $time = microtime(true) - $tstart;

            if($code === 0) {
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

            if($break === true) {
                $returnCode = 1; 
                break; 
            }
        }

        return $returnCode;
    }
}
