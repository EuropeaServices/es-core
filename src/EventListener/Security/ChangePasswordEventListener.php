<?php

namespace Es\CoreBundle\EventListener\Security;

use Es\CoreBundle\Event\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Event listener relative au changement de mot de passe
 * @author hroux
 * @since 05/05/2021
 */
class ChangePasswordEventListener implements EventSubscriberInterface
{

    /**
     * Service Security permettant l'authentification
     *
     * @var Security
     */
    private $security;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var string
     */
    private string $number;

    /**
     * @var string
     */
    private string $unity;

    /**
     * @var string
     */
    private string $routeNameChangePassword;

    /**
     * Constructor
     *
     * @param Security $security
     * @param UrlGeneratorInterface $urlGenerator
     * @param string $number
     * @param string $unity
     * @param string $routeNameChangePassword
     */
    public function __construct(
        Security $security,
        UrlGeneratorInterface $urlGenerator,
        string $number,
        string $unity,
        string $routeNameChangePassword
    ) {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
        $this->number = $number;
        $this->unity = $unity;
        $this->routeNameChangePassword = $routeNameChangePassword;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEvent::PASSWORD_CHANGED => "onPasswordChanged",
            KernelEvents::REQUEST => array(array('onKernelRequest', -10000))
        ];
    }

    /**
     * Mise à jour de la date de réinitialisation de mot de passe
     *
     * @param UserInterface $user
     */
    public function onPasswordChanged(UserEvent $userEvent): void
    {
        $user = $userEvent->getUser();
        $user->setPasswordChangedAt(new \DateTime());
    }

    public function onKernelRequest(RequestEvent $requestEvent): void
    {
        $routeName = $requestEvent->getRequest()->get("_route");
        $user = $this->security->getUser();
        if ($routeName != null && $this->routeNameChangePassword != $routeName && $user != null) {
            //test exipired password user
            $passwordExpiredAt = clone $user->getPasswordChangedAt();
            $passwordExpiredAt->modify("+" . $this->number . " " . $this->unity);
            if ($passwordExpiredAt <= new \DateTime()) {
                $url = $this->urlGenerator->generate($this->routeNameChangePassword, ["mustChange" => 1]);
                $requestEvent->setResponse(new RedirectResponse($url));
            }
        }
    }
}
