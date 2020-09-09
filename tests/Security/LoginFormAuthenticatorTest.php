<?php

namespace Es\CoreBundle\Tests\Security;

use Es\CoreBundle\Entity\User;
use Es\CoreBundle\Security\LoginFormAuthenticator;
use Symfony\Component\HttpFoundation\ParameterBag;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LoginFormAuthenticatorTest extends TestCase
{

    const USER_CLASS = 'Es\CoreBundle\Entity\User';

    const ROUTE_LOGIN_NAME = 'es_core_login';

    const ROUTE_LOGIN = '/login';

    protected $credentials = [
        'username' => "toto",
        'password' => "toto",
        'csrf_token' => "toto"
    ];

    /** 
     * @var LoginFormAuthenticator 
     */
    protected $loginFormAuthenticator;

    /** 
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    protected $entityManager;

    /** 
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    protected $csrfTokenManager;

    /** 
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    protected $passwordEncoder;

    /** 
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    protected $urlGenerator;

    protected function setUp(): void
    {

        $this->csrfTokenManager = $this->getMockBuilder(CsrfTokenManagerInterface::class)->getMock();
        $this->passwordEncoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();
        $this->urlGenerator = $this->getMockBuilder(UrlGeneratorInterface::class)->getMock();
        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $this->repository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();

        $this->entityManager->method('getRepository')
            ->with($this->equalTo(static::USER_CLASS))
            ->will($this->returnValue($this->repository));
        $this->csrfTokenManager->method('isTokenValid')
            ->will($this->returnValue(true));
        $this->loginFormAuthenticator = new LoginFormAuthenticator($this->urlGenerator, $this->entityManager, $this->csrfTokenManager, $this->passwordEncoder);
    }

    public function testSupport()
    {
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $request->method("isMethod")->with("POST")->will($this->returnValue(true));
        $parametersBag = new ParameterBag(["_route" => "es_core_login"]);
        $request->attributes = $parametersBag;
        $result = $this->loginFormAuthenticator->supports($request);

        $this->assertEquals($result, true);
    }

    public function testGetCredentials()
    {
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $session = $this->getMockBuilder(SessionInterface::class)->disableOriginalConstructor()->getMock();
        $session->method("set");
        $request->method("getSession")->will($this->returnValue($session));
        $parametersBag = new ParameterBag($this->credentials);
        $request->request = $parametersBag;

        $result = $this->loginFormAuthenticator->getCredentials($request);
        $this->assertEquals(count($this->credentials), count($result));
    }

    public function testGetUser()
    {
        $user = $this->getUser();
        $this->repository->method("findOneBy")->will($this->returnValue($user));
        $userProvider = $this->getMockBuilder(UserProviderInterface::class)->disableOriginalConstructor()->getMock();
        $result = $this->loginFormAuthenticator->getUser($this->credentials, $userProvider);

        $this->assertInstanceOf(User::class, $result);
    }

    public function testCheckCredentials()
    {
        $this->passwordEncoder->method('isPasswordValid')
            ->with(
                $this->equalTo($this->getUser()),
                $this->equalTo($this->credentials["password"])
            )
            ->will($this->returnValue(true));
        $result = $this->loginFormAuthenticator->checkCredentials($this->credentials, $this->getUser());

        $this->assertEquals($result, true);
    }

    public function testGetPassword()
    {
        $result = $this->loginFormAuthenticator->getPassword($this->credentials);

        $this->assertEquals($result, $this->credentials["password"]);
    }

    public function testOnAuthenticationSuccess()
    {
        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $session = $this->getMockBuilder(SessionInterface::class)->disableOriginalConstructor()->getMock();
        $request->method("getSession")->will($this->returnValue($session));
        $tokenInterface = $this->getMockBuilder(TokenInterface::class)->disableOriginalConstructor()->getMock();
        $this->urlGenerator->method("generate")->with($this->equalTo("home"))->will($this->returnValue("/"));
        $result = $this->loginFormAuthenticator->onAuthenticationSuccess($request, $tokenInterface, "");

        $this->assertInstanceOf(RedirectResponse::class, $result);
    }

    /**
     * @return mixed
     */
    protected function getUser()
    {
        $userClass = static::USER_CLASS;

        return new $userClass();
    }
}
