<?php
require_once "../../../common/global.php";
require_once G_PATH . "common/conexio.php";
require_once G_PATH . "common/Classes/baseController.php";
 
$baseController = new baseController();
$baseController->createModel('rentals');
$baseController->createModel('photos');
$baseController->createModel('registre_emails');

$USERTYPE = $_SESSION['USERTYPE'];
//$USERID = $_SESSION['USERID'];
//$USERTYPE = 4;
$USERID = $_POST['id'];
if (!file_exists("../../../temp/emails/$USERTYPE" . "$USERID")){
    mkdir("../../../temp/emails/$USERTYPE" . "$USERID", 0777, true);
}
$f = "../../../temp/emails/$USERTYPE" . "$USERID/emails.xls";
$link = G_PAGE."temp/emails/$USERTYPE" . "$USERID/emails.xls";

$x=0; 

switch ($USERTYPE) {
    case 1:
//        $leftJoin='';
//        $where='';
        $rentals = $baseController->rentalsModel->getAllRentals();
        $in = array();
        foreach ($rentals as $rental){
            array_push($in, $rental['id']);
        }
        $photos = $baseController->photosModel->getCodePhotosOwnerIn($in);
        $in = array();
        foreach ($photos as $photo){
            array_push($in, $photo['event_id']);
        }
        break;
    case 3:
        $rentals = $baseController->rentalsModel->getRentals($USERID);
        $in = array();
        foreach ($rentals as $rental){
            array_push($in, $rental['id']);
        }
        
//        $CLD_CON->OpenRs("SELECT id FROM rentals WHERE CLD_DistributorId=$USERID");
//        $in = " ";
//        while($CLD_CON->FetchArray()){
//            $in .= "".$CLD_CON->GetArrayField("id") . " ,";
//        }
//        $in = substr($in , 0, -1);
        
        $photos = $baseController->photosModel->getCodePhotosOwnerIn($in);
        $in = array();
        foreach ($photos as $photo){
            array_push($in, $photo['event_id']);
        }
//        $CLD_CON->OpenRs("SELECT p.code FROM photos p LEFT JOIN events e  ON  e.id = p.event_id WHERE e.rental_id in($in)");
//        $in = " ";
//        while($CLD_CON->FetchArray()){
//            $in .= "'".$CLD_CON->GetArrayField("code") . "' ,";
//        }
//        $in = substr($in , 0, -1);

//        $leftJoin = "";
//        $filter[$x] = "e.code in($in)";
        $x++;
    break;
    case 4:
//        $codePhotos = $baseController->photosModel->getCodePhotosOwner($USERID);
//        $i = 0;
//        foreach ($codePhotos as $codePhoto){
//            $codePhotos[$i] = $codePhoto["code"];
//            $i++;
//        }

        $photos = $baseController->photosModel->getCodePhotosOwner($USERID);
        $in = array();
        foreach ($photos as $photo){
            array_push($in, $photo['event_id']);
        }
//        $CLD_CON->OpenRs("SELECT p.code FROM photos p LEFT JOIN events e  ON  e.id = p.event_id WHERE e.rental_id=$USERID");
//        $in = " ";
//        while($CLD_CON->FetchArray()){
//            $in .= "'".$CLD_CON->GetArrayField("code") . "' ,";
//        }
//        $in = substr($in , 0, -1);

//        $leftJoin = "";
//        $filter[$x] = "e.code in($in)";s
//        $filter[$x] = $codePhotos;
        $x++;
    break;    
}


if (isset($filter)) {
    $i = 0;
    $where = "WHERE ";
    while ($i < sizeof($filter)) {
        $where .= $filter[$i] . " ";
        $i++;
        if ($i < sizeof($filter)) {
            $where .= "AND ";
        }
    }
}


$emails = $baseController->registre_emailsModel->getRegistreEmailsOwner($in);
//$query= "SELECT e.email FROM registre_emails e $leftJoin $where GROUP BY e.email";
//$CLD_CON->OpenRs($query);

$fp = fopen($f, "w");
$text="";

foreach($emails as $email1){
    $email = $email1['email'];
    $text .= $email . "\n";
    
}
//while($CLD_CON->FetchArray()){
//    $email = $CLD_CON->GetArrayField('email');
//    $text .= $email . "\n";
//    
//}
 fwrite($fp, $text . PHP_EOL);
 fclose($fp);

echo $link;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>