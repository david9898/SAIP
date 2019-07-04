<?php

namespace App\DTO;

use Core\Validation\Validator;

class ClientDTO extends Validator
{
    private $id;
    private $town;
    private $abonament;
    private $firstName;
    private $lastName;
    private $phone;
    private $email;
    private $street;
    private $neighborhood;
    private $dateRegister;
    private $description;
    private $streetNumber;
    private $paid;
    private $sum;
    private $register;

    public function __construct()
    {
        $this->setNeighborhood(null);
        $this->setDescription(null);
        $this->dateRegister = time();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * @param mixed $town
     */
    public function setTown($town)
    {
        if ( $this->notEmpty($town) ) {
            $this->town = htmlspecialchars($town);
        }
    }

    /**
     * @return mixed
     */
    public function getAbonament()
    {
        return $this->abonament;
    }

    /**
     * @param mixed $abonament
     */
    public function setAbonament($abonament)
    {
        if ( $this->notEmpty($abonament) ) {
            $this->abonament = $abonament;
        }
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        if ( $this->notEmpty($firstName) ) {
            $this->firstName = htmlspecialchars($firstName);
        }
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        if ( $this->notEmpty($lastName) ) {
            $this->lastName = htmlspecialchars($lastName);
        }
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        if ( $this->notEmpty($phone) ) {
            $this->phone = htmlspecialchars($phone);
        }
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
            $this->email = htmlspecialchars($email);
        }
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street)
    {
        if ( $this->notEmpty($street) ) {
            $this->street = $street;
        }
    }

    /**
     * @return mixed
     */
    public function getNeighborhood()
    {
        return $this->neighborhood;
    }

    /**
     * @param mixed $neighborhood
     */
    public function setNeighborhood($neighborhood)
    {
        $this->neighborhood = $neighborhood;
    }

    /**
     * @return mixed
     */
    public function getDateRegister()
    {
        return $this->dateRegister;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = htmlspecialchars($description);
    }

    /**
     * @return mixed
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * @param mixed $streetNumber
     */
    public function setStreetNumber($streetNumber)
    {
        if ( $this->notEmpty($streetNumber) ) {
            $this->streetNumber = htmlspecialchars($streetNumber);
        }
    }

    /**
     * @return mixed
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * @param mixed $paid
     */
    public function setPaid($paid): void
    {
        $this->paid = $paid;
    }

    /**
     * @return mixed
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * @param mixed $sum
     */
    public function setSum($sum): void
    {
        $this->sum = $sum;
    }

    /**
     * @return mixed
     */
    public function getRegister()
    {
        return $this->register;
    }

    /**
     * @param mixed $register
     */
    public function setRegister($register): void
    {
        $this->register = $register;
    }
}