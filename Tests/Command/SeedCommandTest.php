<?php

namespace Soyuka\SeedBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SeedCommandTest extends KernelTestCase
{
    protected function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
        $this->application = new Application(static::$kernel);
        $this->seedsLoader();
    }

    protected function seedsLoader()
    {
        $seeds = $this->container->get('seed.loader');
        $seeds->load($this->application);

        $this->assertTrue($this->application->has('testseeds:country'));
        $this->assertTrue($this->application->has('testseeds:town'));
        $this->assertTrue($this->application->has('testseeds:street'));
        $this->assertTrue($this->application->has('testseeds:postcode'));
    }

    public function testIsValidSeed()
    {
        $c = $this->application->get('testseeds:country');

        $this->assertTrue(method_exists($c, 'disableDoctrineLogging'));
        $this->assertObjectHasAttribute('doctrine', $c);
        $this->assertObjectHasAttribute('manager', $c);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Method should be one of: load, unload
     */
    public function testSeedCommand()
    {
        $command = $this->application->find('testseeds:country');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'method' => 'nonexistant']);

        $this->assertEquals($commandTester->getStatusCode(), 1);
    }
}
