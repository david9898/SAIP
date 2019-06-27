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

    public function calculateBills($lastPayment, $lastTime): array;
}