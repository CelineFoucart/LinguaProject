<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\User;
use App\Form\UserType;
use DateTimeImmutable;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/user')]
class UserAdminController extends AbstractController
{
    #[Route('/', name: 'app_user_admin_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'activeUser' => true,
        ]);
    }

    #[Route('/new', name: 'app_user_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $user->setCreatedAt(new DateTimeImmutable());
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $plainPassword
                )
            );

            $userRepository->save($user, true);
            return $this->redirectToRoute('app_user_admin_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/new.html.twig', [
            'form' => $form,
            'activeUser' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_user_admin_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
            'activeUser' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_admin_edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $user,'Access Denied.');

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword && strlen($plainPassword) > 0) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $plainPassword
                    )
                );
            }
            
            $user->setUpdatedAt(new DateTime());
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_admin_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'activeUser' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_user_admin_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository, RoleHierarchyInterface $roleHierarchy): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $user,'Access Denied.');

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
            $this->addFlash('success', "L'utilisateur a bien été supprimé.");
        } else {
            $this->addFlash( 'error',"Le jeton CSRF est invalide.");
        }

        return $this->redirectToRoute('app_user_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}