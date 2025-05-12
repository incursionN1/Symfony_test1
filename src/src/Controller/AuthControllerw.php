<?php

namespace App\Controller;

use App\Security\StaticUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AuthControllerw extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        // Создаем пользователя без проверки в базе данных
        $user = new StaticUser('api_user', ['ROLE_API']);

        // Генерируем токен
        $token = $JWTManager->create($user);

        return $this->json([
            'token' => $token,
        ]);
    }
}