<?php

namespace Es\CoreBundle\Tests\Security\Controller;

use PHPUnit\Framework\TestCase;
use Es\CoreBundle\Controller\Security\ResettingController;
use Es\CoreBundle\Security\SecurityUtils;
use Es\CoreBundle\Mailer\CoreMailer;
use Es\CoreBundle\Tests\TestingUtils\TokenManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactory;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManagerInterface;

class ResettingControllerTest extends TestCase
{
    /**
     * @var ResettingController
     */
    private $controller;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var array
     */
    private $protectedTestedMethods;

    /** 
     * @var TokenManager
     */
    private $tokenStorage;

    /** 
     * @var Container
     */
    private $container;

    /** 
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    private $twig;

    /** 
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    private $router;

    /** 
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    private $securityUtils;

    /** 
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    private $entityManager;

    /** 
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    private $coreMailer;

    /** 
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    private $formFactory;

    private $userClass = "Es\CoreBundle\Entity\Security\User";

    protected function setUp(): void
    {
        $this->securityUtils = $this->getMockBuilder(SecurityUtils::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->coreMailer = $this->getMockBuilder(CoreMailer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->formFactory = $this->getMockBuilder(FormFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = new Request();
        $this->container = new Container();
        $this->tokenStorage = new TokenManager();
        $this->twig = $this->getMockBuilder(\Twig\Environment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->twig->method("render")->will($this->returnValue("<html></html>"));
        $this->router = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->router->method("generate")->will($this->returnValue("/"));
        $this->container->set("security.token_storage", $this->tokenStorage);
        $this->container->set("twig", $this->twig);
        $this->container->set("router", $this->router);
        $this->controller = new ResettingController(
            $this->securityUtils,
            $this->entityManager,
            $this->coreMailer,
            $this->formFactory,
            $this->userClass
        );
        $this->controller->setContainer($this->container);
        $testedMethods = [
            'resettingRequest',
            'sendEmailAction',
            'confirmSendEmailAction',
            'resetPassword',
        ];
        foreach ($testedMethods as $testedMethod) {
            $method = new \ReflectionMethod(ResettingController::class, $testedMethod);
            $method->setAccessible(true);
            $this->protectedTestedMethods[$testedMethod] = $method;
        }
    }

    public function testResettingRequest()
    {
        $response = $this->protectedTestedMethods['resettingRequest']->invoke($this->controller, "", 200, [], $this->request);

        $this->assertSame(200, $response->getStatusCode());
    }
}
