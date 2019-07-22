<?php


namespace App\Service;


use App\Repository\ClientRepositoryInterface;
use App\Repository\NeighborhoodRepositoryInterface;
use App\Repository\PaymentRepositoryInterface;
use App\Repository\StreetRepositoryInterface;
use App\Repository\TownRepositoryInterface;
use Core\Request\Request;

interface ClientServiceInterface
{
    public function addClient(ClientRepositoryInterface $clientRepo, StreetRepositoryInterface $streetRepo,
                              NeighborhoodRepositoryInterface $neighborhoodRepo, TownRepositoryInterface $townRepo,
                              array $post): array ;

    public function getClients(ClientRepositoryInterface $repository): array;

    public function addPayment(Request $request, PaymentRepositoryInterface $paymentRepo,
                                ClientRepositoryInterface $clientRepo): array;

    public function calculateBills(PaymentRepositoryInterface $paymentRepo, $id): array;

    public function checkIfPaymentsAreReadable(?\Generator $payments): ?bool;

    public function calculateBillsIfTrueOrNull(PaymentRepositoryInterface $paymentRepo, $id): array;

    public function calculateBillsIfFalse(PaymentRepositoryInterface $paymentRepo, $id): array;

    public function makeBillsReadable(array $bills, $lastThreePayments): array;

}