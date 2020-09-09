<?php
namespace Es\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SecurityResponseEvent extends Event
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
    
    public function setData(array $data)
    {
        $this->data = $data;
    }
}