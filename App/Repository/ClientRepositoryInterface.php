<?php


namespace App\Repository;


use App\DTO\ClientDTO;

interface ClientRepositoryInterface
{
    public function addClient(ClientDTO $client): bool;

    public function getClients(): \Generator;

    public function getMoreClients($firstResult): ?\Generator;

    public function searchFriends($pattern, $firstResult): ?\Generator;

    public function getClient($id): ?ClientDTO;

    public function getAllClients(): \Generator;

    public function getClientAbonamentPrice($clientId): ClientDTO;

}