<?php


namespace App\Repository;


use App\DTO\PaymentDTO;
use Core\Database\PrepareStatementInterface;
use phpDocumentor\Reflection\Types\This;

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

    public function getClientPayments($clientId): ?\Generator
    {
        $sql = 'SELECT `time`, `start_time` as startTime, `end_time` as endTime, `sum`, staff.username
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