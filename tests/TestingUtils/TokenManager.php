<?php

namespace Es\CoreBundle\Tests\TestingUtils;

/**
 * On est obligé de créer cette classe remplaçant le service
 * Symfony\Component\Security\Core\Authentication\Token\Storage\UsageTrackingTokenStorage
 * car on peut pas le mock (il a l'attribut final)
 */
class TokenManager
{
    public function getToken()
    {
        return null;
    }
}