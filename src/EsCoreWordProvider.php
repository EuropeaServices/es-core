<?php

namespace ES\EsCoreBundle;

class EsCoreWordProvider implements WordProviderInterface
{
    public function getWordList(): array
    {
        return [
            'adorable',
            'active',
            'admire',
            'adventurous',
            'butterflies',
            'cupcakes',
            'sprinkles',
            'glitter',
            'friend',
            'high-five',
            'friendship',
            'compliments',
            'sunsets',
            'cookies',
            'flowers',
            'bikes',
            'kittens',
            'puppies',
            'macaroni',
            'freckles',
            'baguettes',
            'presents',
            'fireworks',
            'chocholate',
            'marshmallow',
        ];
    }
}
