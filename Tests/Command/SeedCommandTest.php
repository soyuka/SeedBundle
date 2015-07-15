<?php
namespace Soyuka\SeedBundle\Tests\Command;

use Soyuka\SeedBundle\Tests\fixtures\Seeds\CountrySeed;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SeedCommandTest extends KernelTestCase
{

    public function testIsValidSeed()
    {
        $this->container = new ContainerBuilder();
        $this->container->setParameter('seed.directory', 'Tests/fixtures/Seeds');
        $this->container->setParameter('seed.prefix', 'testseeds');
        $this->container->compile();

        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $prefix = $this->container->getParameter('seed.prefix');
        $this->application = new Application(static::$kernel);

        $this->application->add(new CountrySeed($prefix));

        $c = $this->application->get('testseeds:country');

        $this->assertTrue(method_exists($c, 'disableDoctrineLogging'));
        $this->assertObjectHasAttribute('doctrine', $c);
        $this->assertObjectHasAttribute('manager', $c);
    }
    

}
