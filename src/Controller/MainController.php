<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(ItemCollectionRepository $itemCollectionRepository, ItemRepository $itemRepository, TagRepository $tagRepository, Request $request): Response
    {

        $user = $this->getUser();
        $tags = $tagRepository->findAll();
        $largestCollections = $itemCollectionRepository->findLargestCollections();
        $recentItems = $itemRepository->findMostRecentItems();

        return $this->render('main/index.html.twig', [
            'largestCollections' => $largestCollections,
            'recentItems' => $recentItems,
            'tags' => $tags,
            'user' => $user,
        ]);
    }
}
