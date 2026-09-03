<?php
/*
 * Dades de connexió i tipus de base
 */
require("APP_BdD_MySQL.php");
$APP_BdD_error = "";
$APP_BdD = new BdD();
//202009 if(!$APP_BdD->OpenBdD('db399687929.db.1and1.com','dbo399687929','digitalcentre','db399687929')){
//if(!$APP_BdD->OpenBdD('db399687929.db.1and1.com','dbo399687929','cloudPB:w3b!','db399687929')){//202009 
if(!$APP_BdD->OpenBdD('db5004979558.hosting-data.io','dbu2373333','cloudPB:w3b!','dbs4165998')){//202110

//    echo $APP_BdD->error;
    $APP_BdD_error = $APP_BdD->error;
}

function getNewBdD(){
    $newBdD = new BdD();
//202009     $newBdD->OpenBdD('db399687929.db.1and1.com','dbo399687929','digitalcentre','db399687929');
//    $newBdD->OpenBdD('db399687929.db.1and1.com','dbo399687929','cloudPB:w3b!','db399687929');//202009 
    $newBdD->OpenBdD('db5004979558.hosting-data.io','dbu2373333','cloudPB:w3b!','dbs4165998');//202110
    return $newBdD;
}

//taula App_booths SELECT `idBooth`,`type`,`owner`,`name`,`obs`,`serialnumber`,`location`,`latitude`,`longitude`, FROM `App_booths` WHERE 1
//taula App_BoothDongle SELECT `idBooth`, `idDongle`, `datetimeS`, `datetimeF` FROM `App_BoothDongle` WHERE 1
//taula booths SELECT `id`, `dongle`, `reference`, `rand_string`, `rental_id` FROM `booths` WHERE 1
//taula booth_types SELECT `id`, `char`, `name`, `logo_w`, `logo_h`, `frames_w`, `frames_h`, `welcome_w`, `welcome_h`, `banner_w`, `banner_h`, `custom_w`, `custom_h`, `screens` FROM `booth_types` WHERE 1

?>
