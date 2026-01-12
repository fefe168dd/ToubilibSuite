<?php

namespace toubilib\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubilib\core\domain\entities\auth\UserProfile;

/**
 * Action accessible uniquement aux praticiens
 * Nécessite le middleware AuthnMiddleware
 */
class PraticienOnlyAction
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Récupération du profil utilisateur injecté par le middleware
        $userProfile = $request->getAttribute('userProfile');
        
        if (!$userProfile instanceof UserProfile) {
            $payload = json_encode([
                'error' => 'Profil utilisateur non disponible'
            ]);
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }

        // Vérification du rôle praticien
        if (!$userProfile->isPraticien()) {
            $payload = json_encode([
                'error' => 'Accès refusé - Réservé aux praticiens',
                'code' => 'INSUFFICIENT_PRIVILEGES'
            ]);
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(403);
        }

        // Action réservée aux praticiens
        $payload = json_encode([
            'success' => true,
            'message' => 'Bienvenue, praticien !',
            'practitioner' => [
                'id' => $userProfile->getId(),
                'email' => $userProfile->getEmail(),
                'role' => $userProfile->getRoleName()
            ],
            'data' => [
                'patients_count' => 42,
                'appointments_today' => 8,
                'next_appointment' => '14:30'
            ]
        ]);

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}