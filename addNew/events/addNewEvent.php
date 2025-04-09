<?php
include '../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('events');
$baseController->createModel('App_booths');
$baseController->createModel('usbs');

$owner = $_POST['owner'];
$data = $_POST['data'];
$title = $_POST['title'];
$private = $_POST['private'];
$data2 = date("Y-m-d");

$baseController->entity->loadEntity('events');
$baseController->entity->setValue("rental_id", $owner);
$baseController->entity->setValue("start_date", $data);
$baseController->entity->setValue("title", $title);
$baseController->entity->setValue("private", $private);
$baseController->entity->setValue("autocreated", 0);
$baseController->entity->setValue("available", 1);
$baseController->entity->setValue("CLD_date_lastPhoto", $data2);


//$idevent = $baseController->eventsModel->insertEvent();
//if(!$idevent)  die("ko#event#insert");

//20150903vicmissatge erròni en fer new Event utils::vd($idevent);
if ($idevent = $baseController->eventsModel->insertEvent()) {
    mkdir( G_PATH . "events/" . $data . $idevent );
    $eventFolder = G_PATH . "events/$data$idevent";
    if(isset($eventFolder)){
//        $CLD_CON2 = clone($CLD_CON);
//        $CLD_CON2->OpenRs("SELECT b.CLD_idType , b.type FROM App_booths b WHERE b.owner=$owner AND b.CLD_idType IS NOT NULL GROUP BY b.CLD_idType");
        $booths = $baseController->App_boothsModel->getBoothsFiltered($owner);
        if (count($booths) == 1) {
            foreach ($booths as $booth){
                $ch = $booth["type"];
                $idType = $booth["CLD_idType"];
                /*                 * ************************************************* CREEM EL USB *********************************************************************************************** */
                /*                 * ************************************************* NOMES SI TE UN SOL TIPUS DE MAQUINA ************************************************************************ */
                $baseController->entity->loadEntity('usbs');
                $baseController->entity->setValue("rental_id", $owner);
                $baseController->entity->setValue("creation_date", $data);
                $baseController->entity->setValue("title", '-USB'.$data.'-');
                $baseController->entity->setValue("boothtype_char", $ch);
                $baseController->entity->setValue("event_id", $idevent);
                $baseController->entity->setValue("CLD_idTypeBooth", $idType);
                
                $idUSB = $baseController->usbsModel->insert_usbs();

                $USBFolder = $data . $idUSB;
                
                mkdir(G_PATH . "usbs/" . $USBFolder, 0777);
                mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdDownload", 0777);
                mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdDownload/myphotocode", 0777);
                mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload", 0777);
                mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload/Welcome", 0777);
                mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload/Welcome/Custom", 0777);
                mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload/Welcome/Random", 0777);
                mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload/Bye", 0777);
                mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload/Bye/Custom", 0777);
                mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload/Bye/Random", 0777);
                mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload/Frames", 0777);
                mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdEvents", 0777);
                mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdEvents/CustomShots", 0777);
                if ($ch == "A") {
                    mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdEvents/Wedding", 0777);
                    mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdEvents/Wedding/Header", 0777);
                }

                $archivo = G_PATH . "usbs/" . $USBFolder . "/PhotoIdDownload/myphotocode/myphotocode.dat";
                $fp = fopen($archivo, "w+");
                $string = $idevent;
                fputs($fp, $string);
                fclose($fp);

                if ($ch == "D") {
                    $baseController->entity->loadEntity('usbs');
                    $baseController->entity->setValue("rental_id", $owner);
                    $baseController->entity->setValue("creation_date", $data);
                    $baseController->entity->setValue("title", '-USB'.$data.'-');
                    $baseController->entity->setValue("boothtype_char", $ch);
                    $baseController->entity->setValue("event_id", $idevent);
                    $baseController->entity->setValue("CLD_idTypeBooth", $idType);

                    $idUSB = $baseController->usbsModel->insert_usbs();                    

                    //$idUSB = $CLD_CON->ExecuteInsert("INSERT INTO usbs (rental_id , creation_date , title , boothtype_char , event_id , CLD_idTypeBooth) VALUES ($owner , '$data' , '-USB$data-' , '$ch' , $idevent , $idType) ");
                    
                    $USBFolder = $data . $idUSB;
                    mkdir(G_PATH . "usbs/" . $USBFolder, 0777);
                    mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdDownload", 0777);
                    mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdDownload/myphotocode", 0777);
                    mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload", 0777);
                    mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload/Welcome", 0777);
                    mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload/Welcome/Custom", 0777);
                    mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload/Welcome/Random", 0777);
                    mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload/Bye", 0777);
                    mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload/Bye/Custom", 0777);
                    mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload/Bye/Random", 0777);
                    mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdUpload/Frames", 0777);
                    mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdEvents", 0777);
                    mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdEvents/CustomShots", 0777);
                    if ($ch == "A") {
                        mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdEvents/Wedding", 0777);
                        mkdir(G_PATH . "usbs/" . $USBFolder . "/PhotoIdEvents/Wedding/Header", 0777);
                    }

                    $archivo = G_PATH . "usbs/" . $USBFolder . "/PhotoIdDownload/myphotocode/myphotocode.dat";
                    $fp = fopen($archivo, "w+");
                    $string = $idevent;
                    fputs($fp, $string);
                    fclose($fp);
                }

                /*                 * ************************************************* FI CREEM EL USB ******************************************************************************************** */
            }
        }

    }
    echo "OK";
} else {
    echo "Has been an error creating event , please try again";
}
?>