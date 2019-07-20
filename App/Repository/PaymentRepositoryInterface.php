<?php


namespace App\Repository;


use App\DTO\PaymentDTO;

interface PaymentRepositoryInterface
{
    public function getLastPayment(int $clientId): ?PaymentDTO;

    public function addPayment(PaymentDTO $payment): bool;

    public function getClientPayments($clientId): ?\Generator ;

    public function getLastThreePayments($clientId): ?\Generator;
}