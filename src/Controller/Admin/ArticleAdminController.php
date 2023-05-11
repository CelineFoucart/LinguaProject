<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/admin/article')]
class ArticleAdminController extends AbstractController
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    #[Route('/', name: 'app_admin_article_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/article/index.html.twig', [ 'activeArticle' => true]);
    }

    #[Route('/json', name: 'app_api_article_index', methods: ['GET'])]
    public function indexApi(ArticleRepository $articleRepository, Request $request)
    {
        $parameters = $request->query->all();
        $articles = $articleRepository->searchPaginatedItems($parameters);
        $recordsTotal = $articleRepository->count([]);
        $recordsFiltered = $articleRepository->countSearchTotal($parameters);

        $data = [
            'draw' => isset($parameters['draw']) ? (int)$parameters['draw'] : 0,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => isset($recordsFiltered['recordsFiltered']) ? $recordsFiltered['recordsFiltered'] : 0,
            "data" => $articles,
        ];

        return $this->json($data,200, [], ['groups' => 'index']);
    }

    #[Route('/new', name: 'app_admin_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleRepository $articleRepository, CategoryRepository $categoryRepository): Response
    {
        $article = new Article();
        $categoryId = $request->query->getInt('category');

        if ($categoryId > 0) {
            $category = $categoryRepository->find($categoryId);
            $article->setCategory($category);
        }
        
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = strtolower($this->slugger->slug($article->getTitle()));
            $article->setSlug($slug)->setCreatedAt(new DateTimeImmutable())->setAuthor($this->getUser());
            $articleRepository->save($article, true);

            return $this->redirectToRoute('app_admin_article_show', ['id' => $article->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/article/new.html.twig', [
            'article' => $article,
            'form' => $form,
            'activeArticle' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('admin/article/show.html.twig', [
            'article' => $article,
            'activeArticle' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = strtolower($this->slugger->slug($article->getTitle()));
            $article->setSlug($slug)->setUpdatedAt(new DateTime());
            $articleRepository->save($article, true);

            return $this->redirectToRoute('app_admin_article_show', ['id' => $article->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
            'activeArticle' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
            $this->addFlash('success', "L'article a été supprimé.");
        }

        return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
