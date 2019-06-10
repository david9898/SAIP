<?php


namespace Core\Session;


use Core\Exception\SessionException;

class Session implements SessionInterface
{

    public function __construct()
    {
        if ( session_status() !== PHP_SESSION_ACTIVE ) {
            session_start();
        }else {
            if ( session_status() === PHP_SESSION_DISABLED ) {
                throw new SessionException("Sessions are not valid!!!");
            }
        }
    }

    public function get(string $key)
    {
        if ( isset($_SESSION[$key]) ) {
            return $_SESSION[$key];
        }
    }

    public function set(string $key, $value): bool
    {
        $_SESSION[$key] = $value;

        return true;
    }

    public function getAll(): array
    {
        return $_SESSION;
    }

    public function checkIfKeyExist(string $key): bool
    {
        if ( isset($_SESSION[$key]) ) {
            return true;
        }else {
            return false;
        }
    }

    public function checkForKeyValue(string $key, $value): bool
    {
        if ( $_SESSION[$key] === $value ) {
            return true;
        }else {
            return false;
        }
    }
}