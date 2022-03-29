<?php

namespace Es\CoreBundle\Mailer;

use Symfony\Component\Mime\Email;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mime\Address;

class CoreEmail extends Email
{
  
    private string $env;

    public function __construct(KernelInterface $kernel, private string $mailToDev, string $mailFrom)
    {
        parent::__construct();
        $this->env = $kernel->getEnvironment();
        $this->from($mailFrom);
    }

    /**
     * @param Address|string ...$addresses
     *
     * @return $this
     */
    public function to(...$addresses): static
    {
       if ($this->env != "prod"){
            return parent::to($this->mailToDev);
        }
        if (is_array($addresses)){
            foreach($addresses as $address){
                $this->addTo($address);
            }
            return $this;
        }

        return parent::to($addresses);
    }

}