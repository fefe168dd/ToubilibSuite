<?php
namespace Gateway\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use GuzzleHttp\Exception\ClientException;
use Slim\Exception\HttpNotFoundException;

class ProxyAction
{
    private $guzzle;

    public function __construct(ContainerInterface $container)
    {
        $this->guzzle = $container->get('guzzle');
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $uri = $request->getUri()->getPath();
        $method = $request->getMethod();
        $options = [];
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $options['body'] = $request->getBody()->getContents();
        }
        $options['headers'] = $request->getHeaders();
        try {
            $apiResponse = $this->guzzle->request($method, ltrim($uri, '/'), $options);
            $body = $apiResponse->getBody()->getContents();
            $response->getBody()->write($body);
            return $response->withStatus($apiResponse->getStatusCode())
                ->withHeader('Content-Type', $apiResponse->getHeaderLine('Content-Type'));
        } catch (ClientException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 404) {
                throw new HttpNotFoundException($request, "Ressource non trouvée");
            }
            throw $e;
        }
    }
}
