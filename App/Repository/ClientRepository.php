<?php


namespace App\Repository;

use App\DTO\ClientDTO;
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
        $sql = 'INSERT INTO clients(town, abonament, neighborhood, first_name, last_name, phone, email, street, date_register, description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $this->db->prepare($sql)
                ->execute([$client->getTown(), $client->getAbonament(), $client->getNeighborhood(), $client->getFirstName(),
                        $client->getLastName(), $client->getPhone(), $client->getEmail(), $client->getStreet(),
                        $client->getDateRegister(), $client->getDescription()]);

        return true;
    }

    public function getClients(): \Generator
    {
        $sql = 'SELECT abonaments.name as abonament, towns.name as town, streets.name as street, 
                first_name as firstName, last_name as lastName, email 
                FROM clients
                JOIN abonaments ON abonaments.id = clients.abonament
                JOIN towns ON towns.id = clients.town
                JOIN streets ON streets.id = clients.street
                ORDER BY clients.id DESC
                LIMIT 20';

        return $this->db->prepare($sql)
                        ->execute()
                        ->fetchObject(ClientDTO::class);
    }

    public function getMoreClients($firstResult): ?\Generator
    {
        $sql = 'SELECT abonaments.name as abonament, streets.name as street, 
                towns.name as town, email, first_name as firstName, last_name as lastName
                FROM clients
                JOIN abonaments ON abonaments.id = clients.abonament
                JOIN towns ON towns.id = clients.town
                JOIN streets ON streets.id = clients.street
                ORDER BY clients.id DESC
                LIMIT :firstResult, 20';

        return $this->db->prepare($sql)
                        ->bindParam('firstResult', $firstResult, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchAssoc();
    }

    public function searchFriends($pattern, $firstResult): ?\Generator
    {
        $sql = 'SELECT abonaments.name as abonament, streets.name as street, 
                towns.name as town, email, first_name as firstName, last_name as lastName
                FROM clients
                JOIN abonaments ON abonaments.id = clients.abonament
                JOIN towns ON towns.id = clients.town
                JOIN streets ON streets.id = clients.street
                WHERE abonaments.name LIKE :pattern
                OR streets.name LIKE :pattern
                OR towns.name LIKE :pattern
                OR clients.first_name LIKE :pattern
                OR clients.last_name LIKE :pattern
                OR clients.email LIKE :pattern
                LIMIT :firstResult, 20';

        $pattern = urldecode($pattern) . '%';

        return $this->db->prepare($sql)
                        ->bindParam('pattern', $pattern, \PDO::PARAM_STR, 50)
                        ->bindParam('firstResult', $firstResult, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchAssoc();
    }

}