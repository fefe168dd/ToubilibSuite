<?php
declare(strict_types=1);



use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


return function( \Slim\App $app):\Slim\App {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write("Bienvenue sur l'API Toubilib !<br>");
        /*faire un lien vers /praticiens */
        $response->getBody()->write("<a href=\"/praticiens\">Liste des praticiens</a> <br>");
        /*faire un lien vers /praticiens/{id} avec un id exemple */
        $response->getBody()->write("<a href=\"/praticiens/af7bb2f1-cc52-3388-b9bc-c0b89e7f4c5b\">Praticien avec id af7bb2f1-cc52-3388-b9bc-c0b89e7f4c5b</a> <br>");
        $response->getBody()->write("<a href=\"/rdvs/occupe?debut=2025-12-05 11:00:00&fin=2025-12-05 23:00:00&praticien_id=4b1f7ae9-f6d4-3dc2-9869-45b1f2849c49\">RDV occupés du praticien</a> <br>");
        $response->getBody()->write("<a href=\"/rdvs/1\">Consulter le rendez-vous avec id 1</a> <br>");
        /*cree unn lien vers /rdv/creer  qui permet de creer un rdv test avec les valeurs args */
        $response->getBody()->write("<a href=\"/rdvs/creer\">Créer un rendez-vous (POST)</a> <br>");
        return $response;
    });
    $app->get('/praticiens', \toubilib\api\actions\GetPraticiensAction::class);
    $app->post('/tokens/validate', \toubilib\api\actions\ValidateTokenAction::class);
    $app->get('/praticiens/{id}', \toubilib\api\actions\GetPraticienByIdAction::class);
    $app->get('/praticien/{id}/agenda', \toubilib\api\actions\GetAgendaPraticienAction::class)
        ->add(\toubilib\api\middlewares\AuthzMiddleware::class)
        ->add(\toubilib\api\middlewares\AuthnMiddleware::class);
    $app->post('/auth/login', \toubilib\api\actions\AuthenticateUserAction::class);
    $app->post('/auth/signin', \toubilib\api\actions\SignInAction::class);
    $app->post('/auth/refresh', \toubilib\api\actions\RefreshTokenAction::class);
    $app->get('/auth/profile', \toubilib\api\actions\GetUserProfileAction::class)
        ->add(\toubilib\api\middlewares\AuthnMiddleware::class);
    $app->get('/praticien/dashboard', \toubilib\api\actions\PraticienOnlyAction::class)
        ->add(\toubilib\api\middlewares\AuthnMiddleware::class);
    $app->get('/rdvs/occupe', \toubilib\api\actions\GetRdvOcuppePraticienParDate::class)
        ->add(\toubilib\api\middlewares\AuthnMiddleware::class);
    $app->get('/rdvs/{id}', \toubilib\api\actions\GetRendezVousByIdAction::class)
        ->add(\toubilib\api\middlewares\AuthzMiddleware::class)
        ->add(\toubilib\api\middlewares\AuthnMiddleware::class);
    $app->post('/rdvs/creer', \toubilib\api\actions\AddRendezVous::class)
        ->add(\toubilib\api\middlewares\AuthzMiddleware::class)
        ->add(\toubilib\api\middlewares\AuthnMiddleware::class)
        ->add(\toubilib\api\Middlewares\InputRendezVousMiddleware::class);
    $app->get('/praticiens/specialite/{name}', \toubilib\api\actions\GetPracticienBySpecialite::class);
    $app->get('/418', function (Request $request, Response $response) {
        $response->getBody()->write("Je suis une théière");
        return $response->withStatus(418);
    });

    // Route RESTful pour annuler un rendez-vous
    $app->post('/rdvs/{id}/annuler', \toubilib\api\actions\AnnulerRendezVousAction::class)
        ->add(\toubilib\api\middlewares\AuthzMiddleware::class)
        ->add(\toubilib\api\middlewares\AuthnMiddleware::class);
    

    $app->options('/{routes:.+}', function (Request $request, Response $response) {
        return $response;
    });


    return $app;
};