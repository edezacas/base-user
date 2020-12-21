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
                ->scalarNode('firewall_name')->defaultValue('main')->end()
                ->scalarNode('user_enabled')->defaultFalse()->end()
            ->end()
        ->end();

        return $treeBuilder;
    }

}