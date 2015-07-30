<?php
namespace Rested\Bundle\RestedBundle\Routing;

use Rested\UrlGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

class UrlGenerator implements UrlGeneratorInterface
{

    /**
     * @var
     */
    protected $mountPath;

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    protected $urlGenerator;

    public function __construct(SymfonyUrlGeneratorInterface $urlGenerator, $mounthPath)
    {
        $this->mountPath = $mounthPath;
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
        return $this->urlGenerator->route($routeName, $parameters, $absolute ? SymfonyUrlGeneratorInterface::ABSOLUTE_URL : SymfonyUrlGeneratorInterface::RELATIVE_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function url($path, $absolute = true)
    {
        if ($absolute === false) {
            return $path;
        }

        return $this->urlGenerator->to($path);
    }
}
