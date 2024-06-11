<?php

namespace App\Controller;

use App\Repository\ItemRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TagController extends AbstractController
{
    #[Route('/tags/autocomplete', name: 'app_tags_autocomplete')]
    public function index(Request $request, TagRepository $tagRepository): JsonResponse
    {
        $term = $request->query->get('term');
        $tags = $tagRepository->findByTerm($term);

        $tagNames = array_map(function ($tag) {
            return $tag->getName();
        }, $tags);

        return new JsonResponse($tagNames);
    }

    #[Route('/tags', name: 'app_tag_cloud')]
    public function tagCloud(TagRepository $tagRepository): Response
    {
        $tags = $tagRepository->findAllWithItemCounts();

        return $this->render('tag/cloud.html.twig', [
            'tags' => $tags,
        ]);
    }

    #[Route('/tags/{id}', name: 'app_tag_items')]
    public function itemsByTag(int $id, TagRepository $tagRepository, ItemRepository $itemRepository): Response
    {
        $tag = $tagRepository->find($id);

        if (!$tag) {
            throw $this->createNotFoundException('Tag not found');
        }

        $items = $itemRepository->findByTag($tag);

        return $this->render('tag/items.html.twig', [
            'tag' => $tag,
            'items' => $items,
        ]);
    }
}
