<?php
require_once dirname(__FILE__) . "/statisticsModel.php";

class statistics_photo_filesModel extends statisticsModel{
    public function __construct() {
        parent::__construct();
    }
    
    public function getAll(){
        return $this->select('statistics_photo_files');
    }
    
    public function getStd_photoFile($owner, $date){
        $this->setFilter("owner", "=", $owner);
        $this->setFilter("date", "=", $date, "AND");
        return $this->select('statistics_photo_files');
    }

    public function updateStd_photoFiles($id, $array){
        $this->setFilter('id', '=', $id);
        return $this->update('statistics_photo_files', $array);
    }
    
    public function insertStd_photoFiles(){
        return $this->insert('statistics_photo_files');
    }
    
    public function deletedStd_photoFiles($id){
        $this->setFilter('id', '=', $id);
        return $this->delete('statistics_photo_files');
    }
    
    /*
     * STATISTICS FUNCTIONS
     */
    public function getStatisticReportInfo($date1, $date2){
        $this->setBetweenFilter("date", $date1, 'AND', $date2);
        return $this->select('statistics_photo_files', 'sum');
    }
}