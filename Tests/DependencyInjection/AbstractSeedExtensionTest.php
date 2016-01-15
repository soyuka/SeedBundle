<?php

namespace Soyuka\SeedBundle\Tests\DependencyInjection;

//http://egeloen.fr/2013/12/08/unit-test-your-symfony2-bundle-di-like-a-boss/

use Soyuka\SeedBundle\DependencyInjection\SoyukaSeedExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractSeedExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;
    private $container;

    protected function setUp()
    {
        $this->extension = new SoyukaSeedExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    abstract protected function loadConfiguration(ContainerInterface $container, $resource);

    public function testWithoutConfiguration()
    {
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        $this->assertTrue($this->container->hasParameter('seed.prefix'));
        $this->assertTrue($this->container->hasParameter('seed.directory'));
        $this->assertEquals('seed', $this->container->getParameter('seed.prefix'));
        $this->assertEquals('Seeds', $this->container->getParameter('seed.directory'));
    }

    public function testDefaultConfiguration()
    {
        $this->loadConfiguration($this->container, 'default');
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        $this->assertTrue($this->container->hasParameter('seed.prefix'));
        $this->assertTrue($this->container->hasParameter('seed.directory'));
        $this->assertEquals('seed', $this->container->getParameter('seed.prefix'));
        $this->assertEquals('Seeds', $this->container->getParameter('seed.directory'));
    }

    public function testDirectoryConfiguration()
    {
        $this->loadConfiguration($this->container, 'directory');
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        $this->assertTrue($this->container->hasParameter('seed.prefix'));
        $this->assertTrue($this->container->hasParameter('seed.directory'));
        $this->assertEquals('seed', $this->container->getParameter('seed.prefix'));
        $this->assertNotEquals('Seeds', $this->container->getParameter('seed.directory'));
    }

    public function testPrefixConfiguration()
    {
        $this->loadConfiguration($this->container, 'prefix');
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();

        $this->assertTrue($this->container->hasParameter('seed.prefix'));
        $this->assertTrue($this->container->hasParameter('seed.directory'));
        $this->assertNotEquals('seed', $this->container->getParameter('seed.prefix'));
        $this->assertEquals('Seeds', $this->container->getParameter('seed.directory'));
    }
}
