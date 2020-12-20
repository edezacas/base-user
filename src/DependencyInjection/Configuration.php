<?php


namespace DigitalAscetic\BaseUserBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('digital_ascetic_base_user');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('user_class')->isRequired()->end()
            ->end()
        ->end();

        return $treeBuilder;
    }

}