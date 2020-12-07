<?php

namespace Es\CoreBundle\Mailer;

use Symfony\Component\Mime\Email;
use Symfony\Component\HttpKernel\KernelInterface;

class CoreEmail extends Email
{
    private $mailToDev;
    
    private $env;

    public function __construct(KernelInterface $kernel, string $mailToDev, string $mailFrom)
    {
        parent::__construct();
        $this->mailToDev = $mailToDev;
        $this->env = $kernel->getEnvironment();
        $this->from($mailFrom);
    }

    /**
     * @param Address|string ...$addresses
     *
     * @return $this
     */
    public function to(...$addresses)
    {
        if ($this->env != "prod"){
            return parent::to($this->mailToDev);
        }

        if (is_array($addresses)){
            return parent::to($addresses[0]);
        }

        return parent::to($addresses);
    }

}