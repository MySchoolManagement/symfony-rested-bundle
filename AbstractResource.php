<?php
namespace Rested\Bundle\RestedBundle;

use Rested\Definition\ActionDefinition;
use Rested\FactoryInterface;
use Rested\Resource;
use Rested\ResourceInterface;
use Rested\RestedServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

abstract class AbstractResource extends Controller implements ResourceInterface
{

    use Resource;

    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $authManager;

    /**
     * @var \Rested\FactoryInterface
     */
    private $factory;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Rested\RestedServiceInterface
     */
    private $restedService;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        RestedServiceInterface $restedService,
        FactoryInterface $factory,
        AuthorizationCheckerInterface $authorizationChecker,
        AuthManager $authManager,
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorage)
    {
        $this->authManager = $authManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->factory = $factory;
        $this->requestStack = $requestStack;
        $this->restedService = $restedService;
        $this->tokenStorage = $tokenStorage;
    }

    public function export($instance, $allFields = false)
    {
        $action = $this->getCurrentAction();
        $context = $this->getCurrentContext();
        $transform = $action->getTransform();

        // always export using the instance action
        $transformMapping = $context
            ->getResourceDefinition()
            ->findFirstAction(ActionDefinition::TYPE_INSTANCE)
            ->getTransformMapping()
        ;

        if ($allFields === true) {
            return $transform->exportAll($context, $transformMapping, $instance);
        } else {
            return $transform->export($context, $transformMapping, $instance);
        }
    }

    public function exportAll($instance)
    {
        return $this->export($instance, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationChecker()
    {
        return $this->authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getRestedService()
    {
        return $this->restedService;
    }

    public function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }

    /**
     * @return mixed
     */
    public function preHandle()
    {
        $action = $this
            ->getRouter()
            ->getCurrentRoute()
            ->getAction()
        ;

        $attributes = $this
            ->getCurrentRequest()
            ->attributes
        ;
        $attributes->set('_rested', $action['_rested']);

        return call_user_func_array([$this, 'handle'], func_get_args());
    }
}
