<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GroqService
{
    private const API_URL = 'https://api.groq.com/openai/v1/chat/completions';
    private const MODEL = 'llama-3.1-8b-instant';

    private string $apiKey;
    private HttpClientInterface $httpClient;

    public function __construct(
        string $groqApiKey,
        HttpClientInterface $httpClient
    ) {
        $this->apiKey = $groqApiKey;
        $this->httpClient = $httpClient;
    }

    /**
     * @param array<int, array{role: string, content: string}> $messages
     */
    public function chat(array $messages): string
    {
        $systemPrompt = $this->getSystemPrompt();

        $allMessages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $messages
        );

        try {
            $response = $this->httpClient->request('POST', self::API_URL, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => self::MODEL,
                    'messages' => $allMessages,
                    'max_tokens' => 1024,
                    'temperature' => 0.7,
                ],
            ]);

            $data = $response->toArray();

            return $data['choices'][0]['message']['content'] ?? 'Désolé, je n\'ai pas pu générer de réponse.';
        } catch (\Exception $e) {
            return 'Désolé, une erreur est survenue. Veuillez réessayer plus tard.';
        }
    }

    private function getSystemPrompt(): string
    {
        return <<<PROMPT
Tu es l'assistant virtuel de WebDesignPro, une agence de création de sites web professionnels.

À propos de l'entreprise :
- Spécialisée dans la création de sites vitrines, sites e-commerce et applications web sur mesure
- Basée en France
- Offres : Pack Démarrage (499€), Pack Entreprise (1290€), E-commerce & Sur-Mesure (à partir de 2500€)

Ton rôle :
- Répondre aux questions des visiteurs sur les services, tarifs et processus
- Être amical, professionnel et concis
- Orienter vers le formulaire de devis pour les demandes spécifiques
- Répondre en français

Informations sur les packs :
- Pack Démarrage (499€) : 1 page, design responsive, formulaire contact, hébergement 1 an, livraison 7 jours
- Pack Entreprise (1290€) : jusqu'à 5 pages, blog, SEO, galerie, support 90 jours, livraison 14 jours
- E-commerce (sur devis, dès 2500€) : boutique complète, paiement en ligne, gestion stocks, support 6 mois

Réponds de manière concise (2-3 phrases max sauf si on te demande plus de détails).
PROMPT;
    }
}
