<?php
namespace Rested\Bundle\RestedBundle\Routing;

use Rested\Compiler\CompilerInterface;
use Rested\FactoryInterface;
use Rested\RestedServiceInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteLoader extends Loader
{

    /**
     * @var \Rested\Compiler\CompilerInterface
     */
    private $compiler;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Rested\FactoryInterface
     */
    private $factory;

    /**
     * @var \Rested\RestedServiceInterface
     */
    private $rested;

    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    private $routes;

    public function __construct(RestedServiceInterface $rested, FactoryInterface $factory, CompilerInterface $compiler)
    {
        $this->compiler = $compiler;
        $this->factory = $factory;
        $this->rested = $rested;
        $this->routes = new RouteCollection();
    }

    public function load($resource, $type = null)
    {
        foreach ($this->rested->getResources() as $resource) {
            $this->processResource($resource);
        }

        return $this->routes;
    }

    public function processResource($class)
    {
        $def = $class::createResourceDefinition($this->factory);
        $compiledDefinition = $compiler->compile($definition);

        foreach ($compiledDefinition->getActions() as $action) {
            $href = $action->getEndpointUrl(false);
            $routeName = $action->getRouteName();
            $controller = sprintf('%s::%s', $class, 'handle');
            $defaults = [
                '_controller' => $controller,
                '_format' => 'json',
                '_rested' => [
                    'action' => $action->getType(),
                    'controller' => $action->getCallable(),
                    'route_name' => $routeName,
                ],
            ];

            $route = new Route($href, $defaults, [], [], '', [], $action->getMethod());

            // add constraints and validators to the cache
            foreach ($action->getTokens() as $token) {
                if ($token->acceptAnyValue() === false) {
                    $route->where($token->getName(), Parameter::getValidatorPattern($token->getDataType()));
                }
            }

            $this->routes->add($routeName, $route);
            $cache->registerResourceDefinition($routeName, $compiledDefinition);
        }
    }

    public function supports($resource, $type = null)
    {
        return $type === 'rested';
    }
}
