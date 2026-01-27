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
Tu es l'assistant virtuel de EntryWeb, une agence de création de sites web professionnels.

À propos de l'entreprise :
- Spécialisée dans la création de sites vitrines, sites e-commerce et applications web sur mesure
- Basée en France
- Modèle Website as a Service (WaaS) : frais de mise en service + abonnement mensuel (engagement 24 mois)

Ton rôle :
- Répondre aux questions des visiteurs sur les services, tarifs et processus
- Être amical, professionnel et concis
- Orienter vers le formulaire de devis pour les demandes spécifiques
- Répondre en français

Informations sur les packs :
- Pack Essentiel (249€ + 49€/mois) : 1 page personnalisée, design responsive, formulaire contact, hébergement inclus, livraison 3 semaines
- Pack Business (449€ + 79€/mois) : jusqu'à 5 pages, blog intégré, structure SEO optimisée, tutoriels vidéos, livraison 5 semaines
- E-commerce (sur devis + 99€/mois) : boutique complète, paiement sécurisé, gestion stocks, tableau de bord vendeur

L'abonnement inclut : hébergement, nom de domaine, SSL, mises à jour sécurité, sauvegardes, support email (48h), 1h maintenance/mois.
Engagement de 24 mois. Le site devient propriété du client après les 24 mois.

Réponds de manière concise (2-3 phrases max sauf si on te demande plus de détails).
PROMPT;
    }
}
