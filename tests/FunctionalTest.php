<?php
namespace ES\CoreBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use ES\CoreBundle\EsCoreBundle;
use ES\CoreBundle\EsCore;
use ES\CoreBundle\WordProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FunctionalTest extends TestCase
{
    public function testServiceWiring()
    {
        $kernel = new EsCoreTestingKernel();
        $kernel->boot();
        $container = $kernel->getContainer();
        $ipsum = $container->get('es_core.es_core');
        $this->assertInstanceOf(EsCore::class, $ipsum);
        $this->assertIsString($ipsum->getParagraphs());
    }

    public function testServiceWiringWithConfiguration()
    {
        $kernel = new EsCoreTestingKernel([
            'word_provider' => 'stub_word_list'
        ]);
        $kernel->boot();
        $container = $kernel->getContainer();
        $ipsum = $container->get('es_core.es_core');
        $this->assertEquals(count($ipsum->getWordList()), 27);

    }
}

class EsCoreTestingKernel extends Kernel
{

    private $esCoreConfig;


    public function __construct(array $esCoreConfig = [])
    {
        $this->esCoreConfig = $esCoreConfig;
        parent::__construct('test', true);
    }


    public function registerBundles()
    {
        return [
            new EsCoreBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function(ContainerBuilder $container) {
            $container->register('stub_word_list', StubWordList::class)
                ->addTag('es_core_word_provider');
        });
    }

    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment."/".spl_object_hash($this);
    }
}

class StubWordList implements WordProviderInterface
{
    public function getWordList(): array
    {
        return ['stub', 'stub2'];
    }
}