<?php

namespace Es\CoreBundle\Controller\Security;

use Symfony\Component\Form\FormFactory;
use Doctrine\ORM\EntityManagerInterface;
use Es\CoreBundle\Event\UserEvent;
use Es\CoreBundle\Security\SecurityUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Es\CoreBundle\Form\Type\Security\ChangePasswordType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller allow to change password when the user is connected
 * @author hroux
 * @since 05/04/2021
 */
class ChangePasswordController extends AbstractController
{

    /**
     * Constructor
     *
     */
    public function __construct(
        private SecurityUtils $securityUtils,
        private EntityManagerInterface $entityManager,
        private FormFactory $formFactory,
        private EventDispatcherInterface $eventDispatcher,
        private int $minimalPasswordLength,
        private string $regexPattern
    ) {
    }
    /**
     * If not connected, return to home
     * If not, print the form to change password
     *
     * @return Response|RedirectResponse
     */
    public function changePassword(Request $request, ?int $mustChange = 0): Response|RedirectResponse
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $optionsForm = array();
        $optionsForm["minimal_password_length"] = $this->minimalPasswordLength;
        $optionsForm["regex_pattern"] = $this->regexPattern;

        $form = $this->formFactory->create(ChangePasswordType::class, $this->getUser(), $optionsForm);
        $form->handleRequest($request);
        $userEvent = new UserEvent($this->getUser());

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->securityUtils->encodePassword($this->getUser());
            $this->getUser()->setPassword($hashedPassword);
            $userEvent = new UserEvent($this->getUser());
            $this->eventDispatcher->dispatch($userEvent, UserEvent::PASSWORD_CHANGED);
            $this->entityManager->persist($this->getUser());
            $this->entityManager->flush();
            $url = $this->generateUrl('home');
            $response = new RedirectResponse($url);

            return $response;
        }
        return $this->render('@EsCore/security/changePassword/changePasswordForm.html.twig', [
            'form' => $form->createView(),
            "must_change"   => $mustChange
        ]);
    }
}
