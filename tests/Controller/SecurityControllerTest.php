<?php
namespace ES\EsCore\Tests\Controller;

use ES\CoreBundle\EsCoreBundle;
use ES\CoreBundle\Controller\SecurityController;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;

class SecurityControllerTest extends TestCase
{
    public function testIndex()
    {
        $kernel = new SecurityControllerKernel();
        $client = new KernelBrowser($kernel);
        $client->request('GET', '/es_login');
        var_dump($client->getResponse()->getContent());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}

class SecurityControllerKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', true);
    }
    public function registerBundles()
    {
        return [
            new EsCoreBundle(),
            new FrameworkBundle(),
        ];
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes->import(__DIR__.'/../../src/Resources/config/routes.xml', '/');
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->loadFromExtension('framework', [
            'secret' => '$ecretf0rt3st',
        ]);
    }

    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment."/".spl_object_hash($this);
    }
}