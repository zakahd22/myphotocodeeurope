<?php
require_once dirname(__FILE__) . "/statisticsModel.php";

class statistics_typesModel extends statisticsModel{
    public function __construct() {
        parent::__construct();
    }
    
    public function getAll(){
        return $this->select('statistics_types');
    }

    public function getTable($type){
        $this->setFilter('id', '=', $type);
        return $this->select('statistics_types', 'table');
    }
}