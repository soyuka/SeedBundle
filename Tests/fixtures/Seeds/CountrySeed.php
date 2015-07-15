<?php
namespace Soyuka\SeedBundle\Tests\fixtures\Seeds;

use Soyuka\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CountrySeed extends Seed
{

    protected function configure()
    {
        $this
            ->setSeedName('country');

        parent::configure();
    }

    public function load(InputInterface $input, OutputInterface $output){ 
        $this->disableDoctrineLogging();
        $output->writeln('Load country');
    }

    public function unload(InputInterface $input, OutputInterface $output){ 
        $this->disableDoctrineLogging();
        $output->writeln('Unload country');
    }

    public function getOrder() {
        return 1; 
    }
}
