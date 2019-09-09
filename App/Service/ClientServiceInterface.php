<?php


namespace App\Service;


use App\Repository\AbonamentRepositoryInterface;
use App\Repository\ClientRepositoryInterface;
use App\Repository\InvoiceRepositoryInterface;
use App\Repository\NeighborhoodRepositoryInterface;
use App\Repository\PaymentRepositoryInterface;
use App\Repository\StreetRepositoryInterface;
use App\Repository\TownRepositoryInterface;

interface ClientServiceInterface
{
    public function addClient(ClientRepositoryInterface $clientRepo, StreetRepositoryInterface $streetRepo,
                              NeighborhoodRepositoryInterface $neighborhoodRepo, TownRepositoryInterface $townRepo,
                              AbonamentRepositoryInterface $abonamentRepo, PaymentRepositoryInterface $paymentRepo,
                              InvoiceRepositoryInterface $invoiceRepo, AbonamentServiceInterface $abonamentService,
                              $post): array ;

    public function getClients(ClientRepositoryInterface $repository, $firstResult, $csrfToken): array;

    public function getSearchFriends(ClientRepositoryInterface $clientRepository, $firstResult, $csrfToken, $pattern): array;

    public function calculateClientsTimeToBills(?\Generator $clients): ?array;

}