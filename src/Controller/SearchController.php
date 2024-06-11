<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(Request $request, ItemRepository $itemRepository): Response
    {
        $form = $this->createForm(SearchType::class);
        $results = $this->processSearchForm($form, $request, $itemRepository);

        return $this->render('search/index.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
        ]);
    }

    #[Route('/searchform', name: 'app_generate_search')]
    public function generateSearchForm(Request $request, ItemRepository $itemRepository): Response
    {
        $form = $this->createForm(SearchType::class);
        $results = $this->processSearchForm($form, $request, $itemRepository);

        return $this->render('search/_form.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
        ]);
    }

    private function processSearchForm($form, $request, $itemRepository)
    {
        $form->handleRequest($request);
        $results = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->get('query')->getData();
            if ($query) {
                $results = $itemRepository->fullTextSearch($query);
                $results = array_map("unserialize", array_unique(array_map("serialize", $results)));
            }
        }

        return $results;
    }
}
