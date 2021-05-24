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
     * @var SecurityUtils
     */
    private $securityUtils;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    private $minimalPasswordLength;

    private $regexPattern;

    /**
     * Constructor
     *
     */
    public function __construct(
        SecurityUtils $securityUtils,
        EntityManagerInterface $entityManager,
        FormFactory $formFactory,
        EventDispatcherInterface $eventDispatcher,
        int $minimalPasswordLength,
        string $regexPattern
    ) {
        $this->securityUtils = $securityUtils;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->minimalPasswordLength = $minimalPasswordLength;
        $this->regexPattern = $regexPattern;
    }
    /**
     * If not connected, return to home
     * If not, print the form to change password
     *
     * @return Response|RedirectResponse
     */
    public function changePassword(Request $request, ?int $mustChange=0)
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
