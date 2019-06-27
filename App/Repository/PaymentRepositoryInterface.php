<?php


namespace App\Repository;


use App\DTO\PaymentDTO;

interface PaymentRepositoryInterface
{
    public function getLastPayment(int $clientId): ?PaymentDTO;

    public function getAllPayments(int $clientId): ?\Generator;

    public function addPayment(PaymentDTO $payment): bool;
}