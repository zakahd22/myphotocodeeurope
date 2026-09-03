<?php
require_once dirname(__FILE__) . "/trashedModel.php";

class trashed_registre_emailsModel extends trashedModel{

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the event from the id
     */
    public function getRegistreEmail(){
        return $this->select('trashed_registre_emails');
    }
    
}