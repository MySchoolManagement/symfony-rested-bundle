<?php
namespace Rested\Bundle\RestedBundle\Routing;

use Rested\Compiler\CompilerCacheInterface;
use Rested\Definition\Compiled\CompiledActionDefinitionInterface;
use Rested\Definition\Parameter;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteLoader extends Loader
{

    /**
     * @var \Rested\Compiler\CompilerCacheInterface
     */
    private $compilerCache;

    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    private $routes;

    public function __construct(
        CompilerCacheInterface $compilerCache)
    {
        $this->compilerCache = $compilerCache;
        $this->routes = new RouteCollection();
    }

    public function load($resource, $type = null)
    {
        foreach ($this->compilerCache->getResourceDefinitions() as $resourceDefinition) {
            foreach ($resourceDefinition->getActions() as $action) {
                $this->processAction($action);
            }
        }


        return $this->routes;
    }

    public function supports($resource, $type = null)
    {
        return $type === 'rested';
    }

    private function processAction(CompiledActionDefinitionInterface $action)
    {
        $href = $action->getEndpointUrl();
        $routeName = $action->getRouteName();
        $controller = sprintf('%s::%s', $action->getResourceDefinition()->getControllerClass(), 'handle');
        $defaults = [
            '_controller' => $controller,
            '_format' => 'json',
            '_rested' => [
                'action' => $action->getType(),
                'controller' => $action->getControllerName(),
                'route_name' => $routeName,
            ],
        ];

        // add constraints and validators to the cache
        $requirements = [];

        foreach ($action->getTokens() as $token) {
            if ($token->acceptAnyValue() === false) {
                $requirements[$token->getName()] = Parameter::getValidatorPattern($token->getDataType());
            }
        }

        $route = new Route($href, $defaults, $requirements, [], '', [], $action->getHttpMethod());

        $this->routes->add($routeName, $route);
    }
}
