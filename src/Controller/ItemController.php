<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Entity\User;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('users/{user}/collections/{collection}')]
class ItemController extends AbstractController
{
    #[Route('/items', name: 'app_item_index', methods: ['GET'])]
    public function index(ItemRepository $itemRepository, User $user, ItemCollection $collection, Request $request, PaginatorInterface $paginator): Response
    {

        $query = $itemRepository->findByUserAndCollection($user, $collection);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('item/index.html.twig', [
            'user' => $user,
            'collection' => $collection,
            'items' => $itemRepository->findByItemCollection($collection),
            'pagination' => $pagination
        ]);
    }

    #[Route('/items/new', name: 'app_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ItemCollection $collection, User $user): Response
    {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($item);
            $entityManager->flush();

            return $this->redirectToRoute('app_item_index', ['user' => $user->getId(), 'collection' => $collection->getId(),], Response::HTTP_SEE_OTHER);
        }

        return $this->render('item/new.html.twig', [
            'user' => $user,
            'collection' => $collection,
            'item' => $item,
            'form' => $form,
        ]);
    }

    #[Route('/items/{item}', name: 'app_item_show', methods: ['GET'])]
    public function show(Item $item, ItemCollection $collection, User $user): Response
    {
        return $this->render('item/show.html.twig', [
            'user' => $user,
            'collection' => $collection,
            'item' => $item,
        ]);
    }

    #[Route('/items/{item}/edit', name: 'app_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Item $item, EntityManagerInterface $entityManager, ItemCollection $collection, User $user): Response
    {
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_item_index', ['user' => $user->getId(), 'collection' => $collection->getId(),], Response::HTTP_SEE_OTHER);
        }

        return $this->render('item/edit.html.twig', [
            'user' => $user,
            'collection' => $collection,
            'item' => $item,
            'form' => $form,
        ]);
    }

    #[Route('/items/{item}', name: 'app_item_delete', methods: ['POST'])]
    public function delete(Request $request, Item $item, EntityManagerInterface $entityManager, ItemCollection $collection, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($item);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_item_index', ['user' => $user->getId(), 'collection' => $collection->getId(),], Response::HTTP_SEE_OTHER);
    }
}
