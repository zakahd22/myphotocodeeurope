<?php
require_once G_PATH . "models/baseModel.php";

class CLD_subDistributorsModel extends baseModel{
    public $event;
    public $CLD_subDistributors_array;
    public $CLD_questionsModel;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the event from the id
     */
    public function getCLD_SubDistributor(){
        return $this->select('CLD_subDistributors');
    }
    
    /**
     * Get all subdistributors.
     */
    public function getCLD_SubDistributors(){
        return $this->select('CLD_subDistributors');
    }
}
