<?php
namespace Soyuka\SeedBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Soyuka\SeedBundle\Command\LoadSeedsCommand;
use Soyuka\SeedBundle\Command\UnloadSeedsCommand;
use Soyuka\SeedBundle\SeedLoader;
use Soyuka\SeedBundle\Tests\fixtures\BadSeed;
use Soyuka\SeedBundle\Tests\fixtures\FailSeed;

class SeedsCommandTest extends KernelTestCase
{
    protected function setUp() 
    {
        $this->container = new ContainerBuilder();
        $this->container->setParameter('seed.directory', 'Tests/fixtures/Seeds');
        $this->container->setParameter('seed.prefix', 'testseeds');
        $this->container->compile();

        static::$kernel = static::createKernel();
        static::$kernel->boot();

        //This is the same configuration as the one from the bundle
        $prefix = $this->container->getParameter('seed.prefix');
        $this->application = new Application(static::$kernel);

        //Prefix is given to the Seeds (see Soyuka\SeedBundle\Command\Seeds)
        //because when configure is called, container is not ready
        //Seeds does not extend ContainerAware anyway
        $this->application->add(new LoadSeedsCommand($prefix));
        $this->application->add(new UnloadSeedsCommand($prefix));

        $this->assertTrue($this->application->has('testseeds:load'));
        $this->assertTrue($this->application->has('testseeds:unload'));
    }

    protected function seedsLoader() {
        $seeds = new SeedLoader($this->container->getParameter('seed.prefix'));
        $seeds->setContainer($this->container);
        $seeds->load($this->application);

        $this->assertTrue($this->application->has('testseeds:country'));
        $this->assertTrue($this->application->has('testseeds:town'));
    }

    public function testNoSeeds() 
    {
        $application = new Application(static::$kernel);
        $application->add(new LoadSeedsCommand('testseeds'));

        $command = $application->find('testseeds:load');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/No seeds/', $commandTester->getDisplay());
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }

    public function testLoadSeeds() 
    {
        $this->seedsLoader();

        $command = $this->application->find('testseeds:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Load country/', $output);
        $this->assertRegExp('/Load town/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }

    public function testUnloadSeeds() 
    {
        $this->seedsLoader();

        $command = $this->application->find('testseeds:unload');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Unload country/', $output);
        $this->assertRegExp('/Unload town/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }

    public function testNamedSeeds() {

        $this->seedsLoader();

        $command = $this->application->find('testseeds:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), 'seeds' => ['Country']));

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Load country/', $output);
        $this->assertNotRegExp('/Load town/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }

    public function testSkipSeeds() {

        $this->seedsLoader();

        $command = $this->application->find('testseeds:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--skip' => 'Town'));

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Load country/', $output);
        $this->assertNotRegExp('/Load town/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBadSeed() {
        $application = new Application(static::$kernel);
        $application->add(new BadSeed($this->container->getParameter('seed.prefix')));
    }

    public function testBreakSeed() {
    
        $this->seedsLoader();

        $this->application->add(new FailSeed($this->container->getParameter('seed.prefix')));

        $command = $this->application->find('testseeds:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '-b' => true));

        $output = $commandTester->getDisplay();

        $this->assertNotRegExp('/Load country/', $output);
        $this->assertNotRegExp('/Load town/', $output);
        $this->assertRegExp('/testseeds:fail failed/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 1);
    }

    public function testDebugSeed() {
        $this->seedsLoader();
        $command = $this->application->find('testseeds:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '-d' => true));

        $output = $commandTester->getDisplay();

        var_dump($output);

        $this->assertNotRegExp('/Load country/', $output);
        $this->assertNotRegExp('/Load town/', $output);
        $this->assertRegExp('/Starting testseeds:country/', $output);
        $this->assertRegExp('/Starting testseeds:town/', $output);
        $this->assertEquals($commandTester->getStatusCode(), 0);
    }
}
