<?php 
namespace Gateway\Actions;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;   
use GuzzleHttp\Exception\ClientException;

class SignIn
{
    private $guzzle;

    public function __construct(ContainerInterface $container)
    {
        $this->guzzle = $container->get('guzzle');
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $options = [];
        $options['body'] = $request->getBody()->getContents();
        $options['headers'] = $request->getHeaders();

        try {
            $apiResponse = $this->guzzle->request('POST', 'auth/signin', $options);
            $body = $apiResponse->getBody()->getContents();
            $response->getBody()->write($body);
            return $response->withStatus($apiResponse->getStatusCode())
                ->withHeader('Content-Type', $apiResponse->getHeaderLine('Content-Type'));
        } catch (ClientException $e) {
            throw $e;
        }
    }
}