<?php


namespace instagram\smartprint\domain;

class AppBoothDongle
{
    private $dongle;
    private $idBooth;

    public function __construct($idBooth, $dongle)
    {
        $this->dongle = $dongle;
        $this->idBooth = $idBooth;
    }

    public function getDongle(){
        return $this->dongle;
    }

    /**
     * @return mixed
     */
    public function getIdBooth()
    {
        return $this->idBooth;
    }

}