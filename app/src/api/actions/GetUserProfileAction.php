<?php

namespace toubilib\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubilib\core\domain\entities\auth\UserProfile;

/**
 * Action pour obtenir le profil de l'utilisateur connecté
 * Nécessite le middleware AuthnMiddleware
 */
class GetUserProfileAction
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

        // Retour du profil utilisateur
        $payload = json_encode([
            'success' => true,
            'profile' => $userProfile->toArray()
        ]);

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}