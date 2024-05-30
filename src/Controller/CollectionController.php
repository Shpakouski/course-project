<?php

namespace App\Controller;

use App\Entity\ItemCollection;
use App\Entity\User;
use App\Form\ItemCollectionType;
use App\Repository\ItemCollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('users/{user}')]
class CollectionController extends AbstractController
{
    #[Route('/', name: 'app_collection_list', methods: ['GET'])]
    #[Route('/collections', name: 'app_collection_index', methods: ['GET'])]
    public function index(ItemCollectionRepository $itemCollectionRepository, User $user): Response
    {
        return $this->render('collection/index.html.twig', [
            'user' => $user,
            'collections' => $itemCollectionRepository->findByUser($user),
        ]);


    }

    #[Route('/collections/new', name: 'app_collection_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        $collection = new ItemCollection();
        $form = $this->createForm(ItemCollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collection->setUser($this->getUser());
            $entityManager->persist($collection);
            $entityManager->flush();

            $this->addFlash('success', 'The collection has been successfully created');

            return $this->redirectToRoute('app_collection_index', ['user' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('collection/new.html.twig', [
            'user' => $user,
            'collection' => $collection,
            'form' => $form,
        ]);
    }

    #[Route('/collections/{collection}', name: 'app_collection_show', methods: ['GET'])]
    public function show(User $user, ItemCollection $collection): Response
    {
        return $this->render('collection/show.html.twig', [
            'user' => $user,
            'collection' => $collection,
        ]);
    }

    #[Route('/collections/{collection}/edit', name: 'app_collection_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ItemCollection $collection, EntityManagerInterface $entityManager, User $user): Response
    {
        $form = $this->createForm(ItemCollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collection->setUser($this->getUser());
            $entityManager->flush();

            $this->addFlash('success', 'The collection has been successfully edited');

            return $this->redirectToRoute('app_collection_index', ['user' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('collection/edit.html.twig', [
            'user' => $user,
            'collection' => $collection,
            'form' => $form,
        ]);
    }

    #[Route('/collections/{collection}', name: 'app_collection_delete', methods: ['POST'])]
    public function delete(Request $request, ItemCollection $collection, EntityManagerInterface $entityManager, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $collection->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($collection);
            $entityManager->flush();
        }

        $this->addFlash('danger', 'The collection has been successfully deleted');

        return $this->redirectToRoute('app_collection_index', ['user' => $user->getId()], Response::HTTP_SEE_OTHER);
    }

}
