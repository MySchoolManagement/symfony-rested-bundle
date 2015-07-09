<?php
namespace Rested\Bundle\RestedBundle\Routing;

use Rested\UrlGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

class UrlGenerator implements UrlGeneratorInterface
{

    private $urlGenerator;

    public function __construct(SymfonyUrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $absolute = true)
    {
        return $this->urlGenerator->generate($name, $parameters, $absolute);
    }
}