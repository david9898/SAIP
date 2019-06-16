<?php


namespace App\Service;


use App\Repository\ClientRepositoryInterface;
use App\Repository\NeighborhoodRepositoryInterface;
use App\Repository\StreetRepositoryInterface;
use App\Repository\TownRepositoryInterface;

interface ClientServiceInterface
{
    public function addClient(ClientRepositoryInterface $clientRepo, StreetRepositoryInterface $streetRepo,
                              NeighborhoodRepositoryInterface $neighborhoodRepo, TownRepositoryInterface $townRepo,
                              array $post): array ;
}