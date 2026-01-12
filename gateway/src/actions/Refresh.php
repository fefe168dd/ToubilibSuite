<?php 
namespace Gateway\Actions;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;   
use GuzzleHttp\Exception\ClientException;
use Slim\Exception\HttpNotFoundException;

class Refresh
{
    private $guzzle;

    public function __construct(ContainerInterface $container)
    {
        $this->guzzle = $container->get('guzzle');
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        try {
            $apiResponse = $this->guzzle->request('POST', 'auth/refresh');
            $body = $apiResponse->getBody()->getContents();
            $response->getBody()->write($body);
            return $response->withStatus($apiResponse->getStatusCode())
                ->withHeader('Content-Type', $apiResponse->getHeaderLine('Content-Type'));
        } catch (ClientException $e) {
            throw $e;
        }
    }
}