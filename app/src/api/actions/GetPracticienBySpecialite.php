<?php 
namespace toubilib\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use toubilib\core\application\usecases\ServicePraticien;
class GetPracticienBySpecialite
{
    private ServicePraticien $service;

    public function __construct(ServicePraticien $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $specialite = $args['name'];
        $praticiens = $this->service->PraticienParSpecialite($specialite);
        if ($praticiens) {
            // Ajouter les liens HATEOAS
            $baseUrl = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost();
            $praticiensArray = json_decode(json_encode($praticiens), true);
            foreach ($praticiensArray as &$praticien) {
                $praticien['_links'] = [
                    'self' => [
                        'href' => $baseUrl . '/praticiens/' . $praticien['id']
                    ]
                ];
            }
            $payload = json_encode($praticiensArray);
            $response->getBody()->write($payload);

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['error' => 'No praticiens found for this specialty']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }
    }
}