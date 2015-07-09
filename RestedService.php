<?php
namespace Rested\Bundle\RestedBundle;

use Rested\RestedResourceInterface;
use Rested\RestedServiceInterface;
use Symfony\Component\HttpFoundation\Request;

class RestedService implements RestedServiceInterface
{

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string[]
     */
    private $resources = [];

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    public function addResource($class)
    {
        $this->resources[] = $class;
    }

    public function execute($url, $method = 'get', $data = [], &$statusCode = null)
    {

    }

    public function getResources()
    {
        return $this->resources;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }
}
