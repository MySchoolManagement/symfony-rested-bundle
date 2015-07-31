<?php
namespace Rested\Bundle\RestedBundle;

use Rested\Definition\Compiled\CompiledResourceDefinitionInterface;
use Rested\Definition\Model;
use Rested\Definition\ResourceDefinition;
use Rested\FactoryInterface;
use Rested\Http\CollectionResponse;
use Rested\Http\Context;
use Rested\Http\InstanceResponse;
use Rested\RequestContext;
use Rested\RestedResourceInterface;
use Rested\RestedServiceInterface;
use Rested\Transforms\DefaultTransform;
use Rested\Transforms\DefaultTransformMapping;
use Rested\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class Factory implements FactoryInterface
{

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    public function __construct(
        ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function createCollectionResponse(CompiledResourceDefinitionInterface $resourceDefinition, $href, array $items = [], $total = null)
    {
        return new CollectionResponse(
            $this->container->get('rested'),
            $this->container->get('rested.url_generator'),
            $resourceDefinition,
            $href,
            $items,
            $total
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createContext(array $parameters, $actionType, $routeName, CompiledResourceDefinitionInterface $resourceDefinition)
    {
        return new Context(
            $parameters,
            $actionType,
            $routeName,
            $resourceDefinition
        );
    }

    /**
     * @return InstanceResponse
     */
    public function createInstanceResponse(CompiledResourceDefinitionInterface $resourceDefinition, $href, array $data, $instance = null)
    {
        return new InstanceResponse(
            $this->container->get('rested'),
            $this->container->get('rested.url_generator'),
            $resourceDefinition,
            $href,
            $data,
            $instance
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createResourceDefinition($name, $controllerClass, $modelClass)
    {
        return new ResourceDefinition($this, $name, $controllerClass, $this->createTransform(), $this->createTransformMapping($modelClass));
    }

    /**
     * {@inheritdoc}
     */
    public function createTransform()
    {
        return new DefaultTransform(
            $this,
            $this->container->get('rested.compiler_cache'),
            $this->container->get('rested.url_generator')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createTransformMapping($modelClass)
    {
        return new DefaultTransformMapping($modelClass);
    }
}
