<?php

namespace Es\CoreBundle\Tests\Mailer;


use PHPUnit\Framework\TestCase;
use Es\CoreBundle\Mailer\CoreEmail;
use Symfony\Component\Mailer\MailerInterface;
use Es\CoreBundle\Mailer\CoreMailer;
use Es\CoreBundle\Entity\Security\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class CoreMailerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    private $mailer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    private $email;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    private $templating;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    private $urlGenerator;

    /**
     * @var CoreMailer
     */
    private CoreMailer $coreMailer;

    protected function setUp(): void
    {
        $this->mailer = $this->getMockBuilder(MailerInterface::class)
            ->getMock();
        $this->email = $this->getMockBuilder(CoreEmail::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->templating = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlGenerator = $this->getMockBuilder(UrlGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->email->method('subject')
            ->will($this->returnValue($this->email));
        $this->email->method('to')
            ->will($this->returnValue($this->email));
        $this->email->method('html')
            ->will($this->returnValue($this->email));
        $this->mailer->method("send");
        $this->coreMailer = new CoreMailer($this->mailer, $this->email, $this->templating, $this->urlGenerator);
    }

    public function testSendResettingMail()
    {
        $user = new User();
        $this->templating->method('render')
            ->will($this->returnValue("<html></html>"));
        $this->urlGenerator->method('generate')
            ->will($this->returnValue("/home"));
        $result = $this->coreMailer->sendResettingMail($user);

        $this->assertEquals($result, $this->coreMailer);
    }
}
