<?php
require_once G_PATH . "models/baseModel.php";

class App_boothAlertModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the boothDongle from the ?
     */
    
    public function getboothAlert(){
        return $this->select('App_boothAlert');
    }
    
    /**
     * Get the boothDongle from App_booth
     */
    public function getboothAlerts($booth_id){
        $this->setFilter('estat', '<=', 2);
        $this->setFilter('idBooth', '=', $booth_id, 'AND');
        $this->setOrder('App_boothAlert`.`when', 'DESC');
//        $result = $this->select('App_boothAlert', 'count');
        $result = $this->select('App_boothAlert');
//        utils::log('TRACE 0', 'logAleix');
//        utils::log($this->get_sql_string(), 'logAleix');
        return $result;
    }
}
