<?php
namespace Rested\Bundle\RestedBundle\Cache;

use Rested\Compiler\CompilerCacheInterface;
use Rested\Compiler\CompilerInterface;
use Rested\FactoryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class CompilerWarmer implements CacheWarmerInterface
{

    /**
     * @var \Rested\Compiler\CompilerInterface
     */
    private $compiler;

    /**
     * @var \Rested\Compiler\CompilerCacheInterface
     */
    private $compilerCache;

    /**
     * @var string
     */
    private $compilerCacheFilePath;

    /**
     * @var \Rested\FactoryInterface
     */
    private $factory;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $fileSystem;

    /**
     * @var string[]
     */
    private $resources;

    public function __construct(
        FactoryInterface $factory,
        CompilerInterface $compiler,
        CompilerCacheInterface $compilerCache,
        Filesystem $fileSystem,
        array $resources,
        $compilerCacheFilePath)
    {
        $this->compiler = $compiler;
        $this->compilerCache = $compilerCache;
        $this->compilerCacheFilePath = $compilerCacheFilePath;
        $this->factory = $factory;
        $this->fileSystem = $fileSystem;
        $this->resources = $resources;
    }

    public function warmUp($cacheDir)
    {
        $resources = $this->resources;

        foreach ($resources as $resource) {
            $this->processResource($resource);
        }

        $data = $this->compilerCache->serialize();

        $this->fileSystem->dumpFile($this->compilerCacheFilePath, '<?php return "'.base64_encode($data).'";');
    }

    public function isOptional()
    {
        return false;
    }

    private function processResource($class)
    {
        $definition = $class::createResourceDefinition($this->factory);
        $compiledDefinition = $this->compiler->compile($definition);

        foreach ($compiledDefinition->getActions() as $action) {
            $this->compilerCache->registerResourceDefinition($action->getRouteName(), $compiledDefinition);
        }
    }
}
