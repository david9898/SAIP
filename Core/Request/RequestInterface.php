<?php


namespace Core\Request;


interface RequestInterface
{
    public function getPOST(): array;

    public function getGET(): array;

    public function isSubmit(string $buttonName): bool;
}