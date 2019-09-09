<?php

namespace App\DTO;

class ClientDTO
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
    private $creditLimit;
    private $lastInvoicePaid;
    private $remark;
    private $nickname;
    private $clientIp;

    public function __construct()
    {
        if ( $this->getNeighborhood() === null ) {
            $this->setNeighborhood(null);
        }
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
       $this->town = htmlspecialchars($town);
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
        $this->abonament = htmlspecialchars($abonament);
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
        $this->firstName = htmlspecialchars($firstName);

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
        $this->lastName = htmlspecialchars($lastName);

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
        $this->phone = htmlspecialchars($phone);
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
        $this->email = htmlspecialchars($email);
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
        $this->street = htmlspecialchars($street);
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
        $this->description = $description;
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
        $this->streetNumber = htmlspecialchars($streetNumber);
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
    public function getCreditLimit()
    {
        return $this->creditLimit;
    }

    /**
     * @param mixed $creditLimit
     */
    public function setCreditLimit($creditLimit): void
    {
        $this->creditLimit = $creditLimit;
    }

    /**
     * @return mixed
     */
    public function getLastInvoicePaid()
    {
        return $this->lastInvoicePaid;
    }

    /**
     * @param mixed $lastInvoicePaid
     */
    public function setLastInvoicePaid($lastInvoicePaid): void
    {
        $this->lastInvoicePaid = $lastInvoicePaid;
    }

    /**
     * @return mixed
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @param mixed $remark
     */
    public function setRemark($remark): void
    {
        $this->remark = $remark;
    }

    /**
     * @return mixed
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @param mixed $nickname
     */
    public function setNickname($nickname): void
    {
        $this->nickname = $nickname;
    }

    /**
     * @return mixed
     */
    public function getClientIp()
    {
        return $this->clientIp;
    }

    /**
     * @param mixed $clientIp
     */
    public function setClientIp($clientIp): void
    {
        $this->clientIp = $clientIp;
    }

}