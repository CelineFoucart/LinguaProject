<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route(path: '/profile', name: 'app_profile')]
    public function profile(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response
    {
        /** @var User */
        $user = $this->getUser();

        if (null === $user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $user->setUpdatedAt(new DateTime());
            $userRepository->save($user, true);
            $this->addFlash('success', 'Les modifications ont été enregistrées.');

            return $this->redirectToRoute('app_profile');
        }

        $passwordForm = $this->createForm(ChangePasswordType::class, $user);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $plainPassword = $passwordForm->get('plainPassword')->getData();
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $plainPassword
                )
            );

            $user->setUpdatedAt(new DateTime());
            $userRepository->save($user, true);
            $this->addFlash('success', 'Le mot de passe a été modifié.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'passwordForm' => $passwordForm->createView(),
        ]);
    }
}