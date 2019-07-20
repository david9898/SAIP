<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

spl_autoload_register
(
//    function( $class )
//    {
//        $explodeClass = explode('\\', $class);
//
//        require_once $explodeClass[0] . '/' . $explodeClass[1] . '/' .  $explodeClass[2] . '.php';
//    }
);

require_once 'vendor/autoload.php';

$router = new \Phroute\Phroute\RouteCollector(new \Phroute\Phroute\RouteParser());

function processInput(){
    $url = explode('/', $_SERVER['REQUEST_URI']);

    $string = '';
    for ($i = 2; $i < count($url); $i++) {
        $string = $string . '/' . $url[$i];
    }

    return $string;
}


$router->get('/clients', function () {
    require_once 'Front Layer/start.php';

    $clientController = new \App\Controller\ClientController();
    $clientController->showClients($db);
});

$router->get('/client/add', function () {
    require_once 'Front Layer/start.php';

    $clientController = new \App\Controller\ClientController();
    $clientController->addClient($db);
});

$router->get('/getTownStreets/{id}/{csrfToken}', function ($id, $csrfToken) {
   require_once 'Front Layer/start.php';

   $streetApiController = new \App\ApiController\StreetApiController();
   $streetApiController->getTownStreets($db, $id, $csrfToken);
});

$router->post('/addClient', function () {
    require_once 'Front Layer/start.php';

    $clientApi = new \App\ApiController\ClientApiController();
    $clientApi->addClient($db);
});

$router->any('/login', function () {
    require_once 'Front Layer/start.php';

    $staffController = new \App\Controller\StaffController();
    $staffController->login($db);
});

$router->get('/getMoreClients/{csrfToken}/{firstResult}', function ($csrfToken, $firstResult) {
    require_once 'Front Layer/start.php';

    $clientApi = new \App\ApiController\ClientApiController();
    $clientApi->getMoreClients($db, $csrfToken, $firstResult);
});

$router->get('searchFriends/{csrfToken}/{firstResult}/{pattern}?', function ($csrfToken, $firstResult, $pattern = null) {
    require_once 'Front Layer/start.php';

    $clientApi = new \App\ApiController\ClientApiController();
    $clientApi->getSearchFriends($db, $csrfToken, $firstResult, $pattern);
});

$router->get('/logout', function () {
   $staffController = new \App\Controller\StaffController();
   $staffController->logout();
});

$router->any('/addStaff', function () {
    require_once 'Front Layer/start.php';

    $staffController = new \App\Controller\StaffController();
    $staffController->registerStaff($db);
});

$router->get('/client/{id}', function ($id) {
   require_once 'Front Layer/start.php';

   $clientController = new \App\Controller\ClientController();
   $clientController->seeClient($db, $id);
});

$router->get('/updateBills', function () {
   require_once 'BillSearchCommand.php';
});

$router->post('/addPayment', function () {
    require_once 'Front Layer/start.php';

    $clientApiController = new \App\ApiController\ClientApiController();
    $clientApiController->addPayment($db);
});

$router->any('/addAbonament', function () {
   require_once 'Front Layer/start.php';

   $staffController = new \App\Controller\StaffController();
   $staffController->addAbonament($db);
});

$router->any('/addStreet', function () {
    require_once 'Front Layer/start.php';

    $staffController = new \App\Controller\StaffController();
    $staffController->addStreet($db);
});

$dispatcher =  new \Phroute\Phroute\Dispatcher($router->getData());

try {
    echo $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], processInput());
}catch (\Phroute\Phroute\Exception\HttpRouteNotFoundException $e) {
    require_once 'App/Template/Exeptions/PageNotFound.php';
}catch (\Core\Exception\SessionException $exception) {
    require_once 'App/Template/Exeptions/SomethingWrong.php';
}catch (\Core\Exception\AccessDenyException $exception) {
    require_once 'App/Template/Exeptions/AccessDeny.php';
}catch (\Core\Exception\ValidationExeption $exception) {
    require_once 'App/Template/Exeptions/SomethingWrong.php';
}catch (PDOException $exception) {
    require_once 'App/Template/Exeptions/SomethingWrong.php';
}catch (Exception $exception) {
    require_once 'App/Template/Exeptions/SomethingWrong.php';
}