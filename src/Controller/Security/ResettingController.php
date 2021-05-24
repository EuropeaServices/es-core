<?php

namespace Es\CoreBundle\Controller\Security;

use Es\CoreBundle\Mailer\CoreMailer;
use Symfony\Component\Form\FormFactory;
use Doctrine\ORM\EntityManagerInterface;
use Es\CoreBundle\Event\UserEvent;
use Es\CoreBundle\Security\SecurityUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Es\CoreBundle\Form\Type\Security\ResettingType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller allow to resseting password
 * @author hroux
 * @since 11/11/2020
 */
class ResettingController extends AbstractController
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
     * @var CoreMailer
     */
    private $coreMailer;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Path of user class
     *
     * @var string
     */
    private $userClass;

    private $minimalPasswordLength;

    private $regexPattern;

    /**
     * Constructor
     *
     * @param SecurityUtils $securityUtils
     * @param EntityManagerInterface $entityManager
     * @param CoreMailer $coreMailer
     * @param FormFactory $formFactory
     * @param string $userClass
     */
    public function __construct(
        SecurityUtils $securityUtils,
        EntityManagerInterface $entityManager,
        CoreMailer $coreMailer,
        FormFactory $formFactory,
        EventDispatcherInterface $eventDispatcher,
        string $userClass,
        int $minimalPasswordLength,
        string $regexPattern
    ) {
        $this->userClass = $userClass;
        $this->securityUtils = $securityUtils;
        $this->entityManager = $entityManager;
        $this->coreMailer = $coreMailer;
        $this->formFactory = $formFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->minimalPasswordLength = $minimalPasswordLength;
        $this->regexPattern = $regexPattern;
    }

    /**
     * If connected, return to home
     * If not, print the view to input email or username to reset password
     *
     * @return Response|RedirectResponse
     */
    public function resettingRequest()
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('@EsCore/security/resetting/resettingRequest.html.twig', []);
    }

    /**
     * Send the mail if the username or email are knew or if the resquest password are not expired
     * Redirect to the view of confirm after that
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');

        $user = $this->entityManager->getRepository($this->userClass)->findUserByUsernameOrEmail($username);
        if (
            $user !== null
            && !($this->securityUtils->isPasswordRequestNonExpired($user))
        ) {
            $user->setConfirmationToken($this->securityUtils->generateToken());
            $user->setPasswordRequestedAt(new \DateTime());
            $this->coreMailer->sendResettingMail($user);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('es_core_resetting_confirm_send_email');
    }

    /**
     * Return confirm email send view
     *
     * @return Response
     */
    public function confirmSendEmailAction()
    {
        return $this->render('@EsCore/security/resetting/resettingRequestConfirm.html.twig', []);
    }

    /**
     * Check if token is valid and print the resset password form
     * When the form is submitted, change the password for the user and redirect to home route
     *
     * @param Request $request
     * @param string $token
     * @return Response|RedirectResponse
     */
    public function resetPassword(Request $request, $token)
    {
        $user = $this->entityManager->getRepository($this->userClass)->findOneBy(["confirmationToken" =>  $token]);

        if (null === $user) {
            return new RedirectResponse($this->container->get('router')->generate('es_core_login'));
        }

        $optionsForm = array();
        $optionsForm["minimal_password_length"] = $this->minimalPasswordLength;
        $optionsForm["regex_pattern"] = $this->regexPattern;

        $form = $this->formFactory->create(ResettingType::class, $user, $optionsForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->securityUtils->encodePassword($user);
            $user->setPassword($hashedPassword);
            $userEvent = new UserEvent($user);
            $this->eventDispatcher->dispatch($userEvent, UserEvent::PASSWORD_CHANGED);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $url = $this->generateUrl('home');
            $response = new RedirectResponse($url);

            return $response;
        }
        return $this->render('@EsCore/security/resetting/resetPasswordForm.html.twig', [
            'token' => $token,
            'form' => $form->createView()
        ]);
    }
}
