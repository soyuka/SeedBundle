<?php

namespace Soyuka\SeedBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Console\Application;
use Soyuka\SeedBundle\DependencyInjection\Compiler\ExtensionPass;

class SoyukaSeedBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $this->container = $container;

        parent::build($container);

        $container->addCompilerPass(new ExtensionPass());
    }

    public function registerCommands(Application $application)
    {
        $seeds = $this->container->get('seed.loader');
        $seeds->loadSeeds($application);
    }
}
