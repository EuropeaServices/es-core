<?php

namespace Es\CoreBundle\Security;

use Es\CoreBundle\Entity\Security\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

/**
 * Utils of the security parts
 * 
 * @author hroux
 * @since 11/11/2020
 */
class SecurityUtils
{
    /**
     * TTL in second after a resend an request of resetting password
     *
     * @var int
     */
    private $retryTtl;

    /**
     * @var PasswordHasherFactory
     */
    private $encoderFactory;

    /**
     * The constructor
     *
     * @param PasswordHasherFactory $encoderFactory
     * @param integer $retryTtl
     */
    public function __construct(PasswordHasherFactory $encoderFactory, int $retryTtl)
    {
        $this->retryTtl = $retryTtl;
        $this->encoderFactory = $encoderFactory;
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
        $encoder = $this->encoderFactory->getEncoder($user);
        return $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
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
