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

    public function delete(string $key): bool
    {
        if ( isset($_SESSION[$key]) ) {
            unset($_SESSION[$key]);

            return true;
        }

        return false;
    }

    public function getFlashMessage(): ?array
    {
        if ( isset($_SESSION['notification']) ) {
            $arr = $_SESSION['notification'];

            unset($_SESSION['notification']);

            return $arr;
        }

        return null;
    }

    public function addFlashMessage(string $key, string $message): bool
    {
        $_SESSION['notification'] = [];
        $_SESSION['notification']['key'] = $key;
        $_SESSION['notification']['message'] = $message;

        return true;
    }
}