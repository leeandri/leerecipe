<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    /**
     * This controller allow us to edit user info
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/user/edit/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('recipe.index');
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())) {
                $user = $form->getData();

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Your account informations was modified successfully !'
                );

                return $this->redirectToRoute('recipe.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Password incorrect.'
                );
            }
        }
        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/user/password-edit/{id}', name: 'user.password.edit', methods: ['GET', 'POST'])]
    public function editPassword(User $user, Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('recipe.index');
        }
        $form = $this->createForm(UserPasswordType::class, $user);

        $form->handleRequest($request);

        $newPassword = $form->getData()->getPlainPassword();

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $hasher->hashPassword(
                    $user,
                    $newPassword
                )
            );

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Your password was modified successfully !'
            );

            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
