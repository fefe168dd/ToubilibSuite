<?php 
namespace toubilib\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use toubilib\core\application\ports\api\ServiceRdvInterface;
use DateTime;
use Exception;

class GetRdvOcuppePraticienParDate
{
    private ServiceRdvInterface $service;

    public function __construct(ServiceRdvInterface $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $params = $request->getQueryParams();
            
            // Validation des paramètres requis
            if (!isset($params['debut']) || !isset($params['fin']) || !isset($params['praticien_id'])) {
                $response->getBody()->write(json_encode([
                    'error' => 'Paramètres manquants. Requis: debut, fin, praticien_id'
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }

            $debut = new DateTime($params['debut']);
            $fin = new DateTime($params['fin']);
            $praticien_id = $params['praticien_id'];

            $rdvs = $this->service->listerRdvOcuppePraticienParDate($debut, $fin, $praticien_id);
         
            $payload = json_encode($rdvs);


            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
                
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Erreur lors de la récupération des RDV: ' . $e->getMessage()
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}