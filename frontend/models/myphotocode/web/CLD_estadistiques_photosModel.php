<?php
require_once G_PATH . "models/baseModel.php";

class CLD_estadistiques_photosModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the EstadistiquesPhoto from the $photo_id
     */
    
    public function getEstadistiquesPhoto($photo){
        $this->setFilter('photo', '=', $photo);
        return $this->select('CLD_estadistiques_photos');
    }
    
    public function getAll($LIMIT = false){
        if($LIMIT){
            $this->setLimit($LIMIT);
        }
        return $this->select('CLD_estadistiques_photos');
    }
    
    /**
     * Get the EstadistiquesPhoto from estadistiquesPhotos
     */
    public function getEstadistiquesPhotosCount($photoCode){
        $this->setFilter("photo", "LIKE", $photoCode);
        $this->setGroup("type_info");
        return $this->select('CLD_estadistiques_photos', 'count');
    }
    
    /**
     * Get the EstadistiquesPhoto from the $photo_id
     */
    public function getEstadistiquesPhotoForEvents($photo, $type_info){
        $this->setFilter('photo', '=', $photo);
        $this->setFilter('type_info', '=', $type_info, "AND");
        return $this->select('CLD_estadistiques_photos', "count_");
    }
    
    
    /**
     * Get the StaticsPhotos from the $photo_id and photo table
     */
    public function get_StaticsPhotos($event_id){
        $fields = "";

        $this->entity->loadEntity('CLD_estadistiques_photos');
        $fields .= $this->entity->getEntityFields("event_statistics");  

        $sql = "
            SELECT {$fields} 
            FROM (
                SELECT a.`photo`, a.`type_info`, count(a.photo) as counter_photos
                FROM `CLD_estadistiques_photos` AS a
                INNER JOIN `photos`
                ON a.`photo` = `photos`.`code`
                AND `photos`.`event_id` = $event_id
                GROUP BY a.`type_info`, a.`photo`
            ) AS CLD_estadistiques_photos
        ";
        
        $this->setGroup('CLD_estadistiques_photos.type_info');
        $this->setOrder('CLD_estadistiques_photos`.`type_info`, CLD_estadistiques_photos.`counter_photos', "DESC");
        //$this->setOrder('CLD_estadistiques_photos`.`counter_photos', 'DESC');
        
        $query = $this->my_query($sql);
        $result = $this->requestQueryResults($query, 'event_statistics');
            
        return $result;
    }
    
    
    /**
     * 
     * @return type
     */
    public function insertEstaditiquesPhoto(){
        return $this->insert('CLD_estadistiques_photos');
    }
    
    public function deleteEstaditiquesPhoto($id){
        $this->setFilter('id', '=', $id);
        return $this->delete('CLD_estadistiques_photos');
    }
}
