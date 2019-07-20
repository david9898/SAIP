<?php


namespace App\Service;


use App\Repository\NeighborhoodRepositoryInterface;
use App\Repository\StreetRepositoryInterface;

interface StreetServiceInterface
{

    public function getStreetsInTown(StreetRepositoryInterface $streetRepo, NeighborhoodRepositoryInterface $neighborhoodRepo, $townId, $csrfToken): array ;

    public function addStreetInTown(StreetRepositoryInterface $streetRepo, $postArr): bool ;

}