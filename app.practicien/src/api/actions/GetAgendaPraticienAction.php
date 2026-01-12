<?php
namespace toubilib\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use toubilib\core\application\ports\api\ServiceRdvInterface;
use DateTime;

class GetAgendaPraticienAction {
    private ServiceRdvInterface $service;

    public function __construct(ServiceRdvInterface $service) {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $praticienId = $args['id'] ?? null;
        if (!$praticienId) {
            $response->getBody()->write(json_encode([
                'error' => 'ID du praticien manquant.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $params = $request->getQueryParams();
        $debut = isset($params['debut']) ? new DateTime($params['debut']) : null;
        $fin = isset($params['fin']) ? new DateTime($params['fin']) : null;
        $rdvs = $this->service->listerAgendaPraticienParPeriode($praticienId, $debut, $fin);
        $response->getBody()->write(json_encode($rdvs));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
