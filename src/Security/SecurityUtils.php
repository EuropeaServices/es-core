<?php

namespace Es\CoreBundle\Security;

use Es\CoreBundle\Entity\Security\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Utils of the security parts
 * 
 * @author hroux
 * @since 11/11/2020
 */
class SecurityUtils
{
    /**
     * The constructor
     *
     * @param PasswordHasherFactory $encoderFactory
     * @param integer $retryTtl
     */
    public function __construct(
        private PasswordHasherFactory $encoderFactory,
        private int $retryTtl
    ) {
    }

    /**
     * Check if the request password is expired or not
     *
     * @param UserInterface $user
     * @return boolean
     */
    public function isPasswordRequestNonExpired(UserInterface $user): bool
    {
        return $user->getPasswordRequestedAt() instanceof \DateTime &&
            $user->getPasswordRequestedAt()->getTimestamp() + $this->retryTtl > time();
    }

    /**
     * Encode the password by the encoder factory
     *
     * @param UserInterface $user
     * @return string
     */
    public function encodePassword(UserInterface $user): string
    {
        $encoder = $this->encoderFactory->getPasswordHasher($user);
        return $encoder->hash($user->getPlainPassword());
    }

    /**
     * Get TTL
     *
     * @return integer
     */
    public function getRetryTtl(): int
    {
        return $this->retryTtl;
    }

    /**
     * Genere an random Token
     *
     * @return string
     */
    public function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
