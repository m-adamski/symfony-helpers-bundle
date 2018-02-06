<?php

namespace Adamski\Symfony\HelpersBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class HelpersExtension extends Extension {

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter("helpers.mailer.default_sender", $config["mailer"]["default_sender"]);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . "/../Resources/config"));
        $loader->load("services.yaml");
    }
}
