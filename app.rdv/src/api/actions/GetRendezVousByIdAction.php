<?php 
namespace toubilib\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use toubilib\core\application\usecases\ServiceRdv;


class GetRendezVousByIdAction
{
    private ServiceRdv $service;

    public function __construct(ServiceRdv $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $rdv = $this->service->consulterRendezVousParId($id);
        if ($rdv) {
            $baseUrl = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost();
            $rdvArray = json_decode(json_encode($rdv), true);
            $rdvArray['_links'] = [
                'self' => [
                    'href' => $baseUrl . '/rendezvous/' . $id
                ],
                'praticien' => [
                    'href' => $baseUrl . '/praticiens/' . (isset($rdvArray['praticien_id']) ? $rdvArray['praticien_id'] : '')
                ],
                'patient' => [
                    'href' => $baseUrl . '/patients/' . (isset($rdvArray['patient_id']) ? $rdvArray['patient_id'] : '')
                ],
                'annuler' => [
                    'href' => $baseUrl . '/rdvs/' . $id . '/annuler',
                    'method' => 'POST'
                ]
            ];
            $payload = json_encode($rdvArray);
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Rendez-vous not found']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }
    }
}