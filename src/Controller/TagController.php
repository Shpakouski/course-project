<?php

namespace App\Controller;

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
}
