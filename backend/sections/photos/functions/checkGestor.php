<?php

require_once "../../../common/global.php";
require_once G_PATH . 'common/conexio.php';
require_once "../resources/lang.php";
require_once "../resources/countrycodes.php";

function obfuscate_email($email) {
    $em = explode("@", $email);
    $name = implode(array_slice($em, 0, count($em) - 1), '@');
    $len = floor(strlen($name) / 2);

    return substr($name, 0, $len) . str_repeat('*', $len) . "@" . end($em);
}

function obfuscate_phone($number) {
    $mask_number = str_repeat("*", strlen($number) - 4) . substr($number, -4);
    return $mask_number;
}

if (!isset($_POST['codi'])) {
    die("Error");
} else {
    $code = strtoupper($_POST['codi']);
}
utils::log("--- $code --- ", "logCheckGestor");
//$code = strtoupper("parogadnud");
$now = new DateTime();
$now = $now->format('Y-m-d H:i:s');
$entries = array();
$sql = "SELECT * FROM gestor WHERE `code`='$code' AND `state` BETWEEN 0 AND 3";
$CLD_CON->OpenRs($sql);

while ($CLD_CON->FetchArray()) {
    $entry = array();
    $entry['id'] = $CLD_CON->GetArrayField("id");
    $entry['code'] = $CLD_CON->GetArrayField("code");
    $entry['method'] = $CLD_CON->GetArrayField("method");
    $entry['contact'] = $CLD_CON->GetArrayField("contact");
    $entry['timestamp'] = $CLD_CON->GetArrayField("timestamp");
    $entry['state'] = $CLD_CON->GetArrayField("state");
    $entry['last'] = $CLD_CON->GetArrayField("last");
    $entry['error'] = $CLD_CON->GetArrayField("error");
    $entry['versioPB'] = $CLD_CON->GetArrayField("versioPB");

    array_push($entries, $entry);
}
//var_dump($entries);
$methods = array(0 => 'email', 1 => 'SMS', 2 => 'Telegram', 3 => 'WhatsApp', 4 => 'WeChat');

if ($entries) { //la foto esta demanada. Confirmar o modificar.
    $html .= "<center>$code</center><br/> The photo is not available yet, you have this notifications scheduled:<br/><br/>";
    $arrayIDs = array();
//    foreach ($entries as $entry) {
//        array_push($arrayIDs, $entry['id']);
//        $triat = $entry['method'];
//        $elegit = $methods[$triat];
//        $html .= "<div class='element' id='element{$entry['id']}'>"
//                . "<div class='method'>$elegit: </div>";
//        if ($elegit == "email") {
//            $contact = obfuscate_email($entry['contact']);
//            $html .= "<div class='contact'>$contact</div>";
//            $html .= "<div class='confirmcancel'><input type='hidden' value='confirm' name='data[{$entry['id']}][confirm]' id='confirm{$entry['id']}'/><img src='https://www.myphotocode.com/images/web/tick.png' width=40 onClick='eliminar({$entry['id']})' id='keep{$entry['id']}'><img src='https://www.myphotocode.com/images/web/close.png' width=50 onClick='guardar({$entry['id']})' style='display:none' id='delete{$entry['id']}'></div></div>";
//        }
//        if ($elegit == "SMS" || $elegit == "WhatsApp" || $elegit == "WeChat") {
//            $contact = obfuscate_phone($entry['contact']);
//            $html .= "<div class='contact'>$contact</div>";
//            $html .= "<div class='confirmcancel'><input type='hidden' value='confirm' name='data[{$entry['id']}][confirm]' id='confirm{$entry['id']}'/><img src='https://www.myphotocode.com/images/web/tick.png' width=40 onClick='eliminar({$entry['id']})' id='keep{$entry['id']}'><img src='https://www.myphotocode.com/images/web/close.png' width=50 onClick='guardar({$entry['id']})' style='display:none' id='delete{$entry['id']}'></div></div>";
//        }
//    }
    $html.="<table width='100%' border=0>";
    foreach ($entries as $entry) {
        array_push($arrayIDs, $entry['id']);
        $triat = $entry['method'];
        $elegit = $methods[$triat];
        $html .= "<tr id='element{$entry['id']}'>"
                . "<td class='method'>$elegit: </td>";
        if ($elegit == "email") {
            $contact = obfuscate_email($entry['contact']);
        }elseif($elegit == "SMS" || $elegit == "WhatsApp" || $elegit == "WeChat"){
            $contact = obfuscate_phone($entry['contact']);
        }
            $html .= "<td class='contact'>$contact</td>";
            $html .= "<td class='confirmcancel'><input type='hidden' value='confirm' name='data[{$entry['id']}][confirm]' id='confirm{$entry['id']}'/><img src='https://www.myphotocode.com/images/web/tick.png' width=40 onClick='eliminar({$entry['id']})' id='keep{$entry['id']}'><img src='https://www.myphotocode.com/images/web/close.png' width=50 onClick='guardar({$entry['id']})' style='display:none' id='delete{$entry['id']}'></td></tr>";
        }
    $html.="</table>";
    $arrayIDs = json_encode($arrayIDs);
    $html .= "<div class='botons aball'><input type='button' onclick='confirma({$arrayIDs});' id='confirma' value='Confirm'></div></div>";
    
    
} else {
    //la foto no esta demanada. Preguntar si vol avis
    //$countrycodes = json_encode($countrycodes);
    $html .= <<<HTML
            <center>$code</center><br/>
                    The photo is not available yet, would you like us to notify you as soon as it is ready?  <br><br>
                    <div>
        <input type='button' onclick="avisaSMS('$code');" id='si' value='Yes, send me a SMS'>
            <!--21-D-03-Total-Share-Whatsapp-->
            <input type='button' onclick="avisaWhastapp('$code');" id='si' value='Yes, send me a WhatsApp'>
        <input type='button' onclick="avisaMail('$code');" id='si' value='Yes, send me an email'>
        <input type='button' onclick="backToStart();" value='No, thanks'><br><br>
    </div>                    
HTML;
}
echo $html;


