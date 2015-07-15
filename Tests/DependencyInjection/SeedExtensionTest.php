<?php
namespace Soyuka\SeedBundle\Tests\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SeedExtensionTest extends AbstractSeedExtensionTest
{
    protected function loadConfiguration(ContainerInterface $container, $resource) {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../fixtures/Resources/'));
        $loader->load($resource.'.yml');
    }
}
