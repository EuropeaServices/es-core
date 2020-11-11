<?php

namespace Es\CoreBundle\Entity\Security;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserInterface extends SymfonyUserInterface
{
    /**
     * @param GroupInterface $group
     * @return self
     */
    public function addGroup(GroupInterface $group): self;

    /**
     * @param GroupInterface $group
     * @return self
     */
    public function removeGroup(GroupInterface $group): self;

    /**
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt();

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string;
}
