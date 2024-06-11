<?php

namespace App\Controller;

use App\Entity\AttributeValue;
use App\Entity\Comment;
use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Entity\Like;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\ItemType;
use App\Repository\CommentRepository;
use App\Repository\ItemRepository;
use App\Repository\LikeRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $this->denyAccessUnlessGranted('create', $item);

        $item->setItemCollection($collection);

        $form = $this->createForm(ItemType::class, $item, [
            'collection' => $collection,
            'item' => $item,
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
    public function show(Item $item, ItemCollection $collection, User $user, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy(['item' => $item], ['createdAt' => '

DESC']);
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('app_item_comment', ['user' => $user->getId(), 'collection' => $collection->getId(), 'id' => $item->getId()]),
            'method' => 'POST',
        ]);

        return $this->render('item/show.html.twig', [
            'user' => $user,
            'collection' => $collection,
            'item' => $item,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    #[Route('/items/{item}/edit', name: 'app_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Item $item, EntityManagerInterface $entityManager, ItemCollection $collection, User $user, ItemRepository $itemRepository, TagRepository $tagRepository): Response
    {
        $this->denyAccessUnlessGranted('edit', $item);
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
        $this->denyAccessUnlessGranted('delete', $item);
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($item);
            $entityManager->flush();
        }

        $this->addFlash('danger', 'The item has been successfully deleted');

        return $this->redirectToRoute('app_item_index', ['user' => $user->getId(), 'collection' => $collection->getId(),], Response::HTTP_SEE_OTHER);
    }


    #[Route('/item/{id}/comment', name: 'app_item_comment', methods: ['POST'])]
    public function addComment(Item $item, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setItem($item);
            $comment->setUser($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();

            return new JsonResponse([
                'status' => 'success',
                'content' => $comment->getContent(),
                'author' => $comment->getUser()->getEmail(),
                'createdAt' => $comment->getCreatedAt()->format('Y-m-d H:i:s')
            ]);
        }

        return new JsonResponse(['status' => 'error'], 400);
    }

    #[Route('item/{id}/like', name: 'app_item_like', methods: ['POST'])]
    public function like(Item $item, LikeRepository $likeRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        $like = $likeRepository->findOneBy(['item' => $item, 'user' => $user]);

        if (!$like) {
            $like = new Like();
            $like->setItem($item);
            $like->setUser($user);
            $entityManager->persist($like);
            $entityManager->flush();

            return new JsonResponse(['status' => 'liked']);
        }

        return new JsonResponse(['status' => 'already_liked'], 400);
    }

}
