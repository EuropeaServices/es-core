<?php

namespace Es\CoreBundle\Event\Doctrine;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Es\CoreBundle\Entity\Contract\AbstractEntityInterface;

class LoadClassMetadataEvent implements EventSubscriber
{
    /**
     *
     * @var string
     */
    private $userPath;

    public function __construct(string $userPath)
    {
        $this->userPath = $userPath;
    }
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    public function loadClassMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();
        $class = new \ReflectionClass($metadata->getName());

        if ($class->implementsInterface(AbstractEntityInterface::class) && !$metadata->isMappedSuperclass) {
            $metadata->mapManyToOne(array(
                'targetEntity'  => $this->userPath,
                'fieldName'     => 'createdBy',
                'joinColumns'   => [[
                    'name'                 => 'created_by',
                    'referencedColumnName' => 'id',
                    'nullable'             => false,
                ]]
            ));
            $metadata->mapManyToOne(array(
                'targetEntity'  => $this->userPath,
                'fieldName'     => 'updatedBy',
                'joinColumns'   => [[
                    'name'                 => 'updated_by',
                    'referencedColumnName' => 'id',
                    'nullable'             => false,
                ]]
            ));
            $metadata->mapManyToOne(array(
                'targetEntity'  => $this->userPath,
                'fieldName'     => 'deletedBy',
                'joinColumns'   => [[
                    'name'                 => 'deleted_by',
                    'referencedColumnName' => 'id',
                    'nullable'             => false,
                ]]
            ));
        }
    }
}
