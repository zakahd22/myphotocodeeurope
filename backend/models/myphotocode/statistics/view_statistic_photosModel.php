<?php
require_once dirname(__FILE__) . "/statisticsModel.php";

class view_statistic_photosModel extends statisticsModel{
    public function __construct() {
        parent::__construct();
    }
    
    public function getStatisticsEvent($event = false){
        if($event){
            $this->setFilter('event', '=', $event);
        }
        $this->setGroup('type_info');
        return $this->select('view_statistic_photos', 'eventInfo');
    }
    
    public function getStatisticsPhoto($photoCode = false){
        if($photoCode){
            $this->setFilter('code_photo', '=', $photoCode);
        }
        $this->setGroup('type_info');
        return $this->select('view_statistic_photos', 'eventInfo');
    }
    
    public function getStatisticsPhotoType($photoCode = false, $type = false){
        if($photoCode){
            $this->setFilter('code_photo', '=', $photoCode);
        }
        if($type){
            $this->setFilter('type_info', '=', $type, "AND");
        }
        return $this->select('view_statistic_photos');
    }
    
    public function getStatisticsPhotoTypeFromTable($photoCode = false, $type = false){
        if($photoCode){
            $this->setFilter('code_photo', '=', $photoCode);
        }
        if($type){
            $this->setFilter('type_info', '=', $type, "AND");
        }
        return $this->select('statistic_photos');
    }
    
     public function getStatisticsAllByEvent($event = false){
//        if($event){
//            $this->setFilter('event', '=', $event);
//        }
//        return $this->select('view_statistic_photos', 'view_statistic_photos');
        
        $sql = "
            SELECT *
            FROM `statistics_photos`
            WHERE event = {$event}
           
        ";
                
        $query = $this->my_query($sql);
        
        $result = array();
        while($counter = $this->my_fetch_array($query)){
            $result[$counter["code_photo"]][$counter["type_info"]]= $counter["num_show"];
           
        }
//        print "<pre>";
//        print_r($result);print_r($result2);exit;
        return $result;
    }
    
}