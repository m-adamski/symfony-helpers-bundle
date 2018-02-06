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
        $treeBuilder
            ->root("mailer")
            ->children()
            ->scalarNode("default_sender")
            ->defaultValue("noreply@example.com")
            ->end()
            ->end();

        return $treeBuilder;
    }
}
