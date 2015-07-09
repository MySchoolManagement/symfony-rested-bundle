<?php
namespace Rested\Bundle\RestedBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class ProcessResourcePass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $routeLoader = $container->getDefinition('rested');
        $resources = $container->getParameter('rested.resources');

        foreach ($resources as $class) {
            $routeLoader->addMethodCall('addResource', [$class]);
        }
    }
}
