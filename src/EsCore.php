<?php

namespace ES\EsCoreBundle;

class EsCore
{

    private $minSunshine;

    private $wordProvider;

    public function __construct(WordProviderInterface  $wordProvider, $minSunshine = 3)
    {
        $this->wordProvider = $wordProvider;
        $this->minSunshine = $minSunshine;
    }

    public function getMinSunshine() 
    {
        return $this->minSunshine;
    }

    public function getWordList(): array
    {
        return $this->wordProvider->getWordList();
    }
}
