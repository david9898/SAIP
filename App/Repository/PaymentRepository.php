<?php


namespace App\Repository;


use App\DTO\PaymentDTO;
use Core\Database\PrepareStatementInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    private $db;

    public function __construct(PrepareStatementInterface $db)
    {
        $this->db = $db;
    }

    public function getLastPayment(int $clientId): ?PaymentDTO
    {
        $sql = 'SELECT end_time as endTime, start_time as startTime FROM payments 
                WHERE client = :clientId ORDER BY id DESC LIMIT 1';

        return $this->db->prepare($sql)
                        ->bindParam('clientId', $clientId, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchObject(PaymentDTO::class)
                        ->current();
    }

    public function addPayment(PaymentDTO $payment): bool
    {
        $sql = 'INSERT INTO payments (time, sum, operator, client)
                VALUES (:time, :sum, :operator, :client);';

        $this->db->prepare($sql)
                ->bindParam('time', $payment->getTime(), \PDO::PARAM_INT)
                ->bindParam('sum', $payment->getSum(), \PDO::PARAM_INT)
                ->bindParam('operator', $payment->getOperator(), \PDO::PARAM_INT)
                ->bindParam('client', $payment->getClient(), \PDO::PARAM_INT)
                ->execute();

        return true;
    }

    public function getClientPayments($clientId): ?\Generator
    {
        $sql = 'SELECT `time`, `sum`, staff.username as operator
                FROM payments JOIN staff ON payments.operator = staff.id
                WHERE payments.`client` = :clientId
                ORDER BY time';

        return $this->db->prepare($sql)
            ->bindParam('clientId', $clientId, \PDO::PARAM_INT)
            ->execute()
            ->fetchObject(PaymentDTO::class);
    }

    public function getLastThreePayments($clientId): ?\Generator
    {
        $sql = 'SELECT end_time as endTime, start_time as startTime FROM payments 
                WHERE client = :clientId ORDER BY id DESC LIMIT 3';

        return $this->db->prepare($sql)
                        ->bindParam('clientId', $clientId, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchObject(PaymentDTO::class);
    }

}