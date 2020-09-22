<?php

namespace Es\CoreBundle\Tests\Security\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Es\CoreBundle\Controller\Security\SecurityController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Es\CoreBundle\Tests\TestingUtils\TokenManager;

class SecurityControllerTest extends TestCase
{
    /**
     * @var SecurityController
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
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    /** 
     * @var TokenManager
     */ 
    private $tokenStorage;

    /** 
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    private $twig;

    /** 
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    private $router;

    protected function setUp(): void
    {
        $this->authenticationUtils = $this->getMockBuilder(AuthenticationUtils::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = new Request();
        $this->container = new Container();
        $this->tokenStorage = new TokenManager();
        $this->twig = $this->getMockBuilder(\Twig\Environment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->router = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->router->method("generate")->will($this->returnValue("/"));
        $this->container->set("security.token_storage", $this->tokenStorage);
        $this->container->set("twig", $this->twig);
        $this->container->set("router", $this->router);
        $this->controller = new SecurityController($this->authenticationUtils);
        $this->controller->setContainer($this->container);
        $testedMethods = [
            'login',
            'logout'
        ];
        foreach ($testedMethods as $testedMethod) {
            $method = new \ReflectionMethod(SecurityController::class, $testedMethod);
            $method->setAccessible(true);
            $this->protectedTestedMethods[$testedMethod] = $method;
        }
    }

    public function testLogin()
    {
        $this->twig->method("render")->will($this->returnValue("<html></html>"));
        $response = $this->protectedTestedMethods['login']->invoke($this->controller, "", 200, [], $this->request);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testLogout()
    {
        $response = $this->protectedTestedMethods['logout']->invoke($this->controller, "", 200, [], $this->request);

        $this->assertSame(302, $response->getStatusCode());
    }
}


