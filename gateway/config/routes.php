<?php
use Slim\App;
use Gateway\Actions\PraticienListAction;

return function (App $app) {
    // Route générique proxy pour toutes les requêtes vers l'API toubilib
    $app->any('/{routes:.+}', Gateway\Actions\ProxyAction::class);
};
