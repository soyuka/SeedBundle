<?php

namespace Soyuka\SeedBundle\Tests\fixtures\Seeds;

use Soyuka\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Soyuka\SeedBundle\Model\SeedInterface;
use Soyuka\SeedBundle\Model\SeedOrderInterface;

class TownSeed extends Seed implements SeedInterface, SeedOrderInterface
{
    protected function configure()
    {
        $this
            ->setSeedName('town');

        parent::configure();
    }

    public function load(InputInterface $input, OutputInterface $output)
    {
        $this->disableDoctrineLogging();
        $output->writeln('Load town');
    }

    public function unload(InputInterface $input, OutputInterface $output)
    {
        $this->disableDoctrineLogging();
        $output->writeln('Unload town');
    }

    public function getOrder()
    {
        return 2;
    }
}
