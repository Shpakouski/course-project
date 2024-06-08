<?php

namespace App\Controller;

use App\Repository\ItemCollectionRepository;
use Pagerfanta\Adapter\ArrayAdapter;
use App\Form\TicketType;
use App\Service\JiraService;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class TicketController extends AbstractController
{
    public const ISSUE_TYPE = 'Ticket';

    public function __construct(private string $projectKey, private string $baseTicketUrl)
    {
    }

    #[Route('/tickets/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, JiraService $jiraService, SessionInterface $session, ItemCollectionRepository $collectionRepository): Response
    {
        if ($request->isMethod('GET')) {
            $link = $request->headers->get('referer');
            $session->set('referer_link', $link);
        }
        $form = $this->createForm(TicketType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $userEmail = $this->getUser()->getEmail();
            $link = $session->get('referer_link');
            $accountId = $jiraService->getAccountIdByEmail($userEmail);
            if (!$accountId) {
                $user = $jiraService->createUser($userEmail);
                $accountId = $user['accountId'];
            }
            $collection = $this->determineCollection($link, $collectionRepository);
            $jiraPayload = [
                'fields' => [
                    'project' => [
                        'key' => $this->projectKey,
                    ],
                    'summary' => $data['summary'],
                    'description' => $data['description'],
                    'issuetype' => [
                        'name' => self::ISSUE_TYPE,
                    ],
                    'priority' => [
                        'name' => $data['priority']->value,
                    ],
                    'reporter' => [
                        'accountId' => $accountId,
                    ],
                    'customfield_10041' => $collection,
                    'customfield_10043' => $link,
                ],
            ];
            try {
                $jiraResponse = $jiraService->createTicket($jiraPayload);
                $this->addFlash('success', 'Ticket created successfully: ' . $jiraResponse['key']);
                return $this->redirectToRoute('app_ticket_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Failed to create Jira ticket: ' . $e->getMessage());
            }

        }

        return $this->render('ticket/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tickets', name: 'app_ticket_index', methods: ['GET'])]
    public function index(JiraService $jiraService, Request $request): Response
    {
        $userEmail = $this->getUser()->getEmail();

        try {
            $issues = $jiraService->getTicketsByReporter($userEmail);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to retrieve tickets: ' . $e->getMessage());
            $issues = [];
        }

        $adapter = new ArrayAdapter($issues);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));

        return $this->render('ticket/index.html.twig', [
            'pager' => $pagerfanta,
            'baseTicketUrl' => $this->baseTicketUrl,
        ]);
    }

    private function determineCollection(string $link, ItemCollectionRepository $collectionRepository): ?string
    {
        $parsedUrl = parse_url($link);
        $path = $parsedUrl['path'] ?? '';
        $segments = explode('/', trim($path, '/'));

        foreach ($segments as $index => $segment) {
            if ($segment === 'collections' && isset($segments[$index + 1])) {
                $collectionId = (int)$segments[$index + 1];
                return $collectionRepository->findCollectionNameById($collectionId);
            }
        }

        return null;
    }
}
