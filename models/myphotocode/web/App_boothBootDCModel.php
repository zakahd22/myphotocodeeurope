<?php
require_once G_PATH . "models/baseModel.php";

class App_boothBootDCModel extends baseModel{


    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the event from the id
     */
    public function getApp_boothBootDC(){
        return $this->select('App_boothBootDC');
    }
   
    //Si cal fer-lo s'hauria d'editar tenint en compte tots els camps, no id
    //Però no caldrà perquè això és un registre de les versions que van comunicant els PBs a UPGRADEcheck.php
//    public function updateApp_boothBootDC($idbDCAll, $updates){
//        $this->setFilter('id', '=', $idbDCAll);      
//        return $this->update('App_boothBootDC', $updates);
//    }
    
    public function getUPGRADEids(){
        $this->setGroup('App_boothBootDC.UPGRADEid');
        $this->setOrder('datetimeUpgCheck', 'DESC');
        return $this->select('App_boothBootDC');
    }
    
}
