<?php


namespace App\Repository;

use App\DTO\ClientDTO;
use App\DTO\PaymentDTO;
use Core\Database\PrepareStatementInterface;

class ClientRepository implements ClientRepositoryInterface
{
    private $db;

    public function __construct(PrepareStatementInterface $db)
    {
        $this->db = $db;
    }

    public function addClient(ClientDTO $client): bool
    {
        $sql = 'INSERT INTO clients(town, abonament, neighborhood, first_name, last_name, 
                                    phone, email, street, date_register, description, street_number)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $this->db->prepare($sql)
                ->execute([$client->getTown(), $client->getAbonament(), $client->getNeighborhood(), $client->getFirstName(),
                        $client->getLastName(), $client->getPhone(), $client->getEmail(), $client->getStreet(),
                        $client->getDateRegister(), $client->getDescription(), $client->getStreetNumber()]);

        return true;
    }

    public function getClients(): \Generator
    {
        $sql = 'SELECT abonaments.name as abonament, towns.name as town, streets.name as street, 
                first_name as firstName, last_name as lastName, payments.end_time as paid, clients.id
                FROM clients
                JOIN abonaments ON abonaments.id = clients.abonament
                JOIN towns ON towns.id = clients.town
                JOIN streets ON streets.id = clients.street
                LEFT JOIN payments ON payments.id = 
                (SELECT id FROM payments WHERE `client` = clients.id ORDER BY id DESC LIMIT 1)
                ORDER BY clients.id DESC
                LIMIT 20';

        return $this->db->prepare($sql)
                        ->execute()
                        ->fetchObject(ClientDTO::class);
    }

    public function getMoreClients($firstResult): ?\Generator
    {
        $sql = 'SELECT abonaments.name as abonament, streets.name as street, towns.name as town, 
                first_name as firstName, last_name as lastName, clients.id, payments.end_time as paid
                FROM clients
                JOIN abonaments ON abonaments.id = clients.abonament
                JOIN towns ON towns.id = clients.town
                JOIN streets ON streets.id = clients.street
                LEFT JOIN payments ON payments.id = 
                (SELECT id FROM payments WHERE `client` = clients.id ORDER BY id DESC LIMIT 1)
                ORDER BY clients.id DESC
                LIMIT :firstResult, 20';

        return $this->db->prepare($sql)
                        ->bindParam('firstResult', $firstResult, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchAssoc();
    }

    public function searchFriends($patterns, $firstResult): ?\Generator
    {
        $sql = 'SELECT abonaments.name as abonament, streets.name as street, 
                towns.name as town, email, first_name as firstName, last_name as lastName, clients.id
                FROM clients
                JOIN abonaments ON abonaments.id = clients.abonament
                JOIN towns ON towns.id = clients.town
                JOIN streets ON streets.id = clients.street';

        $sql = $sql . ' WHERE ';

        for ($i=0;$i<count($patterns);$i++) {
            if ( $i !== count($patterns) - 1 ) {
                $sql = $sql . ' CONCAT(abonaments.name, towns.name, streets.name, clients.first_name, 
                clients.last_name, clients.email) LIKE :pattern' . $i .' AND ';
            }else {
                $sql = $sql . ' CONCAT(abonaments.name, towns.name, streets.name, clients.first_name, 
                clients.last_name, clients.email) LIKE :pattern' . $i . ' ';
            }
        }

        $sql = $sql . ' LIMIT :firstResult, 20';

        $stmt = $this->db->prepare($sql);
        for ($i=0;$i<count($patterns);$i++) {
            $stmt->bindParam('pattern' . $i, '%' . $patterns[$i] . '%', \PDO::PARAM_STR);
        }
        $stmt->bindParam('firstResult', (int)$firstResult, \PDO::PARAM_INT);
        $resStmt = $stmt->execute();
        return $resStmt->fetchAssoc();
    }

    public function getClient($id): ?ClientDTO
    {
        $sql = 'SELECT abonaments.name as abonament, abonaments.price as sum,
                streets.name as street, towns.name as town, date_register as register,
                neighborhoods.name as neighborhood, email, first_name as firstName, 
                last_name as lastName, phone, street_number as streetNumber
                FROM clients
                JOIN abonaments ON abonaments.id = clients.abonament
                JOIN towns ON towns.id = clients.town
                JOIN streets ON streets.id = clients.street
                LEFT JOIN neighborhoods ON (neighborhoods.id = clients.neighborhood) 
                WHERE clients.id = :id';

        return $this->db->prepare($sql)
                        ->bindParam('id', $id, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchObject(ClientDTO::class)
                        ->current();

    }

    public function getAllClients(): \Generator
    {
        $sql = 'SELECT clients.id, paid, abonaments.price as sum FROM clients 
                JOIN abonaments ON abonaments.id = clients.abonament';

        return $this->db->prepare($sql)
                        ->execute()
                        ->fetchObject(ClientDTO::class);
    }

    public function getClientAbonamentPrice($clientId): ClientDTO
    {
        $sql = 'SELECT abonaments.price as sum FROM clients
                JOIN abonaments ON abonaments.id = clients.abonament
                WHERE clients.id = :clientId';

        return $this->db->prepare($sql)
                        ->bindParam('clientId', $clientId, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchObject(ClientDTO::class)
                        ->current();
    }
}