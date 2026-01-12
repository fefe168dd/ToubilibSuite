<?php
namespace toubilib\api\actions;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ValidateTokenAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        // Extraction du token JWT depuis l'en-tête Authorization
        $authHeader = $request->getHeaderLine('Authorization');
        if (empty($authHeader) || !preg_match('/^Bearer\s+(\S+)$/', $authHeader, $matches)) {
            $response->getBody()->write(json_encode([
                'error' => 'Authorization header missing or malformed.'
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
        $jwt = $matches[1];

        // Vérification du token via le provider JWT
        try {
            // Remplacer par votre provider JWT réel
            $provider = new \toubilib\api\infrastructure\JwtProvider();
            $provider->validate($jwt);
            $response->getBody()->write(json_encode([
                'message' => 'Token is valid.'
            ]));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }
}
