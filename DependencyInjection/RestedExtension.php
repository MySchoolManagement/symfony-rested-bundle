<?php
namespace Rested\Bundle\RestedBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RestedExtension extends Extension
{

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $resources = [];

        if (array_key_exists('prefix', $config) === true) {
            $container->setParameter('rested.prefix', $config['prefix']);
        }


        if (array_key_exists('resources', $config) === true) {
            $resources = $config['resources'];
        }

        $container->setParameter('rested.resources', $resources);
    }
}
