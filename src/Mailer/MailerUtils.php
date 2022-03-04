<?php

namespace Es\CoreBundle\Mailer;

use Doctrine\ORM\EntityManagerInterface;

class MailerUtils
{
    public function __construct(
        private CoreMailer $coreMailer,
        private EntityManagerInterface $entityManager,
        private string $userClass,
        private string $numberPasswordExpired,
        private string $unityDatePasswordExpired,
        private string $numberWarningMail,
        private string $unityDateWarningMail
    ) {
    }

    public function sendMailWarningPasswordExpired(): void
    {
        $users = $this->entityManager
            ->getRepository($this->userClass)
            ->findUserToSendWarningPasswordExpired(
                $this->numberPasswordExpired,
                $this->unityDatePasswordExpired,
                $this->numberWarningMail,
                $this->unityDateWarningMail
            );
        foreach ($users as $user) {
            $this->coreMailer->sendWarningPasswordExpired($user, new \DateTime());
            $user->setMailWarningExpirationPasswordAt(new \DateTime());
            $this->entityManager->persist($user);
        }
        if (!empty($users)) {
            $this->entityManager->flush();
        }
    }
}
