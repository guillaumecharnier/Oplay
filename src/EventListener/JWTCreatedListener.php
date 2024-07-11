<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $payload = $event->getData();
        $payload['id'] = $user->getId(); // Ajoute l'ID de l'utilisateur dans la charge utile

        $event->setData($payload);
    }
}
