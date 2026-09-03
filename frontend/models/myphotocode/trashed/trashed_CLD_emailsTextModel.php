<?php
require_once dirname(__FILE__) . "/trashedModel.php";

class trashed_CLD_emailsTextModel extends trashedModel{

    public function __construct() {
        parent::__construct();
    }
    
    public function getAllCLD_emailsText(){
        return $this->select('trashed_CLD_emailsText', "all");
    }
    
    
}
