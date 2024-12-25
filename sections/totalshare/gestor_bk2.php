<?php

require_once "../../common/global.php";
require_once '../../common/conexio.php';

require_once "telegram.php";
ob_start();
//error_reporting(E_ALL);
ini_set('display_errors',1);

$html = "<script src='functions.js'/>";


////////////////////////////////////////////////////////////////////////////////
//              GESTOR DEL LOG
////////////////////////////////////////////////////////////////////////////////

$logfile = G_PATH . "log/logGestor.dat";
    if (filesize($logfile) > 5000000) {
        $bakfile = G_PATH . "log/logGestor-".date('YmdHis')."-".rand(10,99).".bak";
        copy($logfile, $bakfile);
        unlink($logfile);
        utils::log(date('Y-m-d H:i:s '), "logGestor");
        utils::log("\n", "logGestor");
    }
    
////////////////////////////////////////////////////////////////////////////////



utils::log("\n", "logGestor");
utils::log("=========================================", "logGestor");
utils::log("Comença el cicle", "logGestor");


// Guardar temps actual a $now

$now = new DateTime();
$now = $now->format('Y-m-d H:i:s');
$s30mins = new DateTime();
    $s30mins->modify('-30 minutes');
$s7dies = new DateTime();
    $s7dies->modify('-7 days');
$s1dia = new DateTime();
    $s1dia->modify('-1 day');

// si ve de la web $_POST["data"] estará ple, i voldra dir que les variables estan buides i les hem de omplir amb el post.
// si ve desde una maquina, $_POST['data'] estará buit, y les variables estarán plenes per PBnew_Share.php, no les volem sobreescriure
    
if(isset($_POST["data"])){
    $json = json_decode($_POST["data"], TRUE);
    $codiFoto = $json[0];
    $metode = $json[1];
    $contacte = $json[2];
    $web = $json[3];
    $pref = $json[4];  
    
 // si hi ha $pref vol dir que es un telefon, l'ajuntem amb el contacte.   
    if ($pref) {
        $pref = ltrim($pref, "0");
        $contacte = $pref . $contacte;
    }
}
if ($metode == 1) {
//    $contacte = $pref . $contacte;
    $mini = substr($contacte,1);
    $contacte = "+". ltrim($mini,"0 ");
}

// si el gestor es necessita per escriure a la BBDD, ja sigui mitjançant POST o PBnew_Share.php s'hauran omplert les variables. 
// Fem un INSERT si hi han variables, sino, es una crida al gestor via Cron, i nomes volem revisar la BBDD, aixi que ens saltem el insert.   
if(isset($contacte)){
    $CLD_CON->Execute("INSERT INTO gestor(`code`, `method`, `contact`, `timestamp`, `state`, `versioPB`,`ipb`) VALUES ('$codiFoto', '$metode', '$contacte', '$now', 0, '$versioPB', '$idb')");    
}

//Actualitzar les entrades que ja tenen foto
$CLD_CON->Execute("UPDATE `gestor` JOIN `photos` ON `photos`.`code` = `gestor`.`code` AND `gestor`.`state` = '0' OR `gestor`.`state` = '1' OR `gestor`.`state` = '2' OR `gestor`.`state` = '3' SET `gestor`.`state` = '4'");
    
    
 
// Buscar entrades de les ultimes dues setmanes

    if ($CLD_CON->OpenRs("SELECT * FROM gestor WHERE `timestamp` > SUBTIME (NOW(), '14 0:00:00')")){

    }else{
        utils::log("No s'ha pogut extreure informació de la BBDD, abortant", "logGestor");
        exit;
    }
   
$entries = array();    

$llistaEmail = array();
$llistaSMS = array();
$llistaTelegram = array();
$llistaWhatsapp = array();

// inspeccionem cada entrada y les guardem a $entries
  
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

        
        $entry['entryTime'] = new DateTime($entry['timestamp']);
        $entry['lastTime'] = new DateTime($entry['last']);
        
        //array_push($entries, $entry);
        array_push($entries, $entry);
    }
                 
//construim la petició del missatge


    foreach ($entries as $entry){
        $entryID = $entry['id'];
        
        // amb 'state' comprobem en quin pas está la entrada i comparantho amb un dels timestamps 
        // sabem si ja ha passat suficient temps com per enviar un missatge
        // En el primer Switch triem la plantilla a utilitzar i el state en el que ha de acabar la entrada
        
        $entry['enviar']=false;
        
        switch ($entry['state']) {
            case '0': //(acaba de arribar)
                
                $code = $entry['code'];
                $entry['enviar']=true;
                $gestorID=null;
                $CLD_CON->OpenRs("SELECT `photos`.`id` AS `id`, `photos`.`event_id` AS `event`, `events`.`start_date` AS `date` FROM photos, events WHERE `photos`.`code` = '$code' AND `photos`.`event_id` = `events`.`id`");
                while ($CLD_CON->FetchArray()) {
                    $gestorID = $CLD_CON->GetArrayField("id");
                    $partdate = $CLD_CON->GetArrayField("date");
                    $partevent = $CLD_CON->GetArrayField("event");
                    
                }
               
                    if ($gestorID){
                    //code a la bbdd. Ara comprovem si realment la foto existeix
                    
                    //$filepath = $_SERVER['DOCUMENT_ROOT'];
                    $filepath = G_PATH;
                    $filepath .= "/events/".$partdate.$partevent."/".$entry['code'].".jpg";
                    
                    if (file_exists($filepath)){
                        $entry['plantilla'] = "enviarFoto.php";
                    }else{
                        $entry['plantilla'] = "firstMessage.php";
                    }
                    
                }else{
                    $entry['plantilla'] = "firstMessage.php";
                }
                break;
                
            case '1': //(no estaba abans, esperem 30 mins)  

                if ($entry['entryTime'] < $s30mins) {
                    $entry['enviar']=true;
                    $entry['plantilla'] = "secondMessage.php"; 
                }
                break;
                
            case '2': //(no estaba abans, esperem 7 dies)


                if ($entry['entryTime'] < $s7dies) {
                    $entry['enviar']=true;
                    $entry['plantilla']="lastMessage.php";
                }
                
                break;
            case '4': // (a enviar)
                $entry['enviar']=true;
                $entry['plantilla'] = "enviarFoto.php";
                break;
            case '5': //(ha fallat al enviarse, retry)
                
               // utils::log("$entryID --- Case 5: Ha fallat un envío, farem al menys un retry passades 24 hores", "logGestor");
                    if ($entry['last']!=null && $entry['lastTime'] < $s1dia && $entry['error']!="No s'ha pogut enviar, esperant retry"){
                        $entry['enviar']=true;
                        $entry['plantilla'] = "enviarFoto.php";
                    }
                break;
        }
        
        
        // ara triem el métode de enviament
        
        if ($entry['enviar']){
            switch ($entry['method']){
                case '0'://email
                    $entry['plantilla'] = "./templates/email/" . $entry['plantilla'];
                    array_push($llistaEmail, $entry);
                    break;
                case '1'://sms
                    $entry['plantilla'] = "./templates/sms/" . $entry['plantilla'];
                    array_push($llistaSMS, $entry);
                    break;
                case '2'://telegram
                    if($entry['contact']){
                        $entry['plantilla'] = "./templates/telegram/" . $entry['plantilla'];
                        array_push($llistaTelegram, $entry);
                    }
                case '3'://whatsapp
                    utils::log("$entryID --- Whatsapp", "logGestor");
                    if($entry['contact']){
                        $entry['plantilla'] = "./templates/whatsapp/" . $entry['plantilla'];
                        array_push($llistaWhatsapp, $entry);
                    }
                break;
            }
        }
        
    }


    
utils::log("= Comencem a enviar els Emails =", "logGestor");
require_once G_PATH . 'common/mailer/class.phpmailer.php';

	$from = "noreply@myphotocode.com";
	$from_str = "noreply";
	$host = "smtp.1and1.com";
        $username = "noreply@myphotocode.com";
	$password = "DC12345";

foreach ($llistaEmail as $entry){

    // to subject message
    include $entry['plantilla'];
    
    $id = $entry['id'];
    
    
    $mail = new PHPMailer;

    $mail->isSMTP();                                        // Set mailer to use SMTP
    $mail->Host = $host;                                    // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                                 // Enable SMTP authentication
    $mail->Username = $username;                            // SMTP username
    $mail->Password = $password;                            // SMTP password
    $mail->SMTPSecure = 'tls';                              // Enable encryption, 'ssl' also accepted

    $mail->From = $from;
    $mail->FromName = 'Digital Centre';
    //$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
    $mail->addAddress($to);                                 // Name is optional
    $mail->addReplyTo('main@digital-centre.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    $mail->WordWrap = 70;                                   // Set word wrap to 70 characters
    //$mail->addAttachment('/var/tmp/file.tar.gz');           // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');      // Optional name
    $mail->isHTML(true);                                    // Set email format to HTML

    $mail->Subject = $subject;
    $mail->Body    = $message;
    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    utils::log("--- $id --- ", "logGestor");
    
    if (!$mail->send()) {
        //guarda log negatiu
        utils::log("$id --- L'email no s'ha pogut enviar", "logGestor");
        utils::log("$id --- ERROR: $mail->ErrorInfo", "logGestor");
        if ($CLD_CON->Execute("UPDATE gestor SET `last`= '$now', `state`=$statefailure, `error`='$mail->ErrorInfo' WHERE `id`=$id")) {
            //guardar log del error
        } else {
            //guardar log negatiu
            utils::log("$id --- Ha fallat el UPDATE", "logGestor");
        }
    } else {
        //guarda éxit
        if ($CLD_CON->Execute("UPDATE gestor SET `last`= '$now', `state`=$statesuccess WHERE `id`=$id")) {
            //guardar log positiu
        } else {
            //guardar log negatiu
            utils::log("$id --- Ha fallat el UPDATE", "logGestor");
        }
    }

}

foreach ($llistaSMS as $entry) {

    include $entry['plantilla'];


    $id = $entry['id'];



    $curl = curl_init();

    if (!$curl) {
        die("Couldn't initialize a cURL handle");
    }

    // Set the file URL to fetch through cURL
    curl_setopt($curl, CURLOPT_URL, "https://api.twilio.com/2010-04-01/Accounts/ACa495bb879ddb69a2c3afbdd8eba6cfbf/Messages.json");

    $data = array(
        "To" => $entry['contact'],
        "From" => "MGa19ab83dcefdae8c6ed207158ab9c46e",
        "Body" => $message,
    );


    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_USERPWD, "ACa495bb879ddb69a2c3afbdd8eba6cfbf:05aad8b9b2c4aa71f2e7ccb2ba014527");


    $response = curl_exec($curl);


    if (!curl_errno($curl)) {
        //guarda exit
        if ($CLD_CON->Execute("UPDATE gestor SET `last`= '$now', `state`=$statesuccess WHERE `id`=$id")) {
            //guardar log positiu
        } else {
            //guardar log negatiu
            utils::log("$id --- Ha fallat el UPDATE", "logGestor");
        }
    } else {
        //guarda log negatiu
        $error .= '\n cURL error: ' . curl_error($curl);
        if ($CLD_CON->Execute("UPDATE gestor SET `last`= '$now', `state`=$statefailure, `error`=$error WHERE `id`=$id")) {
            //guardar log del error
        } else {
            //guardar log negatiu
            utils::log("$id --- Ha fallat el UPDATE", "logGestor");
        }
    }


    curl_close($curl);
}



foreach ($llistaTelegram as $entry){

    require $entry['plantilla'];
    
    $id = $entry['id'];
        
    
    $apiToken = "563225064:AAHiZ8ZJdoL144KMUkTQNEarKZa2pTdwMzg";

    $chatID = $entry["contact"];
    $message = "https://api.telegram.org/bot$apiToken/sendMessage?chat_id=$chatID&text=$message";
    $response = file_get_contents("$message");

    if($response){
         //guarda exit
        
        if($CLD_CON->Execute("UPDATE gestor SET `last`= '$now', `state`=$statesuccess WHERE `id`=$id")){
            //guardar log positiu
        }else{
            //guardar log negatiu
            utils::log("$id --- Ha fallat el UPDATE", "logGestor");
        }
    }else{
        //guarda log negatiu
        utils::log("$id --- No s'ha pogut enviar el Telegram", "logGestor");
        if($CLD_CON->Execute("UPDATE gestor SET `last`= $now, `state`=$statefailure, `error`=$error WHERE `id`=$id")){
            //guardar log del error
        }else{
            //guardar log negatiu
            utils::log("$id --- Ha fallat el UPDATE", "logGestor");
        }
        
    }
  
}


ob_get_contents();
ob_end_clean();