<?php


namespace App\AppBundle\EventListener;


use App\Entity\Login;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AuthenticationEventListener
{
    private $doctrine;

    private $request;

    private $entityManager;

    public function __construct($doctrine, $request, EntityManagerInterface $entityManager)
    {
        $this->doctrine = $doctrine;
        $this->request = $request;
        $this->entityManager = $entityManager;
    }

    private function saveLoginAttempt($email) {
        $login = new Login();
        $login->setEmail($email);
        $login->setIp($this->request->getCurrentRequest()->getClientIp());

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($login);
        $entityManager->flush();
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event) {
        $email = $event->getAuthenticationToken()->getCredentials()['email'];
        $this->saveLoginAttempt($email);
    }

    /**
     * onAuthenticationSuccess
     *
     * @param InteractiveLoginEvent $event
     */
    public function onAuthenticationSuccess(InteractiveLoginEvent $event)
    {
        $username = $event->getAuthenticationToken()->getUsername();
        $failedAttempts = $this->entityManager->getRepository(Login::class)->findBy(['email' => $username]);
        foreach ($failedAttempts as $attempt ) {
            $this->entityManager->remove($attempt);
        }
        $this->entityManager->flush();
    }


}