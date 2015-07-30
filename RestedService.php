<?php
namespace Rested\Bundle\RestedBundle;

use Rested\Compiler\CompilerCacheInterface;
use Rested\ResourceInterface;
use Rested\RestedResourceInterface;
use Rested\RestedServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RestedService implements RestedServiceInterface
{

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var \Rested\Compiler\CompilerCacheInterface
     */
    private $compilerCache;

    /**
     * @var string[]
     */
    private $resources = [];

    public function __construct(CompilerCacheInterface $compilerCache, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->compilerCache = $compilerCache;
    }

    public function addResource($class)
    {
        $this->resources[] = $class;
    }

    public function execute($url, $method = 'get', $data = [], &$statusCode = null)
    {

    }
    /**
     * {@inheritdoc}
     */
    public function findActionByRouteName($routeName)
    {
        $resourceDefinition = $this->compilerCache->findResourceDefinition($routeName);

        if ($resourceDefinition !== null) {
            return $resourceDefinition->findActionByRouteName($routeName);
        }

        return null;
    }

    public function getResources()
    {
        return $this->resources;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveContextFromRequest(Request $request, ResourceInterface $resource)
    {
        $requestId = $request->headers->get(RequestIdMiddleware::SUB_HEADER);

        if (array_key_exists($requestId, $this->contexts) === true) {
            return $this->contexts[$requestId];
        }

        $spec = $request->get('_rested');

        $requestParser = new RequestParser();
        $requestParser->parse($request->getRequestUri(), $request->query->all());

        $cache = $this->compilerCache;
        $cache->setAuthorizationChecker($this->authorizationChecker);

        $factory = $this->factory;
        $compiledResourceDefinition = $cache->findResourceDefinition($spec['route_name']);

        $context = $factory->createContext(
            $requestParser->getParameters(),
            $spec['action'],
            $spec['route_name'],
            $compiledResourceDefinition
        );

        return ($this->contexts[$requestId] = $context);
    }
}
