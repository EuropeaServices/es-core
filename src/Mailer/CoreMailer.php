<?php

namespace Es\CoreBundle\Mailer;

use Symfony\Component\Mailer\MailerInterface;
use Es\CoreBundle\Mailer\CoreEmail;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

class CoreMailer
{

    public function __construct(
        private MailerInterface $mailer,
        private CoreEmail $email,
        private Environment $templating,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function sendResettingMail(UserInterface $user): self
    {
        $template = "@EsCore/security/resetting/email.txt.twig";
        $url = $this->urlGenerator->generate('es_core_reset_password', array('token' => $user->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->templating->render($template, array(
            'user' => $user,
            'confirmationUrl' => $url,
        ));
        return $this->sendEmailMessage($rendered, (string) $user->getEmail());
    }

    public function sendWarningPasswordExpired(UserInterface $user, \DateTime $passwordExpiredAt): self
    {
        $template = "@EsCore/security/changePassword/warningPasswordExpiredEmail.txt.twig";
        $url = $this->urlGenerator->generate('es_core_change_password', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->templating->render($template, array(
            'user' => $user,
            'passwordExpiredAt' => $passwordExpiredAt,
            'urlChangePassword' => $url
        ));
        return $this->sendEmailMessage($rendered, (string) $user->getEmail());
    }

    /**
     * @param string       $renderedTemplate
     * @param array|string $fromEmail
     * @param array|string $toEmail
     */
    public function sendEmailMessage($renderedTemplate, $toEmail): self
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = array_shift($renderedLines);
        $body = implode("\n", $renderedLines);
        $message = $this->email
            ->subject($subject)
            ->to($toEmail)
            ->html($body);
        $this->mailer->send($message);

        return $this;
    }
}
