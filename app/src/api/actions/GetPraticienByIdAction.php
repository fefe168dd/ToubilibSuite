<?php 
namespace toubilib\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use toubilib\core\application\usecases\ServicePraticien;


class GetPraticienByIdAction
{
    private ServicePraticien $service;

    public function __construct(ServicePraticien $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $praticien = $this->service->PraticienParId($id);
        if ($praticien) {
            // Ajouter les liens HATEOAS
            $baseUrl = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost();
            $praticienArray = json_decode(json_encode($praticien), true);
            $praticienArray['_links'] = [
                'self' => [
                    'href' => $baseUrl . '/praticiens/' . $id
                ]
            ];
            $payload = json_encode($praticien);
            $response->getBody()->write($payload);

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Praticien not found']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }
    }
}