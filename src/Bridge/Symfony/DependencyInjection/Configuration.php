<?php

namespace KunicMarko\JMSMessengerAdapter\Bridge\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('jms_messenger');
        $rootNode = $treeBuilder->getRootNode();

        \assert($rootNode instanceof ArrayNodeDefinition);

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->enumNode('format')
                    ->defaultValue("json")
                    ->values(["json", "xml", "yaml"])
                ->end()
                ->scalarNode('serializer_id')
                    ->defaultValue('messenger.transport.jms_serializer')
                    ->cannotBeEmpty()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
