<?php
namespace Es\CoreBundle\Event\CRUD;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\Security\Core\Security;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
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
        if ($entity instanceof AbstractEntityInterface) {
            $entity->setUpdatedAt(new \DateTime());
            $entity->setUpdatedBy($this->security->getUser());
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
        dump('LA');
        $entity = $args->getObject();
        if ($entity instanceof AbstractEntityInterface) {
            $entity->setCreatedAt(new \DateTime());
            $entity->setCreatedBy($this->security->getUser());
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
        if ($entity instanceof AbstractEntityInterface) {
            $entity->setDeletedAt(new \DateTime());
            $entity->setDeletedBy($this->security->getUser());
        }
    }
}

