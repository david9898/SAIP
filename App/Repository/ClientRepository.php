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

}