<?php
namespace Rested\Bundle\RestedBundle\EventListener;

use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class KernelEventListener
{

    const MASTER_HEADER = 'X-Request-ID';
    const SUB_HEADER = 'X-SubRequest-ID';

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $masterHeaders = $this->requestStack->getMasterRequest()->headers;
        $currentHeaders = $event->getRequest()->headers;

        // if the master request doesn't have the header then this must be the top most request, not a sub
        if ($masterHeaders->has(self::MASTER_HEADER) === false) {
            $id = Uuid::uuid4()->toString();

            $masterHeaders->set(self::MASTER_HEADER, $id);
            $masterHeaders->set(self::SUB_HEADER, $id);
        } else {
            $currentHeaders->set(self::MASTER_HEADER, $masterHeaders->get(self::MASTER_HEADER));
            $currentHeaders->set(self::SUB_HEADER, Uuid::uuid4()->toString());
        }
    }
}
