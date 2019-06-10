<?php


namespace Core\Validation;


use Core\Exception\ValidationExeption;

abstract class Validator
{
    protected function validateEmail(string $email)
    {
        if ( filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            return true;
        }

        throw new ValidationExeption('Email is not valid!!!');
    }

    protected function validateMAC($mac)
    {
        if ( filter_var($mac, FILTER_VALIDATE_MAC) ) {
            return true;
        }

        throw new ValidationExeption('MAC is not valid!!!');
    }

    protected function validateIP($ip)
    {
        if ( filter_var($ip, FILTER_VALIDATE_IP) ) {
            return true;
        }

        throw new ValidationExeption('IP is not valid!!!');
    }

    protected function validateByRegex($string, $regex)
    {
        if ( filter_var($string, FILTER_VALIDATE_REGEXP, array(
            'options' => array("regexp" => $regex)
        ))) {
            return true;
        }

        throw new ValidationExeption('Regx Exeption!!!');
    }

}