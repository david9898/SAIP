<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

spl_autoload_register
(
    function( $class )
    {
        $explodeClass = explode('\\', $class);

        $string = '';

        for ($i = 0;$i < count($explodeClass); $i++) {
            $dir = $explodeClass[$i];

            if  ( $i === 0 ) {
                $string .= $dir;
            }else {
                $string = $string . '/' . $dir;
            }
        }

        require_once $string . '.php';
    }
);

require_once 'vendor/autoload.php';

$router = new \Phroute\Phroute\RouteCollector(new \Phroute\Phroute\RouteParser());

function processInput(){
    $url = explode('/', htmlspecialchars($_SERVER['REQUEST_URI']));

    $string = '';
    for ($i = 0; $i < count($url); $i++) {
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

$router->get('/getTownStreets/{id:\d+}/{csrfToken:a}', function ($id, $csrfToken) {
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

$router->get('/getClients/{csrfToken:a}/{firstResult:\d+}', function ($csrfToken, $firstResult) {
    require_once 'Front Layer/start.php';

    $clientApi = new \App\ApiController\ClientApiController();
    $clientApi->getClients($db, $csrfToken, $firstResult);
});

$router->get('searchFriends/{csrfToken:a}/{firstResult:\d+}/{pattern}?', function ($csrfToken, $firstResult, $pattern = null) {
    require_once 'Front Layer/start.php';

    $clientApi = new \App\ApiController\ClientApiController();
    $clientApi->getSearchFriends($db, $csrfToken, $firstResult, $pattern);
});

$router->get('/logout', function () {
   $staffController = new \App\Controller\StaffController();
   $staffController->logout();
});

$router->any('/staff', function () {
    require_once 'Front Layer/start.php';

    $staffController = new \App\Controller\StaffController();
    $staffController->registerStaff($db);
});

$router->get('/client/{id:\d+}', function ($id) {
   require_once 'Front Layer/start.php';

   $clientController = new \App\Controller\ClientController();
   $clientController->seeClient($db, $id);
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

$router->get('/getIncomeAccount/{abonamentId:\d+}/{csrfToken}', function ($abonamentId, $csrfToken) {
    require_once 'Front Layer/start.php';

    $clientApi = new \App\ApiController\ClientApiController();
    $clientApi->getIncomeToAccount($db, $abonamentId, $csrfToken);
});

$router->post('/payInvoices', function () {
    require_once 'Front Layer/start.php';

    $clientApi = new \App\ApiController\ClientApiController();
    $clientApi->payInvoices($db);
});

$router->post('/addStaff', function () {
    require_once 'Front Layer/start.php';

    $staffApi = new \App\ApiController\StaffApiController();
    $staffApi->addStaff($db);
});

$router->get('/getOneCustomer/{customerId:\d+}/{csrfToken}', function ($customerId, $csrfToken) {
    require_once 'Front Layer/start.php';

    $staffApi = new \App\ApiController\StaffApiController();
    $staffApi->getOneCustomer($db, $customerId, $csrfToken);
});

$router->put('/updateStaff', function () {
    require_once 'Front Layer/start.php';

    $staffApi = new \App\ApiController\StaffApiController();
    $staffApi->updateStaff($db);
});

$router->put('/deleteStaff', function () {
    require_once 'Front Layer/start.php';

    $staffApi = new \App\ApiController\StaffApiController();
    $staffApi->deleteStaff($db);
});

$router->get('/addInvoicesToAllClients', function () {
    require_once 'Front Layer/start.php';

    $clientRepo     = new \App\Repository\ClientRepository($db);
    $invoiceRepo    = new \App\Repository\InvoiceRepository($db);
    $invoiceService = new \App\Service\InvoiceService();

    $invoiceService->addInvoicesToAllClients($invoiceRepo, $clientRepo);
});

$router->get('/showOldClients', function () {
    require_once 'Front Layer/start.php';

    $clientController = new \App\Controller\ClientController();
    $clientController->getOldClients($db);
});

$router->any('/editOldClient/{id}', function ($id) {
    require_once 'Front Layer/start.php';

    $clientController = new \App\Controller\ClientController();
    $clientController->editOldClient($db, $id);
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
}catch (\PDOException $exception) {
    require_once 'App/Template/Exeptions/SomethingWrong.php';
}catch (Exception $exception) {
    require_once 'App/Template/Exeptions/SomethingWrong.php';
}