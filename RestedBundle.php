<?php
namespace Rested\Bundle\RestedBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class RestedBundle extends Bundle
{

    public function boot()
    {
        $container = $this->container;
        $factory = $container->get('rested.factory');
        $urlGenerator = $container->get('rested.url_generator');
        $compilerCacheFile = $container->getParameter('rested.compiler_cache_file');

        $data = base64_decode(require($compilerCacheFile));

        $compilerCache = $container->get('rested.compiler_cache');
        $compilerCache->setServices($factory, $urlGenerator);
        $compilerCache->hydrate($data);
    }
}
