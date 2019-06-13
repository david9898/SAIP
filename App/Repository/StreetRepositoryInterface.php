<?php


namespace App\Repository;


interface StreetRepositoryInterface
{
    public function getTownStreets($id): array;
}