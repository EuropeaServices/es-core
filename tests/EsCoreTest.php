<?php
namespace ES\CoreBundle\Tests;

use ES\CoreBundle\EsCore;
use ES\CoreBundle\EsCoreWordProvider;
use PHPUnit\Framework\TestCase;

class EsCoreTest extends TestCase
{
    public function testGetWords()
    {
        $ipsum = new EsCore([new EsCoreWordProvider()]);
        $words = $ipsum->getWords(1);
        $this->assertIsString($words);
        $this->assertCount(1, explode(' ', $words));
        $words = $ipsum->getWords(10);
        $this->assertCount(10, explode(' ', $words));
        $words = $ipsum->getWords(10, true);
        $this->assertCount(10, $words);
    }
}