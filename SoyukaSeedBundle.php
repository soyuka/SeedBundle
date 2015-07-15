<?php

namespace Soyuka\SeedBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Console\Application;
use Soyuka\SeedBundle\Command\LoadSeedsCommand;
use Soyuka\SeedBundle\Command\UnloadSeedsCommand;

class SoyukaSeedBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $this->container = $container;

        parent::build($container);

    }

    public function registerCommands(Application $application) 
    {

        $prefix = $this->container->getParameter('seed.prefix');

        $application->add(new LoadSeedsCommand($prefix));
        $application->add(new UnloadSeedsCommand($prefix));

        $seeds = $this->container->get('seed.loader');
        $seeds->load($application);
    }
}
