<?php

namespace Soyuka\SeedBundle\Tests\fixtures;

use Soyuka\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BadSeed extends Seed
{
    protected function configure()
    {
        $this
            ->setSeedName('');
        parent::configure();
    }

    public function load(InputInterface $input, OutputInterface $output)
    {
    }

    public function unload(InputInterface $input, OutputInterface $output)
    {
    }

    public function getOrder()
    {
        return 1;
    }
}
