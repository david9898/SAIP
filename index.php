<?php

spl_autoload_register();

require_once 'vendor/autoload.php';

$router = new \Phroute\Phroute\RouteCollector(new \Phroute\Phroute\RouteParser());

function processInput(){
    $url = array_slice(explode('/', $_SERVER['REQUEST_URI']), 2);

    $string = '';
    for ($i = 0; $i < count($url); $i++) {
        $string = $string . '/' . $url[$i];
    }

    return $string;
}


$router->get('/clients/1', function () {
    $db = new PDO("mysql:host=localhost;dbname=network_controll;charset=utf8", "root", "");
    $clientController = new \App\Controller\ClientController();
    $clientController->showClients();
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

$router->get('/logout', function () {
   $staffController = new \App\Controller\StaffController();
   $staffController->logout();
});
$dispatcher =  new \Phroute\Phroute\Dispatcher($router->getData());

echo $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], processInput());