<?php
require_once "../../../common/global.php";
require_once G_PATH . "common/Classes/baseController.php";

class photosVideos extends baseController{
    
    
    
    public function __construct($context){
        $this->loadVars();
    }

    /*List view*/
    public function indexAction() {
        
    }
}
