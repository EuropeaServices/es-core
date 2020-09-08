<?php
namespace ES\CoreBundle\Controller;

use ES\CoreBundle\EsCore;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use ES\CoreBundle\Event\SecurityResponseEvent;
use ES\CoreBundle\Event\EsCoreEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SecurityController extends AbstractController
{
    public function index(EsCore $esCore, EventDispatcherInterface $eventDispatcher = null)
    {
        $data = [
            'paragraphs' => $esCore->getParagraphs(),
            'sentences' => $esCore->getSentences(),
        ];
        $event = new SecurityResponseEvent($data);
        if ($eventDispatcher != null)
            $eventDispatcher->dispatch($event, EsCoreEvents::SECURITY);
        return $this->json($event->getData());
    }
}