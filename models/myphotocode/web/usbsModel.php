<?php
require_once G_PATH . "models/baseModel.php";

class usbsModel extends baseModel{
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * idBooth = $id
     * @return type
     */
    public function get_usbs(){
        //$this->setFilter("idBooth", "=", $id);
        return $this->select('usbs');
    }   
    
    /**
     * event_id = $event_id
     * @return type
     */
    public function get_usbsEventId($event_id){
        $this->setFilter("event_id", "=", $event_id);
        return $this->select('usbs');
    }   
    
    /**
     * Delete usbs
     * @param type $event_id
     * @return type
     */
    public function delete_usbsEventID($event_id){
        $this->setFilter("event_id", "=", $event_id);
        return $this->delete('usbs');
    }   
    
    public function insert_usbs(){
        return $this->insert('usbs');
    }
}


