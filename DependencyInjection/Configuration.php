<?php

namespace Soyuka\SeedBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('soyuka_seed');

        $rootNode
            ->children()
                ->scalarNode('prefix')->defaultValue('seed')->info('The seed command prefix')->end()
                ->scalarNode('directory')->defaultValue('Seeds')->info('The seeds directory')->end()
            ->end();

        return $treeBuilder;
    }
}
