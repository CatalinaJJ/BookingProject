<?php

namespace App\Controller;

use App\Entity\Login;
use App\Form\LoginType;
use App\Repository\LoginRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/loginattempts")
 */
class LoginController extends AbstractController
{
    /**
     * @Route("/", name="login_index", methods={"GET"})
     */
    public function index(LoginRepository $loginRepository): Response
    {
        return $this->render('login/index.html.twig', [
            'logins' => $loginRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="login_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $login = new Login();
        $form = $this->createForm(LoginType::class, $login);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($login);
            $entityManager->flush();

            return $this->redirectToRoute('login_index');
        }

        return $this->render('login/new.html.twig', [
            'login' => $login,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="login_show", methods={"GET"})
     */
    public function show(Login $login): Response
    {
        return $this->render('login/show.html.twig', [
            'login' => $login,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="login_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Login $login): Response
    {
        $form = $this->createForm(LoginType::class, $login);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('login_index');
        }

        return $this->render('login/edit.html.twig', [
            'login' => $login,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="login_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Login $login): Response
    {
        if ($this->isCsrfTokenValid('delete'.$login->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($login);
            $entityManager->flush();
        }

        return $this->redirectToRoute('login_index');
    }
}
