<?php

namespace App\EventSubscriber;

use App\Entity\LoginEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener(event: LoginSuccessEvent::class, method: 'onLoginSuccess')]
class LoginSuccessSubscriber
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        $login = new LoginEvent($user);
        $this->em->persist($login);
        $this->em->flush();
    }
}
