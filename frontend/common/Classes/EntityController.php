<?php
/*
 * Entities myphotocode_web
 */
require_once G_PATH . 'common/Classes/Entities/rentals.php';
require_once G_PATH . 'common/Classes/Entities/event_backgrounds.php';
require_once G_PATH . 'common/Classes/Entities/CLD_questions.php';
require_once G_PATH . 'common/Classes/Entities/CLD_boothTypes.php';
require_once G_PATH . 'common/Classes/Entities/CLD_Distributors.php';
require_once G_PATH . 'common/Classes/Entities/CLD_Incidents.php';
require_once G_PATH . 'common/Classes/Entities/CLD_Login.php';
require_once G_PATH . 'common/Classes/Entities/CLD_Inc_coments.php';
require_once G_PATH . 'common/Classes/Entities/CLD_historyBooth.php';
require_once G_PATH . 'common/Classes/Entities/App_boothDongle.php';
require_once G_PATH . 'common/Classes/Entities/App_alerts.php';
require_once G_PATH . 'common/Classes/Entities/App_boothAlert.php';
require_once G_PATH . 'common/Classes/Entities/App_booths.php';
require_once G_PATH . 'common/Classes/Entities/booths.php';
require_once G_PATH . 'common/Classes/Entities/ftp_folders.php';
require_once G_PATH . 'common/Classes/Entities/CLD_subDistributors.php';
require_once G_PATH . 'common/Classes/Entities/App_boothConfigDef.php';
require_once G_PATH . 'common/Classes/Entities/App_boothAlertDef.php';
require_once G_PATH . 'common/Classes/Entities/CLD_estadistiques_upload.php';
require_once G_PATH . 'common/Classes/Entities/CLD_Servers.php';
require_once G_PATH . 'common/Classes/Entities/App_ownerAddress.php';
require_once G_PATH . 'common/Classes/Entities/CLD_ownerConnections.php';
require_once G_PATH . 'common/Classes/Entities/CLD_EventsManegers.php';
require_once G_PATH . 'common/Classes/Entities/CLD_banners.php';
require_once G_PATH . 'common/Classes/Entities/CLD_emailsText.php';
require_once G_PATH . 'common/Classes/Entities/CLD_estadistiques_photos.php';
require_once G_PATH . 'common/Classes/Entities/CLD_questions_emails.php';
require_once G_PATH . 'common/Classes/Entities/events.php';
require_once G_PATH . 'common/Classes/Entities/photos.php';
require_once G_PATH . 'common/Classes/Entities/photo_Files.php';
require_once G_PATH . 'common/Classes/Entities/registre_emails.php';
require_once G_PATH . 'common/Classes/Entities/usbs.php';
require_once G_PATH . 'common/Classes/Entities/Fcode_dongle.php';
require_once G_PATH . 'common/Classes/Entities/Fcode_reg.php';
require_once G_PATH . 'common/Classes/Entities/App_sessions.php';
require_once G_PATH . 'common/Classes/Entities/App_info.php';
require_once G_PATH . 'common/Classes/Entities/manuals.php';
require_once G_PATH . 'common/Classes/Entities/manualsBooths.php';
require_once G_PATH . 'common/Classes/Entities/manualsItems.php';
require_once G_PATH . 'common/Classes/Entities/gestor.php';
require_once G_PATH . 'common/Classes/Entities/InstagramSuggestions.php';
require_once G_PATH . 'common/Classes/Entities/App_bootDCAllowed.php';
require_once G_PATH . 'common/Classes/Entities/App_boothBootDC.php';
require_once G_PATH . 'common/Classes/Entities/App_infoDeviceMgt.php';


/*
 * Entities duplicates in myphotocode_web and myphotocode_trashed
 */
require_once G_PATH . 'common/Classes/Entities/trashed_CLD_emailsText.php';
require_once G_PATH . 'common/Classes/Entities/trashed_CLD_estadistiques_photos.php';
require_once G_PATH . 'common/Classes/Entities/trashed_CLD_questions_emails.php';
require_once G_PATH . 'common/Classes/Entities/trashed_events.php';
require_once G_PATH . 'common/Classes/Entities/trashed_photos.php';
require_once G_PATH . 'common/Classes/Entities/trashed_photo_Files.php';
require_once G_PATH . 'common/Classes/Entities/trashed_registre_emails.php';
require_once G_PATH . 'common/Classes/Entities/trashed_usbs.php';

/*
 * Entities statistic DB
 */
require_once G_PATH . 'common/Classes/Entities/statistics_photos.php';
require_once G_PATH . 'common/Classes/Entities/statistics_types.php';
require_once G_PATH . 'common/Classes/Entities/statistics_photo_files.php';
require_once G_PATH . 'common/Classes/Entities/view_statistic_photos.php';

class EntityUtility{
    /**
     * @var EntityController
     */
    public $entity;
    
    public function createEntityObject(){
        $this->entity = new EntityController();
    }
    
    public function setEntityObject($entity){
        $this->entity = $entity;
    }
}

class EntityController{
    private $registeredEntities = array('rentals', 'manualsBooths', 'manuals', 'manualsItems', 'CLD_questions', 'event_backgrounds',
                                   'CLD_estadistiques_photos', 'CLD_boothTypes', 'CLD_Distributors',
                                   'CLD_Incidents', 'App_boothDongle', 'App_alerts', 'App_boothAlert',
                                   'App_booths', 'CLD_Login', 'CLD_Inc_coments', 'booths',
                                   'CLD_historyBooth', 'ftp_folders', 'CLD_subDistributors',
                                   'App_boothConfigDef', 'CLD_estadistiques_upload',
                                   'CLD_Servers','App_boothAlertDef', 'App_ownerAddress',
                                   'CLD_ownerConnections', 'CLD_EventsManegers', 'CLD_banners',
                                   'CLD_emailsText', 'CLD_estadistiques_photos', 'CLD_questions_emails',
                                   'events', 'photos', 'photo_Files', 'registre_emails', 'usbs', 'Fcode_dongle',
                                   'Fcode_reg', 'App_sessions', 'App_info',
                                   'trashed_CLD_emailsText', 'trashed_CLD_estadistiques_photos',
                                   'trashed_CLD_questions_emails','trashed_events', 'trashed_photos',
                                   'trashed_photo_Files', 'trashed_registre_emails', 'trashed_usbs', 
                                    'statistics_photos', 'statistics_types', 'statistics_photo_files','view_statistic_photos',
                                    'gestor','InstagramSuggestions','App_bootDCAllowed','App_boothBootDC', 'App_infoDeviceMgt'
                                );
    private $currentEntity;
    private $entityArray;

    //===================== Manager Entity FUNCTIONS =====================

    /**
     * Creates a new entity object and changes the current Entity
     * 
     * @param String $entityName The name of the entity
     */
    public function loadEntity($entityName){
        if($this->existEntity($entityName)){
            $this->entityArray[$entityName] = new $entityName();
            $this->changeEntity($entityName);
        }
    }
    
    /**
     * Changes the current Entity of the entity Controller
     * 
     * @param String $entityName
     */
    public function changeEntity($entityName){
        if($this->loadedEntity($entityName)){
            if($this->currentEntity != $entityName){
                $this->currentEntity = $entityName;
            }
        }
        else{
            throw new Exception("ENTITY ERROR! changeEntity() - Entity $entityName not loaded from before!");
        }
    }
    
    /**
     * Notifies the current Entity of the entity Controller
     * 
     * @param String $entityName
     */
    public function getCurrentEntity(){
        return $this->currentEntity;
    }

    /**
     * Check if an entity is loaded from before
     * 
     * @param String $entityName
     */
    public function loadedEntity($entityName){
        return array_key_exists($entityName, $this->entityArray);
    }

    /**
     * Check if an entity exists
     * 
     * @param String $entityName
     */
    public function existEntity($entityName){
        $result = in_array($entityName, $this->registeredEntities);
        if(!$result){
            throw new Exception("ENTITY ERROR! existEntity() - Entity $entityName does not exist!");                
        }
        
        return $result;
    }
    
    
    //===================== DATA FUNCTIONS =====================
    
    /**
     * Function to get all returned values
     * 
     * @return Associative array containing the values if any, empty array otherwise
     */
    public function getAllValues($getdataSet = 'all', $entityName = null){
        $result = array();
        
        if($entityName != null){
            if($this->existEntity($entityName)){
                $this->changeEntity($entityName);
            }
        }
        
        foreach($this->entityArray[$this->currentEntity]->aDatasets[$getdataSet] as $dataSet){
            if(property_exists($this->currentEntity, $dataSet)){ 
                $result[$dataSet] = $this->entityArray[$this->currentEntity]->{$dataSet};
            }
            else{
                throw new Exception("ENTITY ERROR! getAllValues() - Entity $entityName has not property {$dataSet} declared!");
            }
        }
        
        return $result;
    }
    
    /**
     * Function to get a single value
     * 
     * @param String $field
     * @return mixed Field value on succes, false otherwise
     */
    public function getValue($field){
        if(property_exists($this->currentEntity, $field)){ 
            return $this->entityArray[$this->currentEntity]->{$field};
        }
    }
    
    /**
     * Function to set a field value
     * 
     * @param String $field
     * @param String $value
     * @return mixed Field value on succes, false otherwise
     */
    public function setValue($field, $value){
        if(property_exists($this->currentEntity, $field)){ 
            $this->entityArray[$this->currentEntity]->{$field} = $value;
        }
    }
    
    //===================== DATASET FUNCTIONS =====================
    /**
     * Function to check if a dataSet exist in the current entity
     * @param String $dataSet
     * @return boolean True if exists, false otherwisae
     * @throws Exception If the dataset does not exist
     */
    public function existDataSet($dataSet = 'all'){
        $result = FALSE;
        
        if($dataSet != null){
            if(array_key_exists($dataSet, $this->entityArray[$this->currentEntity]->aDatasets)){
                $result = TRUE;
            }
            else{
                throw new Exception("ENTITY ERROR! getEntityFields() - dataSet {$dataSet} does not exist in Entity {$this->currentEntity}!");                
            }
        }
        
        return $result;
    }
    
    /**
     * Get an specific dataset, previously defined in aDatasets in the entities. A dataset, for example, is a view where you need a specific values of an entity.
     * 
     * @param String $dataSet The string representation of the dataset
     * @return string A string with the fields of the dataset with coma-separated 
     */
    public function getEntityFields($dataSet = 'all'){
        $result = "";
        
        if($this->existDataSet($dataSet)){        
            $countDataSet = count($this->entityArray[$this->currentEntity]->aDatasets[$dataSet]);
            for($i = 0; $i < $countDataSet; $i++){
                $data = $this->entityArray[$this->currentEntity]->aDatasets[$dataSet][$i];
                if($data == "counter") $result .= "count({$this->currentEntity}.{$this->entityArray[$this->currentEntity]->aDatasets[$dataSet][$i+1]}) as {$data} ";
                else if($data == "summation") $result .= "max({$this->currentEntity}.{$this->entityArray[$this->currentEntity]->aDatasets[$dataSet][$i+1]}) as {$data} ";
                else if($data == "summation_") $result .= "SUM({$this->currentEntity}.{$this->entityArray[$this->currentEntity]->aDatasets[$dataSet][$i+1]}) as {$data} ";
                else $result .= "{$this->currentEntity}.`$data`";
                if($i < $countDataSet -1) $result .= ", ";
            }
        }
        
        return $result;
    }

    
    /**
     * Function to get an array of all the datasets of the entity
     * 
     * @param String $dataSet
     * @return array Containing all the datasets, empty array if any
     * @throws Exception 
     */
    public function getEntityDataSet($dataSet = 'all'){
        $result = array();
        
        if($this->existDataSet($dataSet)){          
            $result = $this->entityArray[$this->currentEntity]->aDatasets[$dataSet];
        }
        
        return $result;
    }
}