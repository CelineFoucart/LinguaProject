<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/category')]
class CategoryAdminController extends AbstractController
{
    public function __construct(
        private SluggerInterface $slugger
    ) {
    }

    #[Route('/', name: 'app_admin_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('/admin/category/index.html.twig', [
            'categories' => $categoryRepository->findBy([], ['position'=>'ASC']),
            'activeCategory' => true,
        ]);
    }

    #[Route('/new', name: 'app_admin_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = strtolower($this->slugger->slug($category->getTitle()));
            $category->setSlug($slug)->setPosition(0);
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_admin_category_show', ['id' => $category->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('/admin/category/new.html.twig', [
            'category' => $category,
            'form' => $form,
            'activeCategory' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('/admin/category/show.html.twig', [
            'category' => $category,
            'activeCategory' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = strtolower($this->slugger->slug($category->getTitle()));
            $category->setSlug($slug);
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_admin_category_show', ['id' => $category->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('/admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
            'activeCategory' => true,
        ]);
    }

    #[Route('/position', name: 'app_api_category_position')]
    public function position(Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $em): JsonResponse
    {
        $categories = $categoryRepository->findAll();
        $data = json_decode($request->getContent(), true);
        $json = [];

        foreach ($categories as $category) {
            $result = array_filter($data, function ($item) use ($category) {
                return $category->getId() === (int) $item['id'];
            });

            if (!empty($result)) {
                $result = array_values($result)[0];
                $position = $result['position'];
                $category->setPosition($position);
                $em->persist($category);
                $json[] = $category->getId();
            }
        }

        $em->flush();

        return $this->json($json, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_admin_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if (!$category->getArticles()->isEmpty()) {
            $this->addFlash('error', "La catégorie {$category->getTitle()} n'a pas pu être supprimée, car elle contient des articles.");
            
            return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);

            $this->addFlash('success', "La catégorie a été supprimée.");
        }

        return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
