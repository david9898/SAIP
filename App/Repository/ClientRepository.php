<?php


namespace App\Repository;

use App\DTO\ClientDTO;
use App\DTO\OldDTO;
use Core\Database\PrepareStatementInterface;

class ClientRepository implements ClientRepositoryInterface
{
    private $db;

    public function __construct(PrepareStatementInterface $db)
    {
        $this->db = $db;
    }

    public function addClient(ClientDTO $client)
    {
        $sql = 'INSERT INTO clients(town, abonament, neighborhood, first_name, last_name, 
                                    phone, email, street, date_register, description, street_number, 
                                    credit_limit, remark, nickname, client_ip)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $this->db->prepare($sql)
                ->execute([$client->getTown(), $client->getAbonament(), $client->getNeighborhood(), $client->getFirstName(),
                        $client->getLastName(), $client->getPhone(), $client->getEmail(), $client->getStreet(),
                        $client->getDateRegister(), $client->getDescription(), $client->getStreetNumber(), $client->getCreditLimit(),
                        $client->getRemark(), $client->getNickname(), $client->getClientIp()]);

        return $this->db->lastInsertId();
    }

    public function getClients($firstResult): ?\Generator
    {
        $sql = 'SELECT abonaments.name as abonament, towns.name as town, streets.name as street, 
                first_name as firstName, last_name as lastName, invoices.end as paid, clients.id,
                invoices.paid_time as lastInvoicePaid 
                FROM clients
                JOIN abonaments ON abonaments.id = clients.abonament
                JOIN towns ON towns.id = clients.town
                JOIN streets ON streets.id = clients.street
                LEFT JOIN invoices ON invoices.id = 
                (SELECT id FROM invoices WHERE `client` = clients.id AND paid_time IS NOT NULL ORDER BY id DESC LIMIT 1)
                ORDER BY clients.id DESC
                LIMIT :firstResult, 20';

        return $this->db->prepare($sql)
                        ->bindParam('firstResult', $firstResult, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchAssoc();
    }

    public function searchFriends($patterns, $firstResult): ?\Generator
    {
        $sql = 'SELECT abonaments.name as abonament, towns.name as town, streets.name as street, 
                first_name as firstName, last_name as lastName, invoices.end as paid, clients.id,
                invoices.paid_time as lastInvoicePaid 
                FROM clients
                JOIN abonaments ON abonaments.id = clients.abonament
                JOIN towns ON towns.id = clients.town
                JOIN streets ON streets.id = clients.street
                LEFT JOIN invoices ON invoices.id =  
                (SELECT id FROM invoices WHERE `client` = clients.id AND paid_time IS NOT NULL ORDER BY id DESC LIMIT 1)';

        $sql = $sql . ' WHERE ';

        for ($i=0;$i<count($patterns);$i++) {
            if ( $i !== count($patterns) - 1 ) {
                $sql = $sql . ' CONCAT_WS(abonaments.name, towns.name, streets.name, clients.first_name, 
                clients.last_name, clients.email, clients.phone, clients.nickname) LIKE :pattern' . $i .' AND ';
            }else {
                $sql = $sql . ' CONCAT_WS(abonaments.name, towns.name, streets.name, clients.first_name, 
                clients.last_name, clients.email, clients.phone, clients.nickname) LIKE :pattern' . $i . ' ';
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
                neighborhoods.name as neighborhood, clients.description as description, 
                email, first_name as firstName, last_name as lastName, phone, 
                street_number as streetNumber, remark, nickname
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

    public function getClientsIdsAndSums(): \Generator
    {
        $sql = 'SELECT clients.id, abonaments.price as sum FROM clients
                JOIN abonaments ON clients.abonament = abonaments.id';

        return $this->db->prepare($sql)
                        ->execute()
                        ->fetchObject(ClientDTO::class);
    }

    /** DELETE */

    public function getFromOld(): \Generator
    {
        $sql = 'SELECT id, clientid as street, tag as name, name as phone, notes, 
                description as remark, startdate as lastInvoicePaid, deadline as stopService, 
                deadline2 as stopService2, progress, clientip as clientIp, disabled
                FROM old WHERE disabled = 0';

        return $this->db->prepare($sql)
                        ->execute()
                        ->fetchObject(OldDTO::class);
    }

    public function getCertainOld($id): OldDTO
    {
        $sql = 'SELECT id, clientid as street, tag as name, name as phone, notes, 
                description as remark, startdate as lastInvoicePaid, deadline as stopService, 
                deadline2 as stopService2, progress, clientip as clientIp, disabled
                FROM old WHERE disabled = 0 AND id = :id';

        return $this->db->prepare($sql)
                        ->bindParam('id', $id, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchObject(OldDTO::class)
                        ->current();
    }

    public function disableOldClient($id): bool
    {
        $sql = 'UPDATE old SET disabled = 1 WHERE id = :oldId';

        $this->db->prepare($sql)
                ->bindParam('oldId', $id, \PDO::PARAM_INT)
                ->execute();

        return true;
    }

}