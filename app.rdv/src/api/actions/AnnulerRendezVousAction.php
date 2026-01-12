<?php
namespace toubilib\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use toubilib\core\application\ports\api\ServiceRdvInterface;

class AnnulerRendezVousAction {
    private ServiceRdvInterface $service;

    public function __construct(ServiceRdvInterface $service) {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $idRdv = $args['id'] ?? null;
        if (!$idRdv) {
            $response->getBody()->write(json_encode([
                'error' => 'ID du rendez-vous manquant.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        try {
            $this->service->annulerRendezVous($idRdv);
            $response->getBody()->write(json_encode([
                'message' => 'Rendez-vous annulé avec succès.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            // 404 si non trouvé, 409 si déjà annulé, 400 si date passée
            $status = ($e->getMessage() === "Le rendez-vous n'existe pas.") ? 404 : (($e->getMessage() === "Le rendez-vous est déjà annulé.") ? 409 : 400);
            return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
        }
    }
}
