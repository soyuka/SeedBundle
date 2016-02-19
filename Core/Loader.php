<?php

namespace Soyuka\SeedBundle\Core;

use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Loader extends Container
{
    use ContainerAwareTrait;
    private $prefix;
    private $separator;

    public function __construct($prefix, $separator)
    {
        $this->prefix = $prefix;
        $this->separator = $separator;
    }

    public function load(Application $application)
    {
        $in = $this->container->getParameter('seed.directory');

        //add seed:load and seed:unload commands
        $application->add($this->container->get('seed.load_seeds_command'));
        $application->add($this->container->get('seed.unload_seeds_command'));

        //Go through bundles and add *Seeds available in seed.directory
        foreach ($application->getKernel()->getBundles() as $bundle) {
            if (!is_dir($dir = sprintf('%s/%s', $bundle->getPath(), $in))) {
                continue;
            }

            $finder = new Finder();
            $finder->files()->name('*Seed.php')->in($dir);

            $prefix = $bundle->getNameSpace().'\\'.strtr($in, '/', '\\');

            foreach ($finder as $file) {
                $ns = $prefix;

                if ($relativePath = $file->getRelativePath()) {
                    $ns .= '\\'.strtr($relativePath, '/', '\\');
                }

                $class = $ns.'\\'.$file->getBasename('.php');

                $alias = 'seed.command.'.strtolower(str_replace('\\', '_', $class));
                if ($this->container->has($alias)) {
                    continue;
                }

                $r = new \ReflectionClass($class);
                if ($r->isSubclassOf('Soyuka\\SeedBundle\\Command\\Seed') && !$r->isAbstract() && ($r->hasMethod('load') || $r->hasMethod('unload'))) {
                    $application->add(
                        $r->newInstanceArgs([$this->prefix, $this->separator])
                    );
                }
            }
        }
    }
}
