<?php
require_once G_PATH . "models/baseModel.php";

class statisticsModel extends baseModel{
    private $id_db = 'myphotocode_statistics';
    
    public function __construct() {
        parent::__construct($this->id_db);
    }
}