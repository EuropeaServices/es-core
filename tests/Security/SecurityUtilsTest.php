<?php

namespace Es\CoreBundle\Tests\Security;

use Es\CoreBundle\Security\SecurityUtils;
use Es\CoreBundle\Entity\Security\User;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

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
        $this->encoderFactory = $this->getMockBuilder(PasswordHasherFactory::class)->disableOriginalConstructor()->getMock();
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
        $passwordEncoder = $this->getMockBuilder(PasswordHasherInterface::class)->disableOriginalConstructor()->getMock();
        $passwordEncoder->method('hash')
        ->will($this->returnValue("xxxxxx"));
        $this->encoderFactory->method("getPasswordHasher")->will($this->returnValue($passwordEncoder));
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