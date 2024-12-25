<?php
require("common/APP_common_1.php");

if(!$APP_user) return;

//<name>".utf8_encode('NG – Zoo – AJ3')."</name>
//<name>".utf8_encode('NG – Zoo – X4E')."</name>



//$str = "NG – Zoo – AJ3";
//Proves d'accès de l'App INICI
echo "$APP_xml
<booth>
<id>23</id>
<name>NG – Zoo – AJ3</name>
<status>Error</status>
<location>
<latitude>1.23456</latitude>
<longitude>9.87654</longitude>
</location>
</booth>
<booth>
<id>24</id>
<name>NG – Zoo – X4E</name>
<status>OK</status>
<location>
<latitude>1.23456</latitude>
<longitude>9.87654</longitude>
</location>
</booth>
</return>
";
return;

//Proves d'accès de l'App FINAL

//Params
////$idBooth = "";
////if(isset($_REQUEST['id'])){$idBooth = $_REQUEST['id']; }

$APP_BdD2 = getNewBdD();
//SELECT code FROM `photos` WHERE `booth_id` = 66 ORDER BY id DESC LIMIT 1

$sql = "SELECT id,rand_string FROM booths WHERE rental_id='$APP_userId'; ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<status>Error - Database error code: 0002a</status></return>";
return;
}
$xml.= "<status>OK</status>";
while($APP_BdD->FetchRs()){
    $xml.= "<booth>";
    $idBooth =  $APP_BdD->GetField(1);
    $xml.= "<id>$idBooth</id>";
    $codi3cars =  $APP_BdD->GetField(2);
    $xml.= "<status>OK</status>";//de moment !!!!!!!!!!!!!!!
    //tipus de booth
        $tipusBooth = "";//màquina associada
        $sql2 = "SELECT code FROM `photos` WHERE `booth_id` = $idBooth ORDER BY id DESC LIMIT 1;"; //la darrera foto pujada !!!!
        $esOK = $APP_BdD2->OpenRs($sql);
        if(!$esOK){
        //caldria controlar l'error
        echo "$APP_xml<status>Error - Database error code: 0002b</status></return>";
        return;
        }
        if($APP_BdD->FetchRs()){

        }



    $APP_xml.= "</booth>";

    //SELECT * FROM `booths` LEFT JOIN 'photos' ON `booths`.id = photos. WHERE 1

//<booth>
//<id>23</id>
//<name>NG – Zoo – AJ3</name>
//<status>[OK, Error]</status>
//<location>
//<latitude>1.23456</latitude>
//<longitude>9.87654</ longitude >
//</location>
//</booth>

}
$APP_BdD->CloseRs();



echo "$APP_xml</return>"; // no cal res més

?>
