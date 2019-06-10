<?php

namespace App\DTO;

use Core\Validation\Validator;

class ClientDTO extends Validator
{
    private $email;
    private $username;
    private $frontImage;
    private $frontImageOne;
    private $frontImage2;

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getFrontImage()
    {
        return $this->frontImage;
    }

    /**
     * @param mixed $frontImage
     */
    public function setFrontImage($frontImage)
    {
        $this->frontImage = $frontImage;
    }

    /**
     * @return mixed
     */
    public function getFrontImageOne()
    {
        return $this->frontImageOne;
    }

    /**
     * @param mixed $frontImageOne
     */
    public function setFrontImageOne($frontImageOne)
    {
        $this->frontImageOne = $frontImageOne;
    }

    /**
     * @return mixed
     */
    public function getFrontImage2()
    {
        return $this->frontImage2;
    }

    /**
     * @param mixed $frontImage2
     */
    public function setFrontImage2($frontImage2)
    {
        $this->frontImage2 = $frontImage2;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        if ( $this->validateEmail($email) ) {
            $this->email = $email;
        }
    }

}