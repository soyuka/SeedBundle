<?php
namespace Soyuka\SeedBundle\Tests\fixtures\Seeds;

use Soyuka\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GlobSeed extends Seed
{
    protected function configure()
    {
        $this
            ->setSeedName('foo:bar');

        parent::configure();
    }

    public function load(InputInterface $input, OutputInterface $output){ 
        $output->writeln('Load foo:bar');
    }

    public function unload(InputInterface $input, OutputInterface $output){ 
        $output->writeln('Unload foo:bar');
    }
}
