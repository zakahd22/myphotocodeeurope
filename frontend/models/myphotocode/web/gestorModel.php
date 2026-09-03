<?php
require_once G_PATH . "models/baseModel.php";

class gestorModel extends baseModel
{
    public function __construct() {
        parent::__construct();
    }

    public function findByCode($code=false){
        $result = [];
        if($code){
            $this->setFilter('code', 'LIKE', $code);
            $result = $this->select('gestor');
        }
        return $result;
    }
}