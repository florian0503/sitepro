<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Article;
use App\Repository\NewsletterSubscriberRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NewsletterMailerService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly NewsletterSubscriberRepository $subscriberRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        #[Autowire('%env(CONTACT_EMAIL)%')]
        private readonly string $contactEmail,
    ) {
    }

    public function sendNewArticleNotification(Article $article): int
    {
        $subscribers = $this->subscriberRepository->findAll();

        if (0 === count($subscribers)) {
            return 0;
        }

        $articleUrl = $this->urlGenerator->generate(
            'app_blog_show',
            ['slug' => $article->getSlug()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $safeTitle = htmlspecialchars($article->getTitle() ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safeExcerpt = htmlspecialchars($article->getExcerpt() ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safeCategory = htmlspecialchars($article->getCategory() ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $readTime = $article->getReadTime();

        $htmlContent = <<<HTML
        <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff;">
            <div style="background: #0066ff; padding: 32px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">EntryWeb</h1>
                <p style="color: #cce0ff; margin: 8px 0 0; font-size: 14px;">Nouvel article publié</p>
            </div>
            <div style="padding: 40px 32px;">
                <p style="font-size: 13px; font-weight: 700; color: #0066ff; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 12px;">{$safeCategory} · {$readTime} min de lecture</p>
                <h2 style="font-size: 24px; font-weight: 700; color: #1a1a2e; margin: 0 0 16px; line-height: 1.3;">{$safeTitle}</h2>
                <p style="font-size: 15px; color: #555; line-height: 1.7; margin: 0 0 32px;">{$safeExcerpt}</p>
                <div style="text-align: center;">
                    <a href="{$articleUrl}" style="display: inline-block; padding: 14px 32px; background: #0066ff; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px;">Lire l'article →</a>
                </div>
            </div>
            <div style="padding: 24px 32px; background: #f8f9fa; border-top: 1px solid #eee; text-align: center;">
                <p style="font-size: 12px; color: #aaa; margin: 0;">
                    EntryWeb · entryweb.fr<br>
                    Vous recevez cet email car vous êtes abonné à la newsletter EntryWeb.
                </p>
            </div>
        </div>
        HTML;

        $sent = 0;
        foreach ($subscribers as $subscriber) {
            try {
                $mail = (new Email())
                    ->from($this->contactEmail)
                    ->to($subscriber->getEmail())
                    ->subject('Nouvel article : '.$article->getTitle())
                    ->html($htmlContent)
                    ->text("Nouvel article EntryWeb : {$article->getTitle()}\n\n{$article->getExcerpt()}\n\nLire : {$articleUrl}");

                $this->mailer->send($mail);
                ++$sent;
            } catch (TransportExceptionInterface) {
                // continue with next subscriber
            }
        }

        return $sent;
    }
}
