<?php
namespace Es\CoreBundle\Entity\Contract;

use Es\CoreBundle\Entity\Security\UserInterface;

/**
 * interface AbstractEntity
 * Must be implement by all entity
 *
 * @since 15/10/2019
 * @author hroux
 */
interface AbstractEntityInterface
{

    public function setCreatedAt(\DateTime $createdAt): void;

    public function getCreatedAt(): \DateTime;

    public function setUpdatedAt(\DateTime $updatedAt): void;

    public function getUpdatedAt(): \DateTime;

    public function setDeletedAt(\DateTime $deletedAt = null): void;

    public function getDeletedAt(): ?\DateTime;

    public function setCreatedBy(UserInterface $createdBy): void;

    public function getCreatedBy(): UserInterface;

    public function setUpdatedBy(UserInterface $updatedBy = null): void;

    public function getUpdatedBy(): UserInterface;

    public function setDeletedBy(UserInterface $deletedBy = null): void;

    public function getDeletedBy(): ?UserInterface;
    
    public function getField(string $field);
}
