<?php

namespace Es\CoreBundle\Entity\Security;

use Es\CoreBundle\Entity\Security\UserInterface;
use Doctrine\Common\Collections\Collection;

interface GroupInterface
{
    /**
     * @return array
     */
    public function getRoles(): array;

    /**
     * @return Collection|UserInterface[]
     */
    public function getUsers(): Collection;
}
