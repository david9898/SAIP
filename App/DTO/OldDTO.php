<?php


namespace App\DTO;


class OldDTO
{
    private $id;
    private $street;
    private $name;
    private $phone;
    private $notes;
    private $remark;
    private $lastInvoicePaid;
    private $stopService;
    private $stopService2;
    private $progress;
    private $clientIp;
    private $disabled;

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
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street): void
    {
        $this->street = $street;
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
    public function setName($name): void
    {
        $this->name = $name;
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
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes): void
    {
        $this->notes = $notes;
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
    public function getStopService()
    {
        return $this->stopService;
    }

    /**
     * @param mixed $stopService
     */
    public function setStopService($stopService): void
    {
        $this->stopService = $stopService;
    }

    /**
     * @return mixed
     */
    public function getStopService2()
    {
        return $this->stopService2;
    }

    /**
     * @param mixed $stopService2
     */
    public function setStopService2($stopService2): void
    {
        $this->stopService2 = $stopService2;
    }

    /**
     * @return mixed
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * @param mixed $progress
     */
    public function setProgress($progress): void
    {
        $this->progress = $progress;
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

    /**
     * @return mixed
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * @param mixed $disabled
     */
    public function setDisabled($disabled): void
    {
        $this->disabled = $disabled;
    }

}