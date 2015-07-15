<?php
namespace Soyuka\SeedBundle;

use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerAware;

class SeedLoader extends ContainerAware
{
    private $prefix;

    public function __construct($prefix) {
        $this->prefix = $prefix;
    }

    public function load(Application $application) {

        $in = $this->container->getParameter('seed.directory');

        foreach($application->getKernel()->getBundles() as $bundle) {

            if(!is_dir($dir = $bundle->getPath() . '/' . $in)) {
                continue; 
            }

            $finder = new Finder();
            $finder->files()->name('*Seed.php')->in($dir);

            $prefix = $bundle->getNameSpace(). '\\' . strtr($in, '/', '\\');

            foreach($finder as $file) {
                $ns = $prefix;

                if ($relativePath = $file->getRelativePath()) {
                    $ns .= '\\'.strtr($relativePath, '/', '\\');
                }

                $class = $ns.'\\'.$file->getBasename('.php');

                if ($this->container) {
                    $alias = 'console.seed.'.strtolower(str_replace('\\', '_', $class));
                    if ($this->container->has($alias)) {
                        continue;
                    }
                }

                $r = new \ReflectionClass($class);
                if ($r->isSubclassOf('Soyuka\\SeedBundle\\Command\\Seed') && !$r->isAbstract() && $r->hasMethod('load')) {
                    $application->add($r->newInstance($this->prefix));
                }
            }

        }
    }
}
