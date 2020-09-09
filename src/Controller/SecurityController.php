<?php
namespace Es\CoreBundle\Controller;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Es\CoreBundle\Event\SecurityResponseEvent;
use Es\CoreBundle\Event\EsCoreEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SecurityController extends AbstractController
{
    private $authenticationUtils;

    public function __construct(AuthenticationUtils $authenticationUtils){
        $this->authenticationUtils = $authenticationUtils;
    }

    public function login(): Response
    {
        if ($this->getUser()) {
             return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();
        return $this->render('@EsCore/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    public function logout()
    {
        return $this->redirectToRoute('es_core_login');
    }
}