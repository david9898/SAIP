<?php


namespace App\Repository;


use App\DTO\StreetDTO;

interface StreetRepositoryInterface
{
    public function getTownStreets($id): \Generator;

    public function getStreetByName($name): ?StreetDTO;

    public function addStreet($name): bool ;

    public function addRelationTownStreet($streetId, $townId): bool ;

}