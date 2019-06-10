<?php


namespace Core\Session;


interface SessionInterface
{

    public function get(string $key);

    public function set(string $key, $value): bool;

    public function getAll(): array;

    public function checkIfKeyExist(string $key): bool;

    public function checkForKeyValue(string $key, $value): bool;
}