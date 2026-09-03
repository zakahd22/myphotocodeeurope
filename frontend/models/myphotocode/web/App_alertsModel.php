<?php
require_once G_PATH . "models/baseModel.php";

class App_alertsModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the boothDongle from App_booth
     */
    public function getAlerts(){
        return $this->select('App_alerts');
    }
    /**
     * Get the boothDongle from App_booth
     */
    public function getAlertsByTypeAlert($typeAlert){
        $this->setFilter("typeAlert", "=", $typeAlert);
        return $this->select('App_alerts');
    }
}
