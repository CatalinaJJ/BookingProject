<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage() {
        $user = $this->getUser();
        if ($user) {
            return $this->render('homepage/homepage.html.twig', [
                'user' => $user->getFirstName(),
            ]);
        }
        else {
            return new RedirectResponse('/login');
        }
    }
}