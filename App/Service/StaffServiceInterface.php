<?php


namespace App\Service;


use App\Repository\StaffRepositoryInterface;

interface StaffServiceInterface
{
    public function login(StaffRepositoryInterface $staffRepo, array $post): bool;
}