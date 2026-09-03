<?php
require_once dirname(__FILE__) . "/statisticsModel.php";

class statistics_photosModel extends statisticsModel{
    public function __construct() {
        parent::__construct();
    }
    
    public function getAll(){
        return $this->select('statistics_photos');
    }
    
    public function getStatisticEvent($idEvent = false){
        if($idEvent || $idEvent == 0){
            $this->setFilter('event', '=', $idEvent);
        }
        return $this->select('statistics_photos');
    }
    
    public function getStatistic($code, $date, $type, $state, $country){
        if($code){
            $this->setFilter('code_photo', 'LIKE', $code);
        }
        if($date){
            $this->setFilter('date', '=', $date, "AND");
        }
        if($type){
            $this->setFilter('type_info', '=', $type, "AND");
        }
        if($state && $state!= NULL){
            $this->setFilter('state', '=', $state, "AND");
        }
        if($country && $country!= NULL){
            $this->setFilter('country', '=', $country, "AND");
        }
        return $this->select('statistics_photos', 'count');
    }
    
    public function getStatisticVisitCloudInfo($date1, $date2, $array){
        $this->setBetweenFilter("date", $date1, 'AND', $date2);
        $this->setInFilter('type_info', $array, "AND");
        
        $result = $this->select('statistics_photos', 'sum');
        return $result;
    }
    
    public function getStatisticSocialInfo($date1, $date2, $type){
        $this->setBetweenFilter("date", $date1, 'AND', $date2);
        $this->setFilter('type_info', "=", $type, "AND");
        
        $result = $this->select('statistics_photos', 'sum');
        return $result;
    }
    
    
    

    public function updateStd_photos($id, $array){
        $this->setFilter('id', '=', $id);
        return $this->update('statistics_photos', $array);
    }
    
    public function insertStd_photos(){
        return $this->insert('statistics_photos');
    }
    
    public function deletedStd_photos($id){
        $this->setFilter('id', '=', $id);
        return $this->delete('statistics_photos');
    }
    
    
}