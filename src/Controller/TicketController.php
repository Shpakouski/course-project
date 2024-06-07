<?php

namespace App\Controller;

use Pagerfanta\Adapter\ArrayAdapter;
use App\Form\TicketType;
use App\Service\JiraService;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TicketController extends AbstractController
{
    public function __construct(private string $projectKey)
    {
    }

    #[Route('/tickets/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, JiraService $jiraService): Response
    {
        $form = $this->createForm(TicketType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $userEmail = $this->getUser()->getEmail();
            $link = $request->headers->get('referer');

            if (!$jiraService->userExists($userEmail)) {
                $jiraService->createUser($userEmail, $this->getUser()->getUsername());
            }

            $accountId = $jiraService->getAccountIdByEmail($userEmail);

            $jiraPayload = [
                'fields' => [
                    'project' => [
                        'key' => $this->projectKey,
                    ],
                    'summary' => $data['summary'],
                    'description' => $data['description'],
                    'issuetype' => [
                        'name' => 'Ticket',
                    ],
                    'priority' => [
                        'name' => $data['priority']->value,
                    ],
                    'reporter' => [
                        'accountId' => $accountId,
                    ],
                    'customfield_10041' => 'Collection name if applicable', // Replace with actual custom field ID
                    'customfield_10042' => $link, // Replace with actual custom field ID
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
        ]);
    }
}
