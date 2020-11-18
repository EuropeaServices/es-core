<?php

namespace Es\CoreBundle\Event\CRUD;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use  Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;
use Es\CoreBundle\Entity\Contract\AbstractEntityInterface;

/**
 * Listener mettant à jour les champs de création et mise à jour pour toutes
 * les entités implémentant l'interface AbstractEntityInterface
 *
 * @link AbstractEntityInterface
 *      
 * @since 15/10/2019
 * @author hroux
 */
class EntityEvent implements EventSubscriber
{

    /**
     * Service Security permettant l'authentification
     *
     * @var Security
     */
    private $security;

    /**
     * Constructeur
     *
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
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
            Events::preUpdate,
            Events::prePersist,
            Events::preRemove
        ];
    }

    /**
     * Met à jour la date et l'auteur de la mise à jour
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $user = $this->security->getUser();
        if ($entity instanceof AbstractEntityInterface) {
            $entity->setUpdatedAt(new \DateTime());
            if ($user) {
                $entity->setUpdatedBy($user);
            }
        }
    }

    /**
     * Met à jour la date et l'auteur de la création
     * Appel également la fonction preUpdate
     *
     * @link CRUDEntityListener::preUpdate
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $user = $this->security->getUser();
        if ($entity instanceof AbstractEntityInterface) {
            $entity->setCreatedAt(new \DateTime());
            if ($user) {
                $entity->setCreatedBy($user);
            }
            $this->preUpdate($args);
        }
    }

    /**
     * Met à jour la date et l'auteur de la suppression
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $user = $this->security->getUser();
        if ($entity instanceof AbstractEntityInterface) {
            $entity->setDeletedAt(new \DateTime());
            if ($user) {
                $entity->setDeletedBy($user);
            }
        }
    }
}
