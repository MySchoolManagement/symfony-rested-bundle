<?php
namespace Rested\Bundle\RestedBundle\Routing;

use Rested\UrlGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

class UrlGenerator implements UrlGeneratorInterface
{

    /**
     * @var
     */
    protected $mountPrefix;

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    protected $urlGenerator;

    public function __construct(SymfonyUrlGeneratorInterface $urlGenerator, $mountPrefix)
    {
        $this->mountPrefix = $mountPrefix;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getMountPath()
    {
        return $this->mountPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function route($routeName, array $parameters = [], $absolute = true)
    {
        return $this->urlGenerator->generate($routeName, $parameters, $absolute ? SymfonyUrlGeneratorInterface::ABSOLUTE_URL : SymfonyUrlGeneratorInterface::RELATIVE_PATH);
    }
}
