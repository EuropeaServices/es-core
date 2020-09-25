<?php

namespace Es\CoreBundle\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Es\CoreBundle\Security\SecurityUtils;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Es\CoreBundle\Mailer\CoreMailer;
use Symfony\Component\Form\FormFactory;
use Es\CoreBundle\Form\Type\Security\ResettingType;
use Es\CoreBundle\Entity\Security\User;

class ResettingController extends AbstractController
{

    private $securityUtils;

    private $entityManager;

    private $coreMailer;

    private $formFactory;

    public function __construct(SecurityUtils $securityUtils, EntityManagerInterface $entityManager, CoreMailer $coreMailer, FormFactory $formFactory)
    {
        $this->securityUtils = $securityUtils;
        $this->entityManager = $entityManager;
        $this->coreMailer = $coreMailer;
        $this->formFactory = $formFactory;
    }

    public function resettingRequest()
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('@EsCore/security/resetting/resettingRequest.html.twig', []);
    }

    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');

        $user = $this->entityManager->getRepository(User::class)->findUserByUsernameOrEmail($username);
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

    public function confirmSendEmailAction()
    {
        return $this->render('@EsCore/security/resetting/resettingRequestConfirm.html.twig', []);
    }

    public function resetPassword(Request $request, $token)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(["confirmationToken" =>  $token]);

        if (null === $user) {
            return new RedirectResponse($this->container->get('router')->generate('es_core_login'));
        }
        $form = $this->formFactory->create(ResettingType::class);
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->securityUtils->encodePassword($user);
            $user->setPassword($hashedPassword);
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
