<?php


namespace App\Service;


use App\Repository\ClientRepositoryInterface;
use App\Repository\PaymentRepositoryInterface;
use Core\Request\Request;

interface PaymentServiceInterface
{
    public function getClientPayments(PaymentRepositoryInterface $paymentRepository, $clientId): ?array ;

    public function makePaymentsReadable(\Generator $payments): array;

    public function addPayment(Request $request, PaymentRepositoryInterface $paymentRepo,
                               ClientRepositoryInterface $clientRepo): array;

}