<?php


namespace App\Service;


use App\DTO\PaymentDTO;
use App\Repository\PaymentRepositoryInterface;

interface PaymentServiceInterface
{
    public function getClientPayments(PaymentRepositoryInterface $paymentRepository, $clientId): ?array ;

    public function makePaymentsReadable(\Generator $payments): array;

}