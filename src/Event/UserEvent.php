<?php

namespace Es\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Es\CoreBundle\Entity\Security\UserInterface;

class UserEvent extends Event
{

    /**
     * Called directly before the Lorem Ipsum API data is returned.
     *
     * Listeners have the opportunity to change that data.
     *
     * @Event("Es\CoreBundle\Event\UserEvent")
     */
    public const PASSWORD_CHANGED = 'es_core.password_changed';

    protected $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
