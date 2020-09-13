<?php

namespace Es\CoreBundle\Tests\Security;

use Es\CoreBundle\Security\SecurityUtils;
use Es\CoreBundle\Entity\Security\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use PHPUnit\Framework\TestCase;

class SecurityUtilsTest extends TestCase
{
    /**
     * @var SecurityUtils
     */
    private $securityUtils;

    /** 
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    private $encoderFactory;

    /**
     * @var integer
     */
    private $retryTtl = 7200;

    protected function setUp(): void
    {
        $this->encoderFactory = $this->getMockBuilder(EncoderFactoryInterface::class)->getMock();
        $this->securityUtils = new SecurityUtils($this->encoderFactory, $this->retryTtl);
    }

    public function testIsPasswordRequestNonExpired()
    {
        $user = new User();
        $date = new \DateTime();
        $user->setPasswordRequestedAt($date);
        $resultTrue = $this->securityUtils->isPasswordRequestNonExpired($user);
        $date->modify("-2 hour");
        $user->setPasswordRequestedAt($date);
        $resultFalse = $this->securityUtils->isPasswordRequestNonExpired($user);

        $this->assertEquals($resultTrue, true);
        $this->assertEquals($resultFalse, false);
    }

    public function testEncodePassword()
    {
        $user = new User();
        $user->setPlainPassword("test");
        $passwordEncoder = $this->getMockBuilder(PasswordEncoderInterface::class)->getMock();
        $passwordEncoder->method('encodePassword')
        ->will($this->returnValue("xxxxxx"));
        $this->encoderFactory->method("getEncoder")->will($this->returnValue($passwordEncoder));
        $result = $this->securityUtils->encodePassword($user);

        $this->assertIsString($result);
    }

    public function testGetRetryTtl()
    {
        $result = $this->securityUtils->getRetryTtl();

        $this->assertEquals($result, $this->retryTtl);
    }

    public function testGenerateToken()
    {
        $result = $this->securityUtils->generateToken();

        $this->assertIsString($result);
    }
}