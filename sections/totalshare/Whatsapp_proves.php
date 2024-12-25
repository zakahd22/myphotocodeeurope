<?php



/* 
 * Reproduïm el test inicial de whatsapp a Twilio
 * Ho fem amb curl igual que SMS
 * suposo que es va triar aquesta opció perque php no es compatible amb la versió PHP 5.6 . Almenys això diu la documentació.
 * En qualssevol cas aquest és l'exemple curl a reproduir amb curl de php
 * 
 * 
 * Whatsapp ************************************************************************************************
 * 
 * curl 'https://api.twilio.com/2010-04-01/Accounts/ACa495bb879ddb69a2c3afbdd8eba6cfbf/Messages.json' -X POST \
--data-urlencode 'To=whatsapp:+34652952049' \
--data-urlencode 'From=whatsapp:+14155238886' \
--data-urlencode 'Body=Your Yummy Cupcakes Company order of 1 dozen frosted cupcakes has shipped and should be delivered on July 10, 2019. Details: http://www.yummycupcakes.com/' \
-u ACa495bb879ddb69a2c3afbdd8eba6cfbf:[AuthToken]
*/  
//require __DIR__.'/vendor/autoload.php';
//print __DIR__.'/vendor/autoload.php';






$namePB = "DC";

$message="Check this out! I took this photo at a DC Photobooth.\n\nClick on the link to see your photo https://www.myphotocode.com/index.php?code=W7UXM5X6G3&method=sms&v=4\n\nCome visit our DC Photobooth.";
$contact="+34652952049";
$curl = curl_init();

if (!$curl) {
    die("Couldn't initialize a cURL handle");
}

// Set the file URL to fetch through cURL
curl_setopt($curl, CURLOPT_URL, "https://api.twilio.com/2010-04-01/Accounts/ACa495bb879ddb69a2c3afbdd8eba6cfbf/Messages.json");

$data = array(
    "To" => "whatsapp:".$contact,
    /*
       When sending a message with a messaging service, Twilio will immediately set the message’s status to accepted. 
     * Twilio will then determine the optimal From phone number from your service. 
     * Any delivery errors will be sent asynchronously to your StatusCallbackURL.
     *      */
    //"From" => "whatsapp:+14155238886", //sender sandbox
    //"From" => "whatsapp:+15866857271", //sender twilio Approved by WhatsApp
    "MessagingServiceSid" => "MGa19ab83dcefdae8c6ed207158ab9c46e",
    //Podem enviar les imatges així però no ho farem perque només està permés en cas que l'usuari respongui previament
    //TODO: provar si el que diu la documentacio de twilio es cert i no es pot realment ;)
//    "MediaUrl0" => "https://www.myphotocode.com/events/2021092340241/W7UXM5X6G3.jpg",
//    "MediaUrl1" => "https://www.myphotocode.com/events/2021092340241/W7UXM5X6G3.jpg",
    "Body" => $message,
);


curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_USERPWD, "ACa495bb879ddb69a2c3afbdd8eba6cfbf:05aad8b9b2c4aa71f2e7ccb2ba014527");


$response = curl_exec($curl);



