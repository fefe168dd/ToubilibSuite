<?php
namespace Gateway\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class PraticienListAction
{
    private $guzzle;

    public function __construct(ContainerInterface $container)
    {
        $this->guzzle = $container->get('guzzle');
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $apiResponse = $this->guzzle->request('GET', 'praticiens');
        $body = $apiResponse->getBody()->getContents();
        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
