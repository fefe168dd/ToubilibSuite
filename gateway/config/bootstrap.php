<?php
use Psr\Container\ContainerInterface;
use DI\Container;
use GuzzleHttp\Client;

return function ($app) {
    $container = new Container();
    $container->set('guzzle', function() {
        return new Client([
            'base_uri' => 'http://api.toubilib:80/',
            'timeout'  => 5.0,
        ]);
    });
    $app->setContainer($container);

    // Ajout du middleware CORS
    $app->add(Gateway\Middleware\CorsMiddleware::class);
};
