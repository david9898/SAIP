<?php

spl_autoload_register();

ini_set('max_execution_time', 900);

$time = time();

$dbParams = parse_ini_file('Configurations/db.ini');

$pdo = new \PDO($dbParams['dsn'], $dbParams['user'], $dbParams['password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

$db = new \Core\Database\PrepareStatement($pdo);

$billRepo   = new \App\Repository\BillRepository($db);
$clientRepo = new \App\Repository\ClientRepository($db);

/** @var ClientDTO[] $clients */
$clients    = $clientRepo->getAllClients();

foreach ($clients as $client) {
    $paid     = (int)$client->getPaid();
    $diffTime = $paid - $time;

    if ( $paid <= $time ) {
        if ( $diffTime <= 0 && $diffTime >= -86400 ) {
            $billDTO = new \App\DTO\BillDTO();
            $billDTO->setStart($paid);
            $billDTO->setEnd($paid + 2592000);
            $billDTO->setSum($client->getSum());
            $billDTO->setClient($client->getId());

            $billRepo->addNewBill($billDTO);
        }else if ( $diffTime >= -2592000 && $diffTime <= -26784000 ) {
            $billDTO = new \App\DTO\BillDTO();
            $billDTO->setStart($paid + 2592000);
            $billDTO->setEnd($paid + 5184000);
            $billDTO->setSum($client->getSum());
            $billDTO->setClient($client->getId());

            $billRepo->addNewBill($billDTO);
        }else if ( $diffTime >= -5184000 && $diffTime <= -5270400 ) {
            $billDTO = new \App\DTO\BillDTO();
            $billDTO->setStart($paid + 5184000);
            $billDTO->setEnd($paid + 7776000);
            $billDTO->setSum($client->getSum());
            $billDTO->setClient($client->getId());

            $billRepo->addNewBill($billDTO);
        }else {
            continue;
        }
    }else {
        continue;
    }
}
