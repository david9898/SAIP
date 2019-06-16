<?php


namespace App\Repository;


use App\DTO\ClientDTO;

interface ClientRepositoryInterface
{
    public function addClient(ClientDTO $client): bool;
}