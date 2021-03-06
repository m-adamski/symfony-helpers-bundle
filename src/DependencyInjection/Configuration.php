<?php

namespace Adamski\Symfony\HelpersBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root("helpers");

        $rootNode
            ->children()
                ->arrayNode("mailer")
                    ->children()
                        ->scalarNode("sender_address")->defaultValue("noreply@example.com")->end()
                        ->scalarNode("sender_name")->defaultValue("no-reply")->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
