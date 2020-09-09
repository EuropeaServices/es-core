<?php
namespace Es\CoreBundle\Entity\Contract;

use Es\CoreBundle\Entity\User;

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

    public function setCreatedBy(User $createdBy): void;

    public function getCreatedBy(): User;

    public function setUpdatedBy(User $updatedBy = null): void;

    public function getUpdatedBy(): User;

    public function setDeletedBy(User $deletedBy = null): void;

    public function getDeletedBy(): ?User;
    
    public function getField(string $field);
}
