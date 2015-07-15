<?php
namespace Soyuka\SeedBundle\Tests\fixtures;

use Soyuka\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FailSeed extends Seed
{

    protected function configure()
    {
        $this
            ->setSeedName('fail');

        parent::configure();
    }

    public function load(InputInterface $input, OutputInterface $output){ 
        return 1;
    }

    public function unload(InputInterface $input, OutputInterface $output){ 
    }

    public function getOrder() {
        return 0; 
    }
}
