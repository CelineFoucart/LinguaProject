<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Form\PageType;
use App\Repository\PageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/page')]
class PageAdminController extends AbstractController
{
    public function __construct(
        private SluggerInterface $slugger
    ) {
    }
    
    #[Route('/', name: 'app_page_admin_index', methods: ['GET'])]
    public function index(PageRepository $pageRepository): Response
    {
        return $this->render('admin/page/index.html.twig', [
            'pages' => $pageRepository->findAll(),
            'activePage' => true,
        ]);
    }

    #[Route('/new', name: 'app_page_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PageRepository $pageRepository): Response
    {
        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = strtolower($this->slugger->slug($page->getTitle()));
            $page->setSlug($slug);
            $pageRepository->save($page, true);

            return $this->redirectToRoute('app_page_admin_show', ['id' => $page->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/page/new.html.twig', [
            'page' => $page,
            'form' => $form,
            'activePage' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_page_admin_show', methods: ['GET'])]
    public function show(Page $page): Response
    {
        return $this->render('admin/page/show.html.twig', [
            'page' => $page,
            'activePage' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_page_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Page $page, PageRepository $pageRepository): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = strtolower($this->slugger->slug($page->getTitle()));
            $page->setSlug($slug);
            $pageRepository->save($page, true);

            return $this->redirectToRoute('app_page_admin_show', ['id' => $page->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/page/edit.html.twig', [
            'page' => $page,
            'form' => $form,
            'activePage' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_page_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Page $page, PageRepository $pageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $pageRepository->remove($page, true);
        }

        return $this->redirectToRoute('app_page_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
