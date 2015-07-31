<?php
namespace Rested\Bundle\RestedBundle;

use Rested\Definition\ActionDefinition;
use Rested\FactoryInterface;
use Rested\Resource;
use Rested\ResourceInterface;
use Rested\RestedServiceInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

abstract class AbstractResource implements ContainerAwareInterface, ResourceInterface
{

    use Resource;

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

    public function setContainer(ContainerInterface $container = null)
    {
        $this->authorizationChecker = $container->get('security.authorization_checker');
        $this->factory = $container->get('rested.factory');
        $this->requestStack = $container->get('request_stack');
        $this->restedService = $container->get('rested');
        $this->tokenStorage = $container->get('security.token_storage');
    }
}
