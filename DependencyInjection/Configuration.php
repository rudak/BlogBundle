<?php

namespace Rudak\BlogBundle\DependencyInjection;

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
        $rootNode    = $treeBuilder->root('rudak_blog');

        $rootNode
            ->children()
            ->booleanNode('twitter_publication')->defaultFalse()->end()
            ->scalarNode('consumer_key')->defaultNull()->end()
            ->scalarNode('consumer_secret')->defaultNull()->end()
            ->scalarNode('access_token')->defaultNull()->end()
            ->scalarNode('access_token_secret')->defaultNull()->end()
            ->end();

        return $treeBuilder;
    }
}
