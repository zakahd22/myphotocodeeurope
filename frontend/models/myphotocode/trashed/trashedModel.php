<?php
require_once G_PATH . "models/baseModel.php";

class trashedModel extends baseModel{
    private $id_db = 'myphotocode_trashed';
    
    public function __construct() {
        parent::__construct($this->id_db);
    }
}