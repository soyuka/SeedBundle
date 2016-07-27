<?php

namespace Soyuka\SeedBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Soyuka\SeedBundle\Tests\fixtures\BadSeed;
use Soyuka\SeedBundle\Tests\fixtures\FailSeed;

class SeedsCommandTest extends KernelTestCase
{
    protected function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
        $this->application = new Application(static::$kernel);
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

    public function testNoSeeds()
    {
        $application = new Application(static::$kernel);
        $application->add($this->container->get('seed.load_seeds_command'));

        $command = $application->find('testseeds:load');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '--skip' => ['foo:bar', 'country', 'town', 'street', 'postcode']]);

        $this->assertRegExp('/No seeds/', $commandTester->getDisplay());
        $this->assertEquals($commandTester->getStatusCode(), 1);
    }

    public function testLoadSeeds()
    {
        $this->seedsLoader();

        $command = $this->application->find('testseeds:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Load country/', $output);
        $this->assertRegExp('/Load town/', $output);
        $this->assertRegExp('/Load street/', $output);
        $this->assertRegExp('/Load postcode/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);

    }

    public function testUnloadSeeds()
    {
        $this->seedsLoader();

        $command = $this->application->find('testseeds:unload');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Unload country/', $output);
        $this->assertRegExp('/Unload town/', $output);
        $this->assertRegExp('/Unload street/', $output);
        $this->assertRegExp('/Unload postcode/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }

    public function testNamedSeeds()
    {
        $this->seedsLoader();

        $command = $this->application->find('testseeds:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'seeds' => ['Country']]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Load country/', $output);
        $this->assertNotRegExp('/Load town/', $output);
        $this->assertNotRegExp('/Load street/', $output);
        $this->assertNotRegExp('/Load postcode/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }

    public function testGlobSeeds()
    {
        $this->seedsLoader();

        $command = $this->application->find('testseeds:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'seeds' => ['foo:*']]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Load foo:bar/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }

    public function testSkipSeeds()
    {
        $this->seedsLoader();

        $command = $this->application->find('testseeds:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '--skip' => 'Town']);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Load country/', $output);
        $this->assertNotRegExp('/Load town/', $output);
        $this->assertRegExp('/Load street/', $output);
        $this->assertRegExp('/Load postcode/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBadSeed()
    {
        $application = new Application(static::$kernel);
        $application->add(new BadSeed($this->container->getParameter('seed.prefix')));
    }

    public function testBreakSeed()
    {
        $this->seedsLoader();

        $this->application->add(new FailSeed($this->container->getParameter('seed.prefix')));

        $command = $this->application->find('testseeds:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '-b' => true]);

        $output = $commandTester->getDisplay();

        $this->assertNotRegExp('/Load country/', $output);
        $this->assertNotRegExp('/Load town/', $output);
        $this->assertNotRegExp('/Load street/', $output);
        $this->assertNotRegExp('/Load postcode/', $output);
        $this->assertRegExp('/testseeds:fail failed/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 1);
    }

    public function testDebugSeed()
    {
        $this->seedsLoader();
        $command = $this->application->find('testseeds:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), '-d' => true]);

        $output = $commandTester->getDisplay();

        $this->assertNotRegExp('/Load country/', $output);
        $this->assertNotRegExp('/Load town/', $output);
        $this->assertNotRegExp('/Load street/', $output);
        $this->assertNotRegExp('/Load postcode/', $output);
        $this->assertRegExp('/Starting testseeds:country/', $output);
        $this->assertRegExp('/Starting testseeds:town/', $output);
        $this->assertRegExp('/Starting testseeds:street/', $output);
        $this->assertRegExp('/Starting testseeds:postcode/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }

    public function testFromSeed()
    {
      $this->seedsLoader();

      $command = $this->application->find('testseeds:load');

      $commandTester = new CommandTester($command);
      $commandTester->execute(['command' => $command->getName(), '-f' => 'testseeds:street']);

      $output = $commandTester->getDisplay();

      $this->assertNotRegExp('/Load country/', $output);
      $this->assertNotRegExp('/Load town/', $output);
      $this->assertRegExp('/Load street/', $output);
      $this->assertRegExp('/Load postcode/', $output);
      $this->assertEquals($commandTester->getStatusCode(), 0);

    }

}
