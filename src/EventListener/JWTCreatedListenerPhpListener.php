<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;


class JWTCreatedListenerPhpListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        // Récupérer l'utilisateur pour obtenir ses informations
        $user = $event->getUser();
        
        // Créer un tableau avec les informations utilisateur
        $userData = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'pseudo' => $user->getPseudo(),
            'roles' => $user->getRoles(),
            'thumbnail' => $user->getThumbnail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'birthdate' => $user->getBirthdate(),
            'city' => $user->getCity(),
            'createdAt' => $user->getCreatedAt(),
        ];
        // Créer un tableau avec le token JWT et les informations utilisateur
        $responseArray = [
            'token' => $event->getData(),
            'user' => $userData,
        ];

        // Convertir le tableau en chaîne JSON
        $responseJson = json_encode($responseArray);

        // Retourner la chaîne JSON
        return $responseJson;
    }
}
