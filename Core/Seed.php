<?php

namespace Soyuka\SeedBundle\Core;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;

abstract class Seed extends ContainerAwareCommand
{
    /** @var string **/
    private $prefix;
    /** @var string **/
    private $seedName;

    /** @var Registry **/
    protected $doctrine;
    /** @var EntityManager **/
    protected $manager;
    /** @var Container **/
    protected $container;

    /**
     * __construct.
     *
     * @param string $prefix - prefix can be changed through configuration
     *                       Note: Prefix is in the contructor because we need it in the "configure()" method
     *                       to build the seed name. The container is not available in the configure state.
     */
    public function __construct($prefix, $separator = ':')
    {
        $this->prefix = $prefix;
        $this->separator = $separator;

        parent::__construct();
    }

    /**
     * setSeedName
     * Protected because is should only be called by the children class extending
     * this one.
     *
     * @param string $name
     */
    protected function setSeedName($name)
    {
        $this->seedName = $name;

        return $this;
    }

    /**
     * getSeedName
     * Public because it can be called by LoadSeedsCommand or UnloadSeedsCommand.
     *
     * @return string
     */
    public function getSeedName(): string
    {
        return $this->seedName;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $name = $this->getSeedName();

        if (!$name) {
            throw new \InvalidArgumentException('Please configure the command '.get_called_class().' with a seed name');
        }

        $this->setName($this->prefix.$this->separator.$this->seedName)
            ->addArgument('method', InputArgument::OPTIONAL);
    }

    /**
     * execute
     * Execute the seed method according to the method argument (load/unload).
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return this
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $method = $input->getArgument('method') ?: 'load';

        if (!in_array($method, ['load', 'unload'])) {
            throw new \InvalidArgumentException('Method should be one of: load, unload');
        }

        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException("Method '$method' does not exist in the command ".get_called_class());
        }

        $this->container = $this->getContainer();
        $this->doctrine = $this->container->get('doctrine');
        $this->manager = $this->doctrine->getManager();

        return $this->$method($input, $output);
    }

    /**
     * disableDoctrineLogging
     * Shortcut to disable doctrine logging, usefull when loading big seeds to
     * avoir memory leaks.
     *
     * @return this
     */
    protected function disableDoctrineLogging()
    {
        $this->getContainer()->get('doctrine')
            ->getConnection()
            ->getConfiguration()
            ->setSQLLogger(null);

        return $this;
    }
}
