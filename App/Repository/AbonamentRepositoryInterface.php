<?php


namespace App\Repository;


use App\DTO\AbonamentDTO;

interface AbonamentRepositoryInterface
{

    public function getAbonaments(): \Generator;

    public function checkIfAbonamentExist(string $name): ?AbonamentDTO;

    public function addAbonament(AbonamentDTO $abonamentDTO): bool;
}