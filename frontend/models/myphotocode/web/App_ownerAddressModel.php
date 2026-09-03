<?php
require_once G_PATH . "models/baseModel.php";

class App_ownerAddressModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the boothDongle from the ?
     */
    public function getOwnerAddress($owner){
        $this->setFilter('owner', '=', $owner);
        return $this->select('App_ownerAddress');
    }
    
    public function updateOwnerAddress($id, $updates){

        return $this->update('App_ownerAddress', $updates);
    }
    
    public function insertOwnerAddress(){
        return $this->insert('App_ownerAddress');
    }
}


