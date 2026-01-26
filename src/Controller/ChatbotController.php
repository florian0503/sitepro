<?php

namespace App\Controller;

use App\Service\GroqService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class ChatbotController extends AbstractController
{
    #[Route('/api/chat', name: 'api_chat', methods: ['POST'])]
    public function chat(Request $request, GroqService $groqService, SessionInterface $session): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userMessage = $data['message'] ?? '';

        if (empty($userMessage)) {
            return new JsonResponse(['error' => 'Message vide'], 400);
        }

        // Récupérer l'historique de la session
        $history = $session->get('chat_history', []);

        // Ajouter le message utilisateur
        $history[] = ['role' => 'user', 'content' => $userMessage];

        // Limiter l'historique aux 10 derniers messages
        if (count($history) > 10) {
            $history = array_slice($history, -10);
        }

        // Obtenir la réponse de Groq
        $response = $groqService->chat($history);

        // Ajouter la réponse à l'historique
        $history[] = ['role' => 'assistant', 'content' => $response];

        // Sauvegarder l'historique
        $session->set('chat_history', $history);

        return new JsonResponse([
            'response' => $response,
        ]);
    }

    #[Route('/api/chat/clear', name: 'api_chat_clear', methods: ['POST'])]
    public function clearHistory(SessionInterface $session): JsonResponse
    {
        $session->remove('chat_history');

        return new JsonResponse(['success' => true]);
    }
}
