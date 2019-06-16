<?php


namespace Core\Session;


interface SessionInterface
{

    public function get(string $key);

    public function set(string $key, $value): bool;

    public function getAll(): array;

    public function checkIfKeyExist(string $key): bool;

    public function checkForKeyValue(string $key, $value): bool;

    public function delete(string $key): bool;

    public function addFlashMessage(string $key, string $message): bool;

    public function getFlashMessage(): ?array;
}