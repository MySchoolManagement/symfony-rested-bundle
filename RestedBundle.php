<?php
namespace Rested\Bundle\RestedBundle;

use Rested\Bundle\RestedBundle\DependencyInjection\Compiler\ProcessResourcePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RestedBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ProcessResourcePass());
    }
}