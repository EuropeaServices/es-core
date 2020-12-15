<?php

namespace Es\CoreBundle\Event\CRUD;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Es\CoreBundle\Mailer\CoreMailer;
use Es\CoreBundle\Security\SecurityUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Es\CoreBundle\Entity\Security\UserInterface;

class UserEvent implements EventSubscriber
{

    /**
     * Service d'envoi de mail
     *
     * @var CoreMailer
     */
    private $coreMailer;

    /**
     * Service Security utils
     *
     * @var SecurityUtils
     */
    private $securityUtils;

    /**
     * Constructeur
     *
     * @param CoreMailer $coreMailer
     */
    public function __construct(CoreMailer $coreMailer, SecurityUtils $securityUtils)
    {
        $this->coreMailer = $coreMailer;
        $this->securityUtils = $securityUtils;
    }

    /**
     * Fonction contenant les hooks récupérés
     *
     * {@inheritdoc}
     * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::prePersist
        ];
    }

    /**
     * Envoi un mail invitant l'utilisateur à initialiser son mot de passe
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof UserInterface) {
            $entity->setConfirmationToken($this->securityUtils->generateToken());
        }
    }


    /**
     * Envoi un mail invitant l'utilisateur à initialiser son mot de passe
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof UserInterface) {
            $this->coreMailer->sendResettingMail($entity);
        }
    }
}
