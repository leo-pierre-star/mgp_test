<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\User;
use App\Service\ContactRateLimiter;
use App\Dto\ContactMessageRequest;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class ContactController extends AbstractController
{
    private const MAX_ATTACHMENT_BYTES = 2_000_000; // 2MB

    #[Route('/contact', name: 'api_contact_send', methods: ['POST'])]
    #[OA\Post(
        path: '/api/contact',
        summary: 'Send a contact message',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['subject', 'message'],
                    properties: [
                        new OA\Property(property: 'subject', type: 'string'),
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'attachment', type: 'string', format: 'binary', nullable: true)
                    ]
                )
            )
        ),
        tags: ['Contact']
    )]
    #[OA\Response(response: 200, description: 'Message sent')]
    #[OA\Response(response: 400, description: 'Validation error')]
    #[OA\Response(response: 429, description: 'Too many requests (1 per hour)')]
    public function send(
        #[MapRequestPayload] ContactMessageRequest $dto,
        MailerInterface $mailer,
        ContactRateLimiter $rateLimiter,
        Request $request
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$rateLimiter->canSend($user)) {
            return new JsonResponse([
                'error' => 'Rate limit: only one contact message per hour',
                'retry_after_seconds' => $rateLimiter->getRetryAfterSeconds($user)
            ], 429);
        }

        $attachment = $request->files->get('attachment');
        if ($attachment) {
            if ($attachment->getSize() > self::MAX_ATTACHMENT_BYTES) {
                return new JsonResponse(['errors' => ['attachment' => 'Attachment exceeds 2MB']], 422);
            }
        }

        $email = (new Email())
            ->from('no-reply@example.com')
            ->to('support@example.com')
            ->subject('[Contact] ' . $dto->subject)
            ->text("From: " . $user->getUserIdentifier() . "\n\n" . $dto->message)
            ->html('<p><strong>From:</strong> ' . htmlspecialchars($user->getUserIdentifier(), ENT_QUOTES) . '</p><p>' . nl2br(htmlspecialchars($dto->message, ENT_QUOTES)) . '</p>');

        if ($attachment) {
            $email->attachFromPath($attachment->getRealPath(), $attachment->getClientOriginalName(), $attachment->getMimeType());
        }

        $mailer->send($email);
        $rateLimiter->markSent($user);

        return new JsonResponse(['message' => 'Contact message sent']);
    }
}
