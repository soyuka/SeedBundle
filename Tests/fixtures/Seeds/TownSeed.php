<?php
namespace Soyuka\SeedBundle\Tests\fixtures\Seeds;

use Soyuka\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TownSeed extends Seed
{

    protected function configure()
    {
        $this
            ->setSeedName('town');

        parent::configure();
    }

    public function load(InputInterface $input, OutputInterface $output){ 
        $this->disableDoctrineLogging();
        $output->writeln('Load town');
    }

    public function unload(InputInterface $input, OutputInterface $output){ 
        $this->disableDoctrineLogging();
        $output->writeln('Unload town');
    }

    public function getOrder() {
        return 2; 
    }
}
