<?php

use App\DTO\ClientDTO;

spl_autoload_register();

require_once 'vendor/autoload.php';

$router = new \Phroute\Phroute\RouteCollector(new \Phroute\Phroute\RouteParser());

function route() {

}

$router->get('Network_Project/davo', function () {
    $milisec = microtime(true) * 1000;
    $session = new \Core\Session\Session();
    $session->set('davo', 'david');
    $a = new \App\Controller\ClientController();
//    $a->test();
    $dataBinder = new \Core\DataBinder\DataBinder();
    $client = new ClientDTO();
    $arr = ['username' => 'fdsfdsa', 'front_image' => 'fdsafdsa',
            'front_image_one' => 'fdsafsasa', 'front_image_2' => 'fdsafsasa',
            'email' => 'david_786@abv.bg'];
    $res = $dataBinder->bindData($arr, $client);
    print_r($res);

    echo '<br />';
    print_r('Memory: ' . memory_get_usage() / 1024 / 1024 . "MB");
    echo '<br />';

    $milisecafter = microtime(true) * 1000;
    print_r("Time: " . ($milisecafter - $milisec));
    echo '<br />';
});

$router->get('Network_Project/rali', function () {
    return 'ralicaaaaa';
});

$dispatcher =  new \Phroute\Phroute\Dispatcher($router->getData());

echo $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));