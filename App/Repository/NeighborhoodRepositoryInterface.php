<?php


namespace App\Repository;


interface NeighborhoodRepositoryInterface
{
    public function getTownNeighborhoods($townId): \Generator;

    public function checkTownHaveNeighborhood($townId): bool ;

    public function getNeighborhoodId($name): ?int ;

    public function checkForValidNeighborhood($townId, $neighborhoodId): bool ;
}