<?php


namespace App\Repository;


use App\DTO\ClientDTO;
use App\DTO\OldDTO;

interface ClientRepositoryInterface
{
    public function addClient(ClientDTO $client);

    public function getClients($firstResult): ?\Generator;

    public function searchFriends($pattern, $firstResult): ?\Generator;

    public function getClient($id): ?ClientDTO;

    public function getAllClients(): \Generator;

    public function getClientAbonamentPrice($clientId): ClientDTO;

    public function getClientsIdsAndSums(): \Generator;

    /** DELETE */

    public function getFromOld(): \Generator;

    public function getCertainOld($id): OldDTO;

    public function disableOldClient($id): bool;
}