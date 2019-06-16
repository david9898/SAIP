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

        throw new ValidationExeption('Имейла не е валиден');
    }

    protected function validateMAC($mac)
    {
        if ( filter_var($mac, FILTER_VALIDATE_MAC) ) {
            return true;
        }

        throw new ValidationExeption('МАК адреса не е валиден');
    }

    protected function validateIP($ip)
    {
        if ( filter_var($ip, FILTER_VALIDATE_IP) ) {
            return true;
        }

        throw new ValidationExeption('ИП адреса не е валиден');
    }

    protected function validateByRegex($string, $regex)
    {
        if ( filter_var($string, FILTER_VALIDATE_REGEXP, array(
            'options' => array("regexp" => $regex)
        ))) {
            return true;
        }

        throw new ValidationExeption('Полетата не отговарят на зададените критерии');
    }

    protected function notEmpty($string)
    {
        if ( $string !== null && $string !== '' ) {
            return true;
        }

        throw new ValidationExeption('Полетата със звезда трябва да са попълнени');
    }
}