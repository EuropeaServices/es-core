<?php
namespace Es\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class EsCoreEvents
{
    /**
     * Called directly before the Lorem Ipsum API data is returned.
     *
     * Listeners have the opportunity to change that data.
     *
     * @Event("Es\CoreBundle\Event\SecurityResponseEvent")
     */
    const SECURITY = 'es_core.security';
}