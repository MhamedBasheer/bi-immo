<?php

namespace App\Services;
use App\Entity\Announcement;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtSubscriber
{
    public function updateJwtData(JWTCreatedEvent $event)
    {
        $user = $event->getUser();
        foreach($user->getFavoris()->getValues() as $fav){
            $user->addFavori($fav);
        }
        $data = $event->getData();
        $data['favoris'] = array_map(
            function(Announcement $announcement){
                return "api/bi-immo/announcement/".$announcement -> getId();
            },
            $user->getFavoris()->getValues()
        );
        foreach(['id', 'email', 'firstname', 'lastname', 'phoneNumber'] as $value){
            $getter = "get".ucfirst($value);
            $data[$value] = $user->$getter();
        }
        $data['role'] = $user->getRole()->getName();
        $event->setData($data);
    }
    
}
