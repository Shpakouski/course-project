<?php

namespace App\Controller;

use App\Entity\ItemCollection;
use App\Form\ItemCollectionType;
use App\Repository\ItemCollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/collections')]
class CollectionController extends AbstractController
{
    #[Route('/', name: 'app_collection_index', methods: ['GET'])]
    public function index(ItemCollectionRepository $itemCollectionRepository): Response
    {
        return $this->render('collection/index.html.twig', [
            'item_collections' => $itemCollectionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_collection_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $itemCollection = new ItemCollection();
        $form = $this->createForm(ItemCollectionType::class, $itemCollection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($itemCollection);
            $entityManager->flush();

            $this->addFlash('success', 'The collection has been successfully created');

            return $this->redirectToRoute('app_collection_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('collection/new.html.twig', [
            'item_collection' => $itemCollection,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_collection_show', methods: ['GET'])]
    public function show(ItemCollection $itemCollection): Response
    {
        return $this->render('collection/show.html.twig', [
            'item_collection' => $itemCollection,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_collection_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ItemCollection $itemCollection, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ItemCollectionType::class, $itemCollection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'The collection has been successfully edited');

            return $this->redirectToRoute('app_collection_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('collection/edit.html.twig', [
            'item_collection' => $itemCollection,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_collection_delete', methods: ['POST'])]
    public function delete(Request $request, ItemCollection $itemCollection, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $itemCollection->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($itemCollection);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_collection_index', [], Response::HTTP_SEE_OTHER);
    }
}
