<?php
namespace Rested\Bundle\RestedBundle;

use Rested\Definition\Model;
use Rested\Definition\ResourceDefinition;
use Rested\FactoryInterface;
use Rested\Http\CollectionResponse;
use Rested\Http\InstanceResponse;
use Rested\RequestContext;
use Rested\RestedResourceInterface;
use Rested\RestedServiceInterface;
use Rested\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class Factory implements FactoryInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RequestContext[]
     */
    private $contexts = [];

    /**
     * @var RestedServiceInterface
     */
    private $restedService;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(ContainerInterface $container, RestedServiceInterface $restedService, UrlGeneratorInterface $urlGenerator)
    {
        $this->container = $container;
        $this->restedService = $restedService;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function createBasicController($class)
    {
        $controller = new $class($this);
        $controller->setContainer($this->container);

        return $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function createBasicControllerFromRouteName($routeName)
    {
        throw new \Exception();
    }

    /**
     * {@inheritdoc}
     */
    public function createCollectionResponse(RestedResourceInterface $resource, array $items = [], $total = 0)
    {
        return new CollectionResponse($this, $this->urlGenerator, $resource, $items, $total);
    }

    /**
     * {@inheritdoc}
     */
    public function createDefinition($name, RestedResourceInterface $resource, $class)
    {
        return new ResourceDefinition($name, $resource, $this->restedService, $class);
    }

    /**
     * {@inheritdoc}
     */
    public function createInstanceResponse(RestedResourceInterface $resource, $href, $item)
    {
        return new InstanceResponse($this, $this->urlGenerator, $resource, $href, $item);
    }

    /**
     * {@inheritdoc}
     */
    public function createModel(ResourceDefinition $resourceDefinition, $class)
    {
        return new Model($resourceDefinition, $class);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveContextForRequest(Request $request, RestedResourceInterface $resource)
    {
        foreach ($this->contexts as $item) {
            if ($item['request'] === $request) {
                return $item['context'];
            }
        }

        $item = [
            'context' => new RequestContext($request, $resource),
            'request' => $request,
        ];

        $this->contexts[] = $item;

        return $item['context'];
    }
}
