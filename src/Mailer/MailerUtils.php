<?php

namespace Es\CoreBundle\Mailer;

use Doctrine\ORM\EntityManagerInterface;

class MailerUtils
{
    private $coreMailer;

    private $entityManager;

    private $userClass;

    private $numberPasswordExpired;

    private $unityDatePasswordExpired;

    private $numberWarningMail;

    private $unityDateWarningMail;

    public function __construct(
        CoreMailer $coreMailer,
        EntityManagerInterface $entityManager,
        string $userClass,
        string $numberPasswordExpired,
        string $unityDatePasswordExpired,
        string $numberWarningMail,
        string $unityDateWarningMail
    ) {
        $this->coreMailer = $coreMailer;
        $this->entityManager = $entityManager;
        $this->userClass = $userClass;
        $this->numberPasswordExpired = $numberPasswordExpired;
        $this->unityDatePasswordExpired = $unityDatePasswordExpired;
        $this->numberWarningMail = $numberWarningMail;
        $this->unityDateWarningMail = $unityDateWarningMail;
    }

    public function sendMailWarningPasswordExpired()
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
