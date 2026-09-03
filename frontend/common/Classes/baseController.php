<?php

class baseController extends EntityUtility{
    /**
     * Models
     * @DBidentifier myphotocode_web
     */
    public $eventsModel;
    public $photosModel;
    public $gestorModel;
    public $boothsModel;
    public $rentalsModel;
    public $registre_emailsModel;
    public $event_backgroundsModel;
    public $App_boothDongleModel;
    public $App_alertsModel;
    public $App_boothAlertModel;
    public $App_boothsModel;
    public $CLD_questionsModel;
    public $CLD_questions_emailsModel;
    public $CLD_estadistiques_photosModel;
    public $CLD_boothTypesModel;
    public $CLD_DistributorsModel;
    public $CLD_IncidentsModel;
    public $CLD_LoginModel;
    public $CLD_Inc_comentsModel;
    public $CLD_historyBoothModel;
    public $ftp_foldersModel;
    public $CLD_emailsTextModel;
    public $CLD_subDistributorsModel;
    public $App_boothConfigDefModel;
    public $CLD_EventsManegersModel;
    public $App_boothAlertDefModel;
    public $CLD_estadistiques_uploadModel;
    public $photo_FilesModel;
    public $CLD_ServersModel;
    public $App_ownerAddressModel;
    public $CLD_ownerConnectionsModel;
    public $usbsModel;
    public $CLD_bannersModel;
    public $Fcode_dongleModel;
    public $Fcode_regModel;
    public $App_sessionsModel;
    public $RepdcModel;
    public $manualsModel;
    public $InstagramSuggestionsModel;
    public $App_bootDCAllowedModel;
    public $App_boothBootDCModel;
    public $App_infoDeviceMgtModel;
    
    /**
     * @DBidentifier myphotocode_trashed
     * 
     * Models already defined in myphotocode_web, 
     * listed below in annotation format:
     */
    public $trashed_CLD_estadistiques_photosModel;
    public $trashed_CLD_questions_emailsModel;
    public $trashed_eventsModel;
    public $trashed_photosModel;
    public $trashed_photo_FilesModel;
    public $trashed_registre_emailsModel;
    public $trashed_usbsModel;    
    
    
    /**
     * @DBidentifier myphotocode_statistics
     */
    public $statistics_photosModel;
    public $statistics_typesModel;
    public $view_statistic_photosModel;
    public $statistics_photo_filesModel;
    
    //Views
    public $photosVideosView;
    public $listView;
    public $StatisticsReportsView;

    public function __construct() {
        $this->createEntityObject();
    }
    
    private function createPathFromID($id_path){
        $id_path_exploded = explode("_", $id_path);
        foreach($id_path_exploded as $dir){
            $path .= "{$dir}/";
        }
        
        return $path;
    }
    
    /**
     * Function to assign the models to a controller
     * 
     * @param String $model Containing the name of the table
     * @param String $params Containing an Array of parametres
     * @param String $id_path Containing the name of the database
     */
    public function createModel($model, $params = false, $id_path = 'myphotocode_web'){
        $modelName = $model . 'Model';
        $models_path = $this->createPathFromid($id_path);
        $path = "models/{$models_path}";
        $this->createObject($path, $modelName);
    }

    /**
     * Function to assign the views to a controller
     * 
     * @param type $section The name of the section
     * @param type $view    The name of the View
     * @param type $params  Te params to pass
     */
    public function createView($section, $view, $params = false){
        $viewName = $view . 'View'; 
        $path = "common/resources/views/{$section}/";
        $this->createObject($path, $viewName);
    }
    
//    public function createView($section, $view, $params = false){
//        $viewName = $view . 'View'; 
//        $path = "sections/{$section}/views/";
//        $this->createObject($path, $viewName);
//    }
    
    private function createObject($path, $object, $params = false){
        if(property_exists('baseController', $object)){
            require_once G_PATH . "{$path}{$object}.php";
            $this->{$object} = new $object($params);
            $this->{$object}->setEntityObject($this->entity);
        }
        else{
            throw new Exception("Wrong object $object");
        }
    }
}

