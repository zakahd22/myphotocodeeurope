<?php 
require_once "../../common/global.php";
require_once '../../common/conexio.php';



include('offset.php');

$updateID = null;
$gestorID = null;
//$apiToken = "563225064:AAHiZ8ZJdoL144KMUkTQNEarKZa2pTdwMzg"; //ens han esborrat el booth DCphotobooth per inactivitat?
$apiToken = "2127043465:AAHqIGAwX4fMwnO5HwcE3T5ijBd-8a25ptg";
//$content = file_get_contents("https://api.telegram.org/bot$apiToken/getUpdates");
$content = file_get_contents("https://api.telegram.org/bot$apiToken/getUpdates?offset=$offset");

$json = str_replace('},]', "}]", $content);
$data = json_decode($json, true);

//$pos = sizeof($data["result"]);


//echo "<pre>";
//print_r($data);//["result"][$pos -1]["message"]
//echo "</pre>";

    $now = new DateTime();
    $now = $now->format('Y-m-d H:i:s');
    // echo "NOW = " . $now;

foreach ($data['result'] as $result) {


    $updateID = $result['update_id'];
    $chatID = $result['message']['from']['id'];
    $codi = strtoupper($result['message']['text']);
    $regex = "/[\s\S]{10}/";
    $rand_string = substr($codi, 1, 3); //codi maquina
    $string = null;
    $sql = "SELECT `id` FROM `booths` WHERE `rand_string` = '$rand_string'";
    $CLD_CON->OpenRs($sql);
    while ($CLD_CON->FetchArray()) {
        $string = $CLD_CON->GetArrayField("id");
    }

// if $codi te 10 caracters i els caracters 2-3-4 corresponen amb un rand_string estarem bastant segurs de que es un codi de foto real.
    if (preg_match($regex, $codi) && $string != null) {
        //te pinta de ser un codi correcte
        $texte = "Registered the petition for the picture: $codi";

        $sql = "SELECT * FROM `gestor` WHERE `code` = '$codi' AND `method`='2' AND `contact` IS NULL";
        $CLD_CON->OpenRs($sql);

        while ($CLD_CON->FetchArray()) {
            //echo "entra al while <br>";
            $gestorID = $CLD_CON->GetArrayField("id");
        }
        //echo "gestor
        //ID = $gestorID \n";
        if ($gestorID) {
            //echo "gestorID = $gestorID <br>";
            //s'ha demanat desde una maquina aixi que a la petició que ja existeix li afegim el chatID
            $sql = "UPDATE gestor SET `contact`='$chatID', `last`='$now' WHERE `id` = '$gestorID'";
            //echo "$sql <br>";
            if ($CLD_CON->OpenRs($sql)) {
                //log s'ha guardat be el chatID
                //$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?chat_id=$chatID&text=$texte");
            } else {
                //no s'ha guardat be el chatID
            }
        } else {
            // Sembla que es una petició nova així que fem una entrada nova
            $sql = "INSERT INTO `gestor`(`code`, `method`, `contact`, `timestamp`, `state`) VALUES ('$codi', '2', '$chatID', '$now', '0')";
            if ($CLD_CON->OpenRs($sql)) {
                // log s'ha insertat correctament
                //$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?chat_id=$chatID&text=$texte");
            } else {
                // log no s'ha insertat correctament
            }
        }
    } else {
        //es un codi incorrecte
        $texte = "The code $codi does not appear to be correct. Please check it up";

        $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?chat_id=$chatID&text=$texte");
    }
}
if ($updateID) {
    $offset = $updateID + 1;

    $lUid = fopen("offset.php", "w") or die("Unable to open file!");
    $txt = <<<HTML
<?php
        \$offset = $offset;
HTML;
//    echo $txt;
    fwrite($lUid, $txt);
    fclose($lUid);
}