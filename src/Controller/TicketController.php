<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Service\JiraService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class TicketController extends AbstractController
{
    private $jiraService;

    public function __construct(JiraService $jiraService)
    {
        $this->jiraService = $jiraService;
    }

    #[Route('/tickets/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserInterface $user)
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $collection = 'Example Collection'; // Adjust according to your logic
            $link = $request->headers->get('referer');
            $response = $this->jiraService->createTicket(
                $data->getSummary(),
                $data->getPriority(),
                $user->getEmail(),
                $collection,
                $link
            );

            // Display the JIRA ticket link
            $this->addFlash('success', 'Ticket created: ' . $response['self']);

            return $this->redirectToRoute('app_ticket_new');
        }

        return $this->render('ticket/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tickets', name: 'app_ticket_index', methods: ['GET'])]
    public function index(UserInterface $user): Response
    {
        // Fetch user's tickets from JIRA using JIRA API and display them
        // You can implement pagination logic here as well
        return $this->render('ticket/index.html.twig', [
            // 'tickets' => $tickets,
        ]);
    }
}
