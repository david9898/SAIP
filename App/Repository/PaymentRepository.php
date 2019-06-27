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
        $sql = 'SELECT end_time as endTime FROM payments 
                WHERE client = :clientId ORDER BY id DESC LIMIT 1';

        return $this->db->prepare($sql)
                        ->bindParam('clientId', $clientId, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchObject(PaymentDTO::class)
                        ->current();
    }

    public function getAllPayments(int $clientId): ?\Generator
    {

    }

    public function addPayment(PaymentDTO $payment): bool
    {
        $sql = 'INSERT INTO payments (time, start_time, end_time, sum, operator, client)
                VALUES (:time, :start, :end, :sum, :operator, :client)';

        $this->db->prepare($sql)
                ->bindParam('time', $payment->getTime(), \PDO::PARAM_INT)
                ->bindParam('start', $payment->getStartTime(), \PDO::PARAM_INT)
                ->bindParam('end', $payment->getEndTime(), \PDO::PARAM_INT)
                ->bindParam('sum', $payment->getSum(), \PDO::PARAM_INT)
                ->bindParam('operator', $payment->getOperator(), \PDO::PARAM_INT)
                ->bindParam('client', $payment->getClient(), \PDO::PARAM_INT)
                ->execute();

        return true;
    }
}