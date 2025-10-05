<?php

namespace App\Controller;

use App\Dto\CreateUserRequest;
use App\Dto\RequestPasswordResetRequest;
use App\Dto\ResetPasswordRequest;
use App\Entity\User;
use App\Service\PasswordResetService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route('/users', name: 'api_users_create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/users',
        summary: 'Create a new user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: CreateUserRequest::class))
        ),
        tags: ['Users']
    )]
    #[OA\Response(
        response: 201,
        description: 'User created',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'id', type: 'integer'),
            new OA\Property(property: 'email', type: 'string')
        ])
    )]
    #[OA\Response(response: 400, description: 'Invalid data')]
    public function create(
        #[MapRequestPayload] CreateUserRequest $dto,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): Response {

        $user = new User();
        $user->setEmail($dto->email);
        $hashed = $passwordHasher->hashPassword($user, $dto->password);
        $user->setPassword($hashed);

        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/password/forgot', name: 'api_password_request', methods: ['POST'])]
    #[OA\Post(
        path: '/api/password/forgot',
        summary: 'Request password reset',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: RequestPasswordResetRequest::class))
        ),
        tags: ['Auth']
    )]
    #[OA\Response(response: 200, description: 'Reset email dispatched (generic response)')]
    public function requestReset(
        #[MapRequestPayload] RequestPasswordResetRequest $dto,
        PasswordResetService $service
    ): Response {
        $service->createResetToken($dto->email);
        return new JsonResponse(['message' => 'If this email exists, a reset link was sent'], 200);
    }

    #[Route('/password/reset', name: 'api_password_reset', methods: ['POST'])]
    #[OA\Post(
        path: '/api/password/reset',
        summary: 'Reset password with token',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: ResetPasswordRequest::class))
        ),
        tags: ['Auth']
    )]
    #[OA\Response(response: 200, description: 'Password updated')]
    #[OA\Response(response: 400, description: 'Invalid token')]
    public function resetPassword(
        #[MapRequestPayload] ResetPasswordRequest $dto,
        PasswordResetService $service,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ): Response {
        $user = $service->validateAndConsume($dto->token);
        if (!$user) {
            return new JsonResponse(['error' => 'Invalid or expired token'], 400);
        }
        $hashed = $hasher->hashPassword($user, $dto->newPassword);
        $user->setPassword($hashed);
        $em->flush();
        return new JsonResponse(['message' => 'Password updated']);
    }
}
