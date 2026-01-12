<?php
namespace Gateway\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use GuzzleHttp\Exception\ClientException;
use Slim\Exception\HttpNotFoundException;

class PraticienRdvListAction
{
    private $guzzle;

    public function __construct(ContainerInterface $container)
    {
        $this->guzzle = $container->get('guzzle');
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        try {
            $apiResponse = $this->guzzle->request('GET', 'praticiens/' . $id . '/rdv');
            $body = $apiResponse->getBody()->getContents();
            $response->getBody()->write($body);
            return $response->withHeader('Content-Type', 'application/json');
        } catch (ClientException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 404) {
                throw new HttpNotFoundException($request, "Rendez-vous ou praticien non trouvé");
            }
            throw $e;
        }
    }
}
