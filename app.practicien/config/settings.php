<?php

use Psr\Container\ContainerInterface;
use toubilib\api\actions\GetPraticiensAction;
use toubilib\core\application\ports\spi\repositoryInterfaces\PraticienRepository;
use toubilib\core\application\ports\api\ServicePraticienInterface;
use toubilib\core\application\usecases\ServicePraticien;
use toubilib\infra\repositories\PDOPraticienRepository;
use toubilib\api\actions\GetRdvOcuppePraticienParDate;
use toubilib\core\application\ports\api\ServiceRdvInterface;
use toubilib\core\application\usecases\ServiceRdv;
use toubilib\core\application\ports\spi\repositoryInterfaces\RdvRepository;
use toubilib\core\application\ports\spi\repositoryInterfaces\PatientRepository;
use toubilib\infra\repositories\PDOPatientRepository;
use toubilib\infra\repositories\PDORdvRepository;
use toubilib\core\domain\entities\auth\AuthServiceInterface;
use toubilib\core\application\usecases\AuthService;
use toubilib\core\application\ports\spi\repositoryInterfaces\UserRepositoryInterface;
use toubilib\infra\repositories\PDOUserRepository;
use toubilib\api\actions\AuthenticateUserAction;
use toubilib\api\actions\SignInAction;
use toubilib\api\actions\RefreshTokenAction;
use toubilib\api\provider\AuthProviderInterface;
use toubilib\api\provider\JwtAuthProvider;
use toubilib\api\middlewares\AuthnMiddleware;
use toubilib\api\actions\GetUserProfileAction;
use toubilib\api\actions\PraticienOnlyAction;
use toubilib\core\domain\entities\auth\AuthzServiceInterface;
use toubilib\core\application\usecases\AuthzService;
use toubilib\api\middlewares\AuthzMiddleware;


return [

    // settings
    'displayErrorDetails' => true,
    'logs.dir' => __DIR__ . '/../../var/logs',
    'toubilib.db.config' => __DIR__ . '/toubilib.db.ini',
    'env.config' => __DIR__ . '/.env.dist',

    GetPraticiensAction::class => function (ContainerInterface $c) {
        return new GetPraticiensAction($c->get(ServicePraticienInterface::class));
    },

    AuthenticateUserAction::class => function (ContainerInterface $c) {
        return new AuthenticateUserAction($c->get(AuthServiceInterface::class));
    },

    SignInAction::class => function (ContainerInterface $c) {
        return new SignInAction($c->get(AuthProviderInterface::class));
    },

    RefreshTokenAction::class => function (ContainerInterface $c) {
        return new RefreshTokenAction($c->get(AuthProviderInterface::class));
    },

    GetUserProfileAction::class => function (ContainerInterface $c) {
        return new GetUserProfileAction();
    },

    PraticienOnlyAction::class => function (ContainerInterface $c) {
        return new PraticienOnlyAction();
    },

    ServicePraticienInterface::class => function (ContainerInterface $c) {
        return new ServicePraticien($c->get(PraticienRepository::class));
            },

    GetRdvOcuppePraticienParDate::class => function (ContainerInterface $c) {
        return new GetRdvOcuppePraticienParDate($c->get(ServiceRdvInterface::class));
    },

    ServiceRdvInterface::class => function (ContainerInterface $c) {
        return new ServiceRdv(
            $c->get(RdvRepository::class),
            $c->get(PraticienRepository::class),
            $c->get(PatientRepository::class)
        );
    },

    AuthServiceInterface::class => function (ContainerInterface $c) {
        return new AuthService($c->get(UserRepositoryInterface::class));
    },

    AuthProviderInterface::class => function (ContainerInterface $c) {
        $config = parse_ini_file($c->get('env.config'));
        $secret = $config['auth.jwt.key'] ?? getenv('AUTH_JWT_KEY') ?? null;
        if (!$secret) {
            throw new \RuntimeException('JWT secret not configured. Add auth.jwt.key to your env file or set AUTH_JWT_KEY.');
        }

        return new JwtAuthProvider(
            $c->get(AuthServiceInterface::class),
            $secret,
            'HS256',
            3600,
            86400
        );
    },

    AuthnMiddleware::class => function (ContainerInterface $c) {
        return new AuthnMiddleware($c->get(AuthProviderInterface::class));
    },

    AuthzServiceInterface::class => function (ContainerInterface $c) {
        return new AuthzService(
            $c->get(RdvRepository::class),
            $c->get(PraticienRepository::class)
        );
    },

    AuthzMiddleware::class => function (ContainerInterface $c) {
        return new AuthzMiddleware($c->get(AuthzServiceInterface::class));
    },

    RdvRepository::class => fn(ContainerInterface $c) => new PDORdvRepository($c->get('rdv.pdo')),

    PatientRepository::class => fn(ContainerInterface $c) => new PDOPatientRepository($c->get('patient.pdo')),

    UserRepositoryInterface::class => fn(ContainerInterface $c) => new PDOUserRepository($c->get('auth.pdo')),

    PraticienRepository::class => fn(ContainerInterface $c) => new PDOPraticienRepository($c->get('praticien.pdo')),


    // infra
    'praticien.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file($c->get('env.config'));
        $dsn = "{$config['prat.driver']}:host={$config['prat.host']};dbname={$config['prat.database']}";
        $user = $config['prat.username'];
        $password = $config['prat.password'];
        return new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    },
    'rdv.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file($c->get('env.config'));
        $dsn = "{$config['rdv.driver']}:host={$config['rdv.host']};dbname={$config['rdv.database']}";
        $user = $config['rdv.username'];
        $password = $config['rdv.password'];
        return new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    },

    'patient.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file($c->get('env.config'));
        $dsn = "{$config['pat.driver']}:host={$config['pat.host']};dbname={$config['pat.database']}";
        $user = $config['pat.username'];
        $password = $config['pat.password'];
        return new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    },

    'auth.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file($c->get('env.config'));
        $dsn = "{$config['auth.driver']}:host={$config['auth.host']};dbname={$config['auth.database']}";
        $user = $config['auth.username'];
        $password = $config['auth.password'];
        return new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    },


];