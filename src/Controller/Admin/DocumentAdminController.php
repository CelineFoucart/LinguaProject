<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Document;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/document')]
class DocumentAdminController extends AbstractController
{
    #[Route('/', name: 'app_document_admin_index', methods: ['GET'])]
    public function index(DocumentRepository $documentRepository): Response
    {
        return $this->render('admin/document/index.html.twig', [
            'documents' => $documentRepository->findAll(),
            'activeDocument' => true,
        ]);
    }

    #[Route('/new/article/{id}', name: 'app_document_admin_new', methods: ['GET', 'POST'])]
    public function new(Article $article, Request $request, DocumentRepository $documentRepository): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $document->setCreatedAt(new DateTimeImmutable())->setArticle($article);
            $documentRepository->save($document, true);

            return $this->redirectToRoute('app_document_admin_show', ['id' => $document->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/document/new.html.twig', [
            'document' => $document,
            'form' => $form,
            'activeDocument' => true,
            'article' => $article,
        ]);
    }

    #[Route('/{id}', name: 'app_document_admin_show', methods: ['GET'])]
    public function show(Document $document): Response
    {
        return $this->render('admin/document/show.html.twig', [
            'document' => $document,
            'activeDocument' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_document_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Document $document, DocumentRepository $documentRepository): Response
    {
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $document->setUpdatedAt(new DateTime());
            $documentRepository->save($document, true);

            return $this->redirectToRoute('app_document_admin_show', ['id' => $document->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/document/edit.html.twig', [
            'document' => $document,
            'form' => $form,
            'activeDocument' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_document_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Document $document, DocumentRepository $documentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->request->get('_token'))) {
            $documentRepository->remove($document, true);

            $this->addFlash('success', "Le document a été supprimé.");
        }

        return $this->redirectToRoute('app_document_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
