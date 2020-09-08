<?php
namespace ES\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class EsCoreEvents
{
    /**
     * Called directly before the Lorem Ipsum API data is returned.
     *
     * Listeners have the opportunity to change that data.
     *
     * @Event("ES\CoreBundle\Event\SecurityResponseEvent")
     */
    const SECURITY = 'es_core.security';
}