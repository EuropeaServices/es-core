<?php

namespace Es\CoreBundle\Tests\Mailer;


use PHPUnit\Framework\TestCase;
use Es\CoreBundle\Mailer\CoreEmail;
use Symfony\Component\HttpKernel\KernelInterface;

class CoreEmailTest extends TestCase
{
    /**
     * @var string
     */
    private string $mailToDev;

    /**
     * @var string
     */
    private string $mailFrom;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    private $kernelInterface;

    /**
     * @var CoreEmail
     */
    private CoreEmail $coreEmail;

    protected function setUp(): void
    {
        $this->mailToDev = "suppor_applicatif@europeaservices.com";
        $this->mailFrom = "suppor_applicatif@europeaservices.com";
        $this->kernelInterface =  $this->getMockBuilder(KernelInterface::class)->getMock();
        $this->kernelInterface->method('getEnvironment')
            ->will($this->returnValue("dev"));
        $this->coreEmail = new CoreEmail($this->kernelInterface, $this->mailToDev, $this->mailFrom);
    }

    public function testTo()
    {
        $this->coreEmail->to("toto@test.com");
        $result = $this->coreEmail->getTo();

        $this->assertCount(1, $result);
        $this->assertEquals($this->mailToDev, $result[0]->getAddress());
    }
}
