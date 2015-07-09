<?php
namespace Rested\Bundle\RestedBundle;

use Rested\FactoryInterface;
use Rested\RestedResource;
use Rested\RestedResourceInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Role\Role;


abstract class AbstractResource implements ContainerAwareInterface, RestedResourceInterface
{

    use RestedResource;

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
    private $rested;

    private $tokenStorage;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->authorizationChecker = $container->get('security.authorization_checker');
        $this->factory = $container->get('rested.factory');
        $this->requestStack = $container->get('request_stack');
        $this->urlGenerator = $container->get('rested.url_generator');
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    public function getCurrentRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}
