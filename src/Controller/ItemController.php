<?php

namespace App\Controller;

use App\Entity\AttributeValue;
use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Entity\Tag;
use App\Entity\User;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use App\Repository\TagRepository;
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
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('item/index.html.twig', [
            'user' => $user,
            'collection' => $collection,
            'items' => $itemRepository->findByItemCollection($collection),
            'pagination' => $pagination
        ]);
    }

    #[Route('/items/new', name: 'app_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ItemCollection $collection, User $user, ItemRepository $itemRepository, TagRepository $tagRepository): Response
    {
        $item = new Item();

        $item->setItemCollection($collection);

        $form = $this->createForm(ItemType::class, $item, [
            'collection' => $collection,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item->setItemCollection($collection);
            $item->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($item);

            $itemRepository->createCustomAttributes($item, $collection, $form, $entityManager);

            $tagRepository->addTags($form, $entityManager, $item);

            $entityManager->flush();

            $this->addFlash('success', 'The item has been successfully created');

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
    public function edit(Request $request, Item $item, EntityManagerInterface $entityManager, ItemCollection $collection, User $user, ItemRepository $itemRepository, TagRepository $tagRepository): Response
    {
        $attributes = $entityManager->getRepository(AttributeValue::class)->findBy(['item' => $item]);
        $attributeValues = [];
        foreach ($attributes as $attribute) {
            $fieldName = AttributeValue::ATTR_PREFIX . $attribute->getCustomAttribute()->getId();
            $attributeValues[$fieldName] = $attribute->getValue($attribute->getCustomAttribute()->getType()->value);
        }
        $form = $this->createForm(ItemType::class, $item, [
            'collection' => $collection,
            'attributes' => $attributeValues,
            'item' => $item,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $itemRepository->editCustomAttributes($item, $collection, $form, $entityManager);
            $item->setItemCollection($collection);

            $tagRepository->addTags($form, $entityManager, $item);

            $entityManager->flush();

            $this->addFlash('success', 'The item has been successfully edited');

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

        $this->addFlash('danger', 'The item has been successfully deleted');

        return $this->redirectToRoute('app_item_index', ['user' => $user->getId(), 'collection' => $collection->getId(),], Response::HTTP_SEE_OTHER);
    }
}
