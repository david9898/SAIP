<?php


namespace App\Repository;


interface TownRepositoryInterface
{
    public function getTowns(): \Generator;

    public function checkForStreetInTown($townId, $streetId): bool;
}