<?php
require_once "../../../common/global.php";
require_once '../../../common/conexio.php';

require_once "copiaHistoric.php";

//Interessats recull si el usuari realment esta interessat en rebre info sobre la imatge o no
$now = new DateTime();
$now = $now->format('Y-m-d H:i:s');

if (isset($_POST["interessats"])) {
    $json = json_decode($_POST["interessats"], TRUE);
    
    $interessats = array(array());
    
    $i=0;
    while($i<count($json)-1){
        $interessats[$i]['id']=$json[$i];
        $interessats[$i]['confirm']=$json[$i+1];
        $i=$i+2;
    }

    foreach ($interessats as $entry) {
        if ($entry['confirm'] == "confirm") {
            $rao = "->2, usuari ha mostrat interes i ha confirmat que vol rebre avis";
            $set = "`gestor`.`state`=2, `gestor`.`last`='$now'";
        }
        if ($entry['confirm'] == "delete") {
            $rao = "->-1, usuari ha cancelat avis";
            $set = "`gestor`.`state`='-1', `gestor`.`last`='$now'";
        }
        $sql = "SELECT * FROM `gestor` WHERE `id` = {$entry['id']}";
//      
        copiaHistoric($CLD_CON, $now,$rao,$sql,$set); 
    }
}
