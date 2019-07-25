<?php


namespace Core\Validation;


use Core\Exception\ValidationExeption;

class Validator
{
    public static function validateEmail(string $email)
    {
        if ( filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            return true;
        }

        throw new ValidationExeption('Имейла не е валиден');
    }

    public static function validateMAC($mac)
    {
        if ( filter_var($mac, FILTER_VALIDATE_MAC) ) {
            return true;
        }

        throw new ValidationExeption('МАК адреса не е валиден');
    }

    public static function validateIP($ip)
    {
        if ( filter_var($ip, FILTER_VALIDATE_IP) ) {
            return true;
        }

        throw new ValidationExeption('ИП адреса не е валиден');
    }

    public static function validateByRegex($string, $regex)
    {
        if ( filter_var($string, FILTER_VALIDATE_REGEXP, array(
            'options' => array("regexp" => $regex))))
        {
            return true;
        }

        throw new ValidationExeption('Полетата не отговарят на зададените критерии');
    }

    public static function notEmpty($string)
    {
        if ( $string !== null && $string !== '' ) {
            return true;
        }

        throw new ValidationExeption('Полетата със звезда трябва да са попълнени');
    }

    public static function validateInt($num)
    {
        if ( filter_var($num, FILTER_VALIDATE_INT) ) {
            return true;
        }

        throw new ValidationExeption('Невалидни данни!');
    }

    public static function validateBgCharacters($string)
    {
        if ( filter_var($string, FILTER_VALIDATE_REGEXP , ['options' => ['regexp' => '/[а-яА-Я]+/']]) ) {
            return true;
        }

        throw new ValidationExeption('Позволени са само български букви!');
    }

    public static function validatePhone($string)
    {
        if ( filter_var($string, FILTER_VALIDATE_REGEXP , ['options' => ['regexp' => '/^[0-9]*$/']]) ) {
            return true;
        }

        throw new ValidationExeption('Невалиден телефон!');
    }
}