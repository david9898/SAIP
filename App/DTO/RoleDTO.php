<?php


namespace App\DTO;


class RoleDTO
{
    private $id;
    private $roleName;

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
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getRoleName()
    {
        return $this->roleName;
    }

    /**
     * @param mixed $roleName
     */
    public function setRoleName($roleName): void
    {
        $this->roleName = $roleName;
    }

}