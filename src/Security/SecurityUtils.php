<?php

namespace Es\CoreBundle\Security;

use Es\CoreBundle\Entity\Security\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class SecurityUtils
{
    private $retryTtl;

    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory, int $retryTtl)
    {
        $this->retryTtl = $retryTtl;
        $this->encoderFactory = $encoderFactory;
    }

    public function isPasswordRequestNonExpired(User $user): bool
    {
        return $user->getPasswordRequestedAt() instanceof \DateTime &&
            $user->getPasswordRequestedAt()->getTimestamp() + $this->retryTtl > time();
    }

    public function encodePassword(User $user)
    {
        $encoder = $this->encoderFactory->getEncoder($user);
        return $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
    }

    public function getRetryTtl(): int
    {
        return $this->retryTtl;
    }

    public function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
