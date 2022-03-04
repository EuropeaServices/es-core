<?php

namespace Es\CoreBundle\Controller\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Login Controller
 * 
 * @author hroux
 * @since 11/11/2020
 */
class SecurityController extends AbstractController
{
    /**
     * Constructor
     *
     * @param AuthenticationUtils $authenticationUtils
     */
    public function __construct(private AuthenticationUtils $authenticationUtils)
    {
    }

    /**
     * If the is connected, redirect to home
     * Else print the login form
     *
     * @return Response|RedirectResponse
     */
    public function login(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('@EsCore/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Redirect to login
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        return $this->redirectToRoute('es_core_login');
    }
}
