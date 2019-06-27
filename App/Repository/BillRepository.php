<?php


namespace App\Repository;


use App\DTO\BillDTO;
use Core\Database\PrepareStatementInterface;

class BillRepository implements BillRepositoryInterface
{
    private $db;

    public function __construct(PrepareStatementInterface $db)
    {
        $this->db = $db;
    }

    public function addNewBill(BillDTO $billDTO): bool
    {
        $sql = 'INSERT INTO bills (start, end, sum, client, time)
                VALUES (?, ?, ?, ?, ?)';

        $this->db->prepare($sql)
                ->execute([$billDTO->getStart(), $billDTO->getEnd(),
                            $billDTO->getSum(), $billDTO->getClient(), $billDTO->getTime()]);

        return true;
    }

    public function getBillsOnClient($id): \Generator
    {
        $sql = 'SELECT id, start, end, sum, client, time FROM bills
                WHERE client = :clientId';

        return $this->db->prepare($sql)
                        ->bindParam('clientId', $id, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchObject(BillDTO::class);
    }

    public function removeBill($id): bool
    {
        // TODO: Implement removeBill() method.
    }
}