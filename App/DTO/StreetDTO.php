<?php


namespace App\DTO;


class StreetDTO
{
    private $id;
    private $name;
    private $townId;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getTownId()
    {
        return $this->townId;
    }

    /**
     * @param mixed $townId
     */
    public function setTownId($townId): void
    {
        $this->townId = $townId;
    }

}