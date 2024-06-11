<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ItemCollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('users')]
class UserController extends AbstractController
{
    public function __construct(private Security $security, private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    #[Route('/{user}', name: 'app_user_show', methods: ['GET'])]
    public function show(ItemCollectionRepository $itemCollectionRepository, User $user): Response
    {

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'collections' => $itemCollectionRepository->findByUser($user),
            'apiToken' => $user->getApiToken(),
        ]);
    }

    #[Route('/generate-api-token', name: 'generate_api_token', methods: ['POST'])]
    public function generateApiToken(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $token = bin2hex(random_bytes(32));
        $user->setApiToken($token);
        $this->entityManager->flush();

        return new JsonResponse(['apiToken' => $token]);
    }
}
