<?php


namespace App\Service;


use App\DTO\AbonamentDTO;
use App\Repository\AbonamentRepositoryInterface;

interface AbonamentServiceInterface
{
    public function addAbonament(AbonamentRepositoryInterface $abonamentRepository, $postArr): bool ;

    public function getIncomeAccount(AbonamentRepositoryInterface $abonamentRepository, $abonamentId, $csrfToken): array ;

}