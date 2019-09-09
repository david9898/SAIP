<?php


namespace App\Repository;


use App\DTO\InvoiceDTO;
use Core\Database\PrepareStatementInterface;

class InvoiceRepository implements InvoiceRepositoryInterface
{

    private $db;

    public function __construct(PrepareStatementInterface $db)
    {
        $this->db = $db;
    }

    public function addInvoice(InvoiceDTO $invoice): bool
    {
        $sql = 'INSERT INTO invoices(`start`, `end`, `sum`, `client`, `time`, `operator`, `paid_time`)
                VALUES (:start, :end, :sum, :client, :time, :operator, :paidTime)';

        $this->db->prepare($sql)
                ->bindParam('start', $invoice->getStart(), \PDO::PARAM_INT)
                ->bindParam('end', $invoice->getEnd(), \PDO::PARAM_INT)
                ->bindParam('sum', $invoice->getSum(), \PDO::PARAM_INT)
                ->bindParam('client', $invoice->getClient(), \PDO::PARAM_INT)
                ->bindParam('time', $invoice->getTime(), \PDO::PARAM_INT)
                ->bindParam('operator', $invoice->getOperator(), \PDO::PARAM_INT)
                ->bindParam('paidTime', $invoice->getTimePaid(), \PDO::PARAM_INT)
                ->execute();

        return true;
    }

    public function getInvoicesOnClient($clientId): \Generator
    {
        $sql = 'SELECT id, start, end, sum, client, time, paid_time as timePaid
                FROM invoices 
                WHERE client = :clientId';

        return $this->db->prepare($sql)
                        ->bindParam('clientId', $clientId, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchObject(InvoiceDTO::class);
    }

    public function getUnpaidInvoicesOnClient($clientId): ?\Generator
    {
        $sql = 'SELECT id, start, end, sum, client, time, paid_time as timePaid
                FROM invoices 
                WHERE client = :clientId AND paid_time IS NULL';

        return $this->db->prepare($sql)
                        ->bindParam('clientId', $clientId, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchObject(InvoiceDTO::class);
    }

    public function payInvoice($invoiceId, $operator, $time): bool
    {
        $sql = 'UPDATE invoices 
                SET paid_time = :paidTime, operator = :operator 
                WHERE id = :id';

        $this->db->prepare($sql)
                ->bindParam('paidTime', $time, \PDO::PARAM_INT)
                ->bindParam('operator', $operator, \PDO::PARAM_INT)
                ->bindParam('id', $invoiceId, \PDO::PARAM_INT)
                ->execute();

        return true;
    }

    public function payAllInvoices($clientId, $operator, $time): bool
    {
        $sql = 'UPDATE invoices 
                SET paid_time = :paidTime, operator = :operator 
                WHERE client = :client';

        $this->db->prepare($sql)
                ->bindParam('client', $clientId, \PDO::PARAM_INT)
                ->bindParam('paidTime', $time, \PDO::PARAM_INT)
                ->bindParam('operator', $operator, \PDO::PARAM_INT)
                ->execute();

        return true;
    }

    public function getLastInvoiceOnClient($clientId): InvoiceDTO
    {
        $sql = 'SELECT end FROM invoices WHERE client = :clientId ORDER BY id DESC LIMIT 1';

        return $this->db->prepare($sql)
                        ->bindParam('clientId', $clientId, \PDO::PARAM_INT)
                        ->execute()
                        ->fetchObject(InvoiceDTO::class)
                        ->current();
    }

}