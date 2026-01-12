<?php
use Slim\App;
use Gateway\Actions\PraticienListAction;

return function (App $app) {
    $app->get('/praticiens', PraticienListAction::class);
};
