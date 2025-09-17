<?php
chdir( __DIR__ );//20250111cron
require_once "../../common/global.php";
//error_log( "TO_DELETE gestor 01" );
require_once '../../common/conexio.php';
//error_log( "TO_DELETE gestor 02" );
require_once "telegram.php";

$twilioSid   = $_ENV['TWILIO_ACCOUNT_SID'] ?? getenv('TWILIO_ACCOUNT_SID');
$twilioToken = $_ENV['TWILIO_AUTH_TOKEN'] ?? getenv('TWILIO_AUTH_TOKEN');

$authString = $twilioSid . ':' . $twilioToken;
$urlTwilio = "https://api.twilio.com/2010-04-01/Accounts/{$twilioSid}/Messages.json";

ob_start();
//error_reporting(E_ALL);
//ini_set('display_errors',1);

$html = "<script src='functions.js'/>"; //No s'utilitza enlloc !?


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
// si ve desde una maquina, $_POST['data estará buit, y les variables estarán plenes per PBnew_Share.php, no les volem sobreescriure
    
if(isset($_POST["data"])){
    $json = json_decode($_POST["data"], TRUE);
    
    $codiFoto = $json[0];
    $metode = $json[1];
    $contacte = $json[2];
    $versioPB = $json[3];
    $pref = $json[4];  
    //Inicialitzem variables de costos SMS o whatsapp
    utils::log("$codiFoto --- Inicialitzem vars... Calcular el cost", "logGestor");
    $owner = 0;
    $idBooth = 0;    
    $cost = 0;
    $price_unit = 'USD';
    $CLD_CONowner = getNewBdD();
    $sqlOwner = "SELECT ab.owner, p.pbs_id FROM `photos` p 
 LEFT JOIN App_booths ab ON ab.idBooth=p.pbs_id  
  WHERE p.code = '$codiFoto'";
    $CLD_CONowner->OpenRs($sqlOwner);
    while ($CLD_CONowner->FetchArray()) {
        $owner = $CLD_CONowner->GetArrayField("owner");
        $idBooth = $CLD_CONowner->GetArrayField("pbs_id");
    }  
    utils::log("$idBooth --- $owner --- Inicialitzem vars... Calcular el cost", "logGestor");
 // si hi ha $pref vol dir que es un telefon, l'ajuntem amb el contacte.   
    if ($pref) {
         
        $contacte = preg_replace("/[^0-9]/", "", $contacte );     //li treiem signes que no siguin numeros wtf
   
        $contacte = $pref .' '. $contacte;
        //$contacte = $pref . $contacte;
    }
    
    
}

$token = "";
if($metode == 1){
    $rand = rand(1000, 9999);
    $c = substr($codiFoto, -3);
    $token = 'SMS'.$c.$rand;
 } elseif ($metode == 3){
    $rand = rand(1000, 9999);
    $c = substr($codiFoto, -3);
    $token = 'WAPP'.$c.$rand;
 }
if ($metode == 1 || $metode == 3) {

    $mini = substr($contacte,1);
    $contacte = "+". ltrim($mini,"0 ");    
    
    
    
    //21-D-07 Consum SMS i WhatsApp
    //RECOPILEM INFO COSTOS SMS o Whatsapp
     utils::log("$codiFoto --- Iniciem calculs... Calcular el cost", "logGestor");
//20260124split    $prefix = split(' ',$mini);    
    $prefix = explode(" ",$mini);//20260124split
    
    
    /* Whatsapp de moment no té  API per saber el cost en temps real. 
    * Hem fet aquest array amb preus a dia  9-12-2021
    * Si més endavant cal es pot mirar si ja tenen API, que sembla que no faràn perque depenen de Whatsapp.
    * Si no caldrà actualitzar manualment el cost de whatsapp periodicament... ;) 
    * https://www.twilio.com/whatsapp/pricing/zw canviant zw pel ISO country que toqui (ES, US, IT...) es pot mirar al web de twilio
     * 
     */
    $arrayCodesISO = array(
    '376'=>array('name'=>'ANDORRA','ISO'=>'AD', 'costWhatsapp'=>'0.0135'),'971'=>array('name'=>'UNITED ARAB EMIRATES','ISO'=>'AE', 'costWhatsapp'=>'0.0285'),'93'=>array('name'=>'AFGHANISTAN','ISO'=>'AF', 'costWhatsapp'=>'0.0555'),'1268'=>array('name'=>'ANTIGUA AND BARBUDA','ISO'=>'AG', 'costWhatsapp'=>'0.0135'),'1264'=>array('name'=>'ANGUILLA','ISO'=>'AI', 'costWhatsapp'=>'0.0135'),'355'=>array('name'=>'ALBANIA','ISO'=>'AL', 'costWhatsapp'=>'0.0669'),'374'=>array('name'=>'ARMENIA','ISO'=>'AM', 'costWhatsapp'=>'0.0669'),'599'=>array('name'=>'NETHERLANDS ANTILLES','ISO'=>'AN', 'costWhatsapp'=>'0.0135'),'244'=>array('name'=>'ANGOLA','ISO'=>'AO', 'costWhatsapp'=>'0.0626'),'672'=>array('name'=>'ANTARCTICA','ISO'=>'AQ', 'costWhatsapp'=>'0.0135'),'54'=>array('name'=>'ARGENTINA','ISO'=>'AR', 'costWhatsapp'=>'0.0465'),'1684'=>array('name'=>'AMERICAN SAMOA','ISO'=>'AS', 'costWhatsapp'=>'0.0135'),'43'=>array('name'=>'AUSTRIA','ISO'=>'AT', 'costWhatsapp'=>'0.0800'),'61'=>array('name'=>'AUSTRALIA','ISO'=>'AU', 'costWhatsapp'=>'0.0555'),'297'=>array('name'=>'ARUBA','ISO'=>'AW', 'costWhatsapp'=>'0.0135'),'358'=>array('name'=>'ÅLAND ISLANDS','ISO'=>'AX', 'costWhatsapp'=>'0.0135'),'994'=>array('name'=>'AZERBAIJAN','ISO'=>'AZ', 'costWhatsapp'=>'0.0669'),
    '387'=>array('name'=>'BOSNIA AND HERZEGOVINA','ISO'=>'BA', 'costWhatsapp'=>'0.0135'),'1246'=>array('name'=>'BARBADOS','ISO'=>'BB', 'costWhatsapp'=>'0.0135'),'880'=>array('name'=>'BANGLADESH','ISO'=>'BD', 'costWhatsapp'=>'0.0555'),'32'=>array('name'=>'BELGIUM','ISO'=>'BE', 'costWhatsapp'=>'0.0800'),'226'=>array('name'=>'BURKINA FASO','ISO'=>'BF', 'costWhatsapp'=>'0.0626'),'359'=>array('name'=>'BULGARIA','ISO'=>'BG', 'costWhatsapp'=>'0.0669'),'973'=>array('name'=>'BAHRAIN','ISO'=>'BH', 'costWhatsapp'=>'0.0564'),'257'=>array('name'=>'BURUNDI','ISO'=>'BI', 'costWhatsapp'=>'0.0626'),'229'=>array('name'=>'BENIN','ISO'=>'BJ', 'costWhatsapp'=>'0.0626'),'590'=>array('name'=>'SAINT BARTHELEMY','ISO'=>'BL', 'costWhatsapp'=>'0.0135'),'1441'=>array('name'=>'BERMUDA','ISO'=>'BM', 'costWhatsapp'=>'0.0135'),'673'=>array('name'=>'BRUNEI DARUSSALAM','ISO'=>'BN', 'costWhatsapp'=>'0.0135'),'591'=>array('name'=>'BOLIVIA','ISO'=>'BO', 'costWhatsapp'=>'0.0565'),'599'=>array('name'=>'CARIBEAN NETHERLANDS','ISO'=>'BQ', 'costWhatsapp'=>'0.0135'),'55'=>array('name'=>'BRAZIL','ISO'=>'BR', 'costWhatsapp'=>'0.0523'),'1242'=>array('name'=>'BAHAMAS','ISO'=>'BS', 'costWhatsapp'=>'0.0135'),'975'=>array('name'=>'BHUTAN','ISO'=>'BT', 'costWhatsapp'=>'0.0135'),'55'=>array('name'=>'BOUVET ISLAND','ISO'=>'BV', 'costWhatsapp'=>'0.0135'),'267'=>array('name'=>'BOTSWANA','ISO'=>'BW', 'costWhatsapp'=>'0.0626'),'375'=>array('name'=>'BELARUS','ISO'=>'BY', 'costWhatsapp'=>'0.0669'),'501'=>array('name'=>'BELIZE','ISO'=>'BZ', 'costWhatsapp'=>'0.0135'),
    '1'=>array('name'=>'CANADA','ISO'=>'CA', 'costWhatsapp'=>'0.0135'),'61'=>array('name'=>'COCOS (KEELING) ISLANDS','ISO'=>'CC', 'costWhatsapp'=>'0.0135'),'243'=>array('name'=>'CONGO, THE DEMOCRATIC REPUBLIC OF THE','ISO'=>'CD', 'costWhatsapp'=>'0.0135'),'236'=>array('name'=>'CENTRAL AFRICAN REPUBLIC','ISO'=>'CF', 'costWhatsapp'=>'0.0135'),'242'=>array('name'=>'CONGO','ISO'=>'CG', 'costWhatsapp'=>'0.0626'),'41'=>array('name'=>'SWITZERLAND','ISO'=>'CH', 'costWhatsapp'=>'0.0800'),'225'=>array('name'=>'COTE D IVOIRE','ISO'=>'CI', 'costWhatsapp'=>'0.0626'),'682'=>array('name'=>'COOK ISLANDS','ISO'=>'CK', 'costWhatsapp'=>'0.0135'),'56'=>array('name'=>'CHILE','ISO'=>'CL', 'costWhatsapp'=>'0.0636'),'237'=>array('name'=>'CAMEROON','ISO'=>'CM', 'costWhatsapp'=>'0.0626'),'86'=>array('name'=>'CHINA','ISO'=>'CN', 'costWhatsapp'=>'0.0555'),'57'=>array('name'=>'COLOMBIA','ISO'=>'CO', 'costWhatsapp'=>'0.0150'),'506'=>array('name'=>'COSTA RICA','ISO'=>'CR', 'costWhatsapp'=>'0.0565'),'53'=>array('name'=>'CUBA','ISO'=>'CU', 'costWhatsapp'=>'0.0135'),'238'=>array('name'=>'CAPE VERDE','ISO'=>'CV', 'costWhatsapp'=>'0.0135'),'599'=>array('name'=>'CURAÇAO','ISO'=>'CW', 'costWhatsapp'=>'0.0135'),'61'=>array('name'=>'CHRISTMAS ISLAND','ISO'=>'CX', 'costWhatsapp'=>'0.0135'),'357'=>array('name'=>'CYPRUS','ISO'=>'CY', 'costWhatsapp'=>'0.0135'),'420'=>array('name'=>'CZECH REPUBLIC','ISO'=>'CZ', 'costWhatsapp'=>'0.0669'),
    '49'=>array('name'=>'GERMANY','ISO'=>'DE', 'costWhatsapp'=>'0.0908'),'253'=>array('name'=>'DJIBOUTI','ISO'=>'DJ', 'costWhatsapp'=>'0.0135'),'45'=>array('name'=>'DENMARK','ISO'=>'DK', 'costWhatsapp'=>'0.0800'),'1767'=>array('name'=>'DOMINICA','ISO'=>'DM', 'costWhatsapp'=>'0.0135'),'1809'=>array('name'=>'DOMINICAN REPUBLIC','ISO'=>'DO', 'costWhatsapp'=>'0.0135'),'213'=>array('name'=>'ALGERIA','ISO'=>'DZ', 'costWhatsapp'=>'0.0800'),'593'=>array('name'=>'ECUADOR','ISO'=>'EC', 'costWhatsapp'=>'0.0565'),'372'=>array('name'=>'ESTONIA','ISO'=>'EE', 'costWhatsapp'=>'0.0135'),'20'=>array('name'=>'EGYPT','ISO'=>'EG', 'costWhatsapp'=>'0.0737'),'212'=>array('name'=>'WESTERN SAHARA','ISO'=>'EH', 'costWhatsapp'=>'0.0135'),'291'=>array('name'=>'ERITREA','ISO'=>'ER', 'costWhatsapp'=>'0.0626'),'34'=>array('name'=>'SPAIN','ISO'=>'ES', 'costWhatsapp'=>'0.0430'),'251'=>array('name'=>'ETHIOPIA','ISO'=>'ET', 'costWhatsapp'=>'0.0626'),'358'=>array('name'=>'FINLAND','ISO'=>'FI', 'costWhatsapp'=>'0.0800'),'679'=>array('name'=>'FIJI','ISO'=>'FJ', 'costWhatsapp'=>'0.0135'),'500'=>array('name'=>'FALKLAND ISLANDS (MALVINAS)','ISO'=>'FK', 'costWhatsapp'=>'0.0135'),'691'=>array('name'=>'MICRONESIA, FEDERATED STATES OF','ISO'=>'FM', 'costWhatsapp'=>'0.0135'),'298'=>array('name'=>'FAROE ISLANDS','ISO'=>'FO', 'costWhatsapp'=>'0.0135'),'33'=>array('name'=>'FRANCE','ISO'=>'FR', 'costWhatsapp'=>'0.0820'),
    '241'=>array('name'=>'GABON','ISO'=>'GA', 'costWhatsapp'=>'0.0626'),'44'=>array('name'=>'UNITED KINGDOM','ISO'=>'GB', 'costWhatsapp'=>'0.0450'),'1473'=>array('name'=>'GRENADA','ISO'=>'GD', 'costWhatsapp'=>'0.0135'),'995'=>array('name'=>'GEORGIA','ISO'=>'GE', 'costWhatsapp'=>'0.0669'),'594'=>array('name'=>'FRENCH GUIANA','ISO'=>'GF', 'costWhatsapp'=>'0.0135'),'44'=>array('name'=>'GUERNSEY','ISO'=>'GG', 'costWhatsapp'=>'0.0135'),'233'=>array('name'=>'GHANA','ISO'=>'GH', 'costWhatsapp'=>'0.0626'),'350'=>array('name'=>'GIBRALTAR','ISO'=>'GI', 'costWhatsapp'=>'0.0135'),'299'=>array('name'=>'GREENLAND','ISO'=>'GL', 'costWhatsapp'=>'0.0135'),'220'=>array('name'=>'GAMBIA','ISO'=>'GM', 'costWhatsapp'=>'0.0626'),'224'=>array('name'=>'GUINEA','ISO'=>'GN', 'costWhatsapp'=>'0.0135'),'590'=>array('name'=>'GUADELOUPE','ISO'=>'GP', 'costWhatsapp'=>'0.0135'),'240'=>array('name'=>'EQUATORIAL GUINEA','ISO'=>'GQ', 'costWhatsapp'=>'0.0135'),'30'=>array('name'=>'GREECE','ISO'=>'GR', 'costWhatsapp'=>'0.0669'),'500'=>array('name'=>'SOUTH GEORGIA & SOUTH SANDWICH ISLANDS','ISO'=>'GS', 'costWhatsapp'=>'0.0135'),'502'=>array('name'=>'GUATEMALA','ISO'=>'GT', 'costWhatsapp'=>'0.0565'),'1671'=>array('name'=>'GUAM','ISO'=>'GU', 'costWhatsapp'=>'0.0135'),'245'=>array('name'=>'GUINEA-BISSAU','ISO'=>'GW', 'costWhatsapp'=>'0.0626'),'592'=>array('name'=>'GUYANA','ISO'=>'GY', 'costWhatsapp'=>'0.0135'),'852'=>array('name'=>'HONG KONG','ISO'=>'HK', 'costWhatsapp'=>'0.0555'),'672'=>array('name'=>'HEARD & MCDONALD ISLANDS','ISO'=>'HM', 'costWhatsapp'=>'0.0135'),
    '504'=>array('name'=>'HONDURAS','ISO'=>'HN', 'costWhatsapp'=>'0.0565'),'385'=>array('name'=>'CROATIA','ISO'=>'HR', 'costWhatsapp'=>'0.0669'),'509'=>array('name'=>'HAITI','ISO'=>'HT', 'costWhatsapp'=>'0.0565'),'36'=>array('name'=>'HUNGARY','ISO'=>'HU', 'costWhatsapp'=>'0.0669'),'62'=>array('name'=>'INDONESIA','ISO'=>'ID', 'costWhatsapp'=>'0.0290'),'353'=>array('name'=>'IRELAND','ISO'=>'IE', 'costWhatsapp'=>'0.0800'),'972'=>array('name'=>'ISRAEL','ISO'=>'IL', 'costWhatsapp'=>'0.0282'),'44'=>array('name'=>'ISLE OF MAN','ISO'=>'IM', 'costWhatsapp'=>'0.0135'),'91'=>array('name'=>'INDIA','ISO'=>'IN', 'costWhatsapp'=>'0.0092'),'246'=>array('name'=>'BRITISH INDIAN OCEAN TERRITORY','ISO'=>'IO', 'costWhatsapp'=>'0.0135'),'964'=>array('name'=>'IRAQ','ISO'=>'IQ', 'costWhatsapp'=>'0.0564'),'98'=>array('name'=>'IRAN, ISLAMIC REPUBLIC OF','ISO'=>'IR', 'costWhatsapp'=>'0.0135'),'354'=>array('name'=>'ICELAND','ISO'=>'IS', 'costWhatsapp'=>'0.0135'),'39'=>array('name'=>'ITALY','ISO'=>'IT', 'costWhatsapp'=>'0.0470'),'44'=>array('name'=>'JERSEY','ISO'=>'JE', 'costWhatsapp'=>'0.0135'),'1876'=>array('name'=>'JAMAICA','ISO'=>'JM', 'costWhatsapp'=>'0.0565'),'962'=>array('name'=>'JORDAN','ISO'=>'JO', 'costWhatsapp'=>'0.0564'),'81'=>array('name'=>'JAPAN','ISO'=>'JP', 'costWhatsapp'=>'0.0555'),'254'=>array('name'=>'KENYA','ISO'=>'KE', 'costWhatsapp'=>'0.0626'),'996'=>array('name'=>'KYRGYZSTAN','ISO'=>'KG', 'costWhatsapp'=>'0.0135'),'855'=>array('name'=>'CAMBODIA','ISO'=>'KH', 'costWhatsapp'=>'0.0555'),
    '686'=>array('name'=>'KIRIBATI','ISO'=>'KI', 'costWhatsapp'=>'0.0135'),'269'=>array('name'=>'COMOROS','ISO'=>'KM', 'costWhatsapp'=>'0.0135'),'1869'=>array('name'=>'SAINT KITTS AND NEVIS','ISO'=>'KN', 'costWhatsapp'=>'0.0135'),'850'=>array('name'=>'KOREA DEMOCRATIC PEOPLES REPUBLIC OF','ISO'=>'KP', 'costWhatsapp'=>'0.0135'),'82'=>array('name'=>'KOREA REPUBLIC OF','ISO'=>'KR', 'costWhatsapp'=>'0.0430'),'965'=>array('name'=>'KUWAIT','ISO'=>'KW', 'costWhatsapp'=>'0.0564'),'1345'=>array('name'=>'CAYMAN ISLANDS','ISO'=>'KY', 'costWhatsapp'=>'0.0135'),'7'=>array('name'=>'KAZAKSTAN','ISO'=>'KZ', 'costWhatsapp'=>'0.0135'),'856'=>array('name'=>'LAO PEOPLES DEMOCRATIC REPUBLIC','ISO'=>'LA', 'costWhatsapp'=>'0.0555'),'961'=>array('name'=>'LEBANON','ISO'=>'LB', 'costWhatsapp'=>'0.0564'),'1758'=>array('name'=>'SAINT LUCIA','ISO'=>'LC', 'costWhatsapp'=>'0.0135'),'423'=>array('name'=>'LIECHTENSTEIN','ISO'=>'LI', 'costWhatsapp'=>'0.0135'),'94'=>array('name'=>'SRI LANKA','ISO'=>'LK', 'costWhatsapp'=>'0.0555'),'231'=>array('name'=>'LIBERIA','ISO'=>'LR', 'costWhatsapp'=>'0.0626'),'266'=>array('name'=>'LESOTHO','ISO'=>'LS', 'costWhatsapp'=>'0.0626'),'370'=>array('name'=>'LITHUANIA','ISO'=>'LT', 'costWhatsapp'=>'0.0669'),'352'=>array('name'=>'LUXEMBOURG','ISO'=>'LU', 'costWhatsapp'=>'0.0135'),'371'=>array('name'=>'LATVIA','ISO'=>'LV', 'costWhatsapp'=>'0.0669'),'218'=>array('name'=>'LIBYAN ARAB JAMAHIRIYA','ISO'=>'LY', 'costWhatsapp'=>'0.0626'),'212'=>array('name'=>'MOROCCO','ISO'=>'MA', 'costWhatsapp'=>'0.0626'),
    '377'=>array('name'=>'MONACO','ISO'=>'MC', 'costWhatsapp'=>'0.0135'),'373'=>array('name'=>'MOLDOVA, REPUBLIC OF','ISO'=>'MD', 'costWhatsapp'=>'0.0669'),'382'=>array('name'=>'MONTENEGRO','ISO'=>'ME', 'costWhatsapp'=>'0.0135'),'1599'=>array('name'=>'SAINT MARTIN','ISO'=>'MF', 'costWhatsapp'=>'0.0135'),'261'=>array('name'=>'MADAGASCAR','ISO'=>'MG', 'costWhatsapp'=>'0.0626'),'692'=>array('name'=>'MARSHALL ISLANDS','ISO'=>'MH', 'costWhatsapp'=>'0.0135'),'389'=>array('name'=>'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','ISO'=>'MK', 'costWhatsapp'=>'0.0669'),'223'=>array('name'=>'MALI','ISO'=>'ML', 'costWhatsapp'=>'0.0626'),'95'=>array('name'=>'MYANMAR','ISO'=>'MM', 'costWhatsapp'=>'0.0135'),'976'=>array('name'=>'MONGOLIA','ISO'=>'MN', 'costWhatsapp'=>'0.0555'),'853'=>array('name'=>'MACAU','ISO'=>'MO', 'costWhatsapp'=>'0.0135'),'1670'=>array('name'=>'NORTHERN MARIANA ISLANDS','ISO'=>'MP', 'costWhatsapp'=>'0.0135'),'596'=>array('name'=>'MARTINIQUE','ISO'=>'MQ', 'costWhatsapp'=>'0.0135'),'222'=>array('name'=>'MAURITANIA','ISO'=>'MR', 'costWhatsapp'=>'0.0626'),'1664'=>array('name'=>'MONTSERRAT','ISO'=>'MS', 'costWhatsapp'=>'0.0135'),'356'=>array('name'=>'MALTA','ISO'=>'MT', 'costWhatsapp'=>'0.0135'),'230'=>array('name'=>'MAURITIUS','ISO'=>'MU', 'costWhatsapp'=>'0.0135'),'960'=>array('name'=>'MALDIVES','ISO'=>'MV', 'costWhatsapp'=>'0.0135'),'265'=>array('name'=>'MALAWI','ISO'=>'MW', 'costWhatsapp'=>'0.0626'),'52'=>array('name'=>'MEXICO','ISO'=>'MX', 'costWhatsapp'=>'0.0190'),'60'=>array('name'=>'MALAYSIA','ISO'=>'MY', 'costWhatsapp'=>'0.0457'),
    '258'=>array('name'=>'MOZAMBIQUE','ISO'=>'MZ', 'costWhatsapp'=>'0.0626'),'264'=>array('name'=>'NAMIBIA','ISO'=>'NA', 'costWhatsapp'=>'0.0135'),'687'=>array('name'=>'NEW CALEDONIA','ISO'=>'NC', 'costWhatsapp'=>'0.0135'),'227'=>array('name'=>'NIGER','ISO'=>'NE', 'costWhatsapp'=>'0.0626'),'672'=>array('name'=>'NORFOLK ISLAND','ISO'=>'NF', 'costWhatsapp'=>'0.0135'),'234'=>array('name'=>'NIGERIA','ISO'=>'NG', 'costWhatsapp'=>'0.0369'),'505'=>array('name'=>'NICARAGUA','ISO'=>'NI', 'costWhatsapp'=>'0.0565'),'31'=>array('name'=>'NETHERLANDS','ISO'=>'NL', 'costWhatsapp'=>'0.0850'),'47'=>array('name'=>'NORWAY','ISO'=>'NO', 'costWhatsapp'=>'0.0800'),'977'=>array('name'=>'NEPAL','ISO'=>'NP', 'costWhatsapp'=>'0.0555'),'674'=>array('name'=>'NAURU','ISO'=>'NR', 'costWhatsapp'=>'0.0135'),'683'=>array('name'=>'NIUE','ISO'=>'NU', 'costWhatsapp'=>'0.0135'),'64'=>array('name'=>'NEW ZEALAND','ISO'=>'NZ', 'costWhatsapp'=>'0.0555'),'968'=>array('name'=>'OMAN','ISO'=>'OM', 'costWhatsapp'=>'0.0564'),'507'=>array('name'=>'PANAMA','ISO'=>'PA', 'costWhatsapp'=>'0.0565'),'51'=>array('name'=>'PERU','ISO'=>'PE', 'costWhatsapp'=>'0.0494'),'689'=>array('name'=>'FRENCH POLYNESIA','ISO'=>'PF', 'costWhatsapp'=>'0.0135'),'675'=>array('name'=>'PAPUA NEW GUINEA','ISO'=>'PG', 'costWhatsapp'=>'0.0555'),'63'=>array('name'=>'PHILIPPINES','ISO'=>'PH', 'costWhatsapp'=>'0.0555'),'92'=>array('name'=>'PAKISTAN','ISO'=>'PK', 'costWhatsapp'=>'0.0303'),'48'=>array('name'=>'POLAND','ISO'=>'PL', 'costWhatsapp'=>'0.0669'),'508'=>array('name'=>'SAINT PIERRE AND MIQUELON','ISO'=>'PM', 'costWhatsapp'=>'0.0135'),
    '870'=>array('name'=>'PITCAIRN','ISO'=>'PN', 'costWhatsapp'=>'0.0135'),'1'=>array('name'=>'PUERTO RICO','ISO'=>'PR', 'costWhatsapp'=>'0.0565'),'970'=>array('name'=>'PALESTINE','ISO'=>'PS', 'costWhatsapp'=>'0.0135'),'351'=>array('name'=>'PORTUGAL','ISO'=>'PT', 'costWhatsapp'=>'0.0800'),'680'=>array('name'=>'PALAU','ISO'=>'PW', 'costWhatsapp'=>'0.0135'),'595'=>array('name'=>'PARAGUAY','ISO'=>'PY', 'costWhatsapp'=>'0.0565'),'974'=>array('name'=>'QATAR','ISO'=>'QA', 'costWhatsapp'=>'0.0564'),'262'=>array('name'=>'RÉUNION','ISO'=>'RE', 'costWhatsapp'=>'0.0135'),'40'=>array('name'=>'ROMANIA','ISO'=>'RO', 'costWhatsapp'=>'0.0680'),'381'=>array('name'=>'SERBIA','ISO'=>'RS', 'costWhatsapp'=>'0.0135'),'7'=>array('name'=>'RUSSIAN FEDERATION','ISO'=>'RU', 'costWhatsapp'=>'0.0527'),'250'=>array('name'=>'RWANDA','ISO'=>'RW', 'costWhatsapp'=>'0.0626'),'966'=>array('name'=>'SAUDI ARABIA','ISO'=>'SA', 'costWhatsapp'=>'0.0310'),'677'=>array('name'=>'SOLOMON ISLANDS','ISO'=>'SB', 'costWhatsapp'=>'0.0135'),'248'=>array('name'=>'SEYCHELLES','ISO'=>'SC', 'costWhatsapp'=>'0.0135'),'249'=>array('name'=>'SUDAN','ISO'=>'SD', 'costWhatsapp'=>'0.0626'),'46'=>array('name'=>'SWEDEN','ISO'=>'SE', 'costWhatsapp'=>'0.0800'),'65'=>array('name'=>'SINGAPORE','ISO'=>'SG', 'costWhatsapp'=>'0.0555'),'290'=>array('name'=>'SAINT HELENA','ISO'=>'SH', 'costWhatsapp'=>'0.0135'),'386'=>array('name'=>'SLOVENIA','ISO'=>'SI', 'costWhatsapp'=>'0.0669'),'47'=>array('name'=>'SVALBARD & JAN MAYEN','ISO'=>'SJ', 'costWhatsapp'=>'0.0135'),'421'=>array('name'=>'SLOVAKIA','ISO'=>'SK', 'costWhatsapp'=>'0.0669'),
    '232'=>array('name'=>'SIERRA LEONE','ISO'=>'SL', 'costWhatsapp'=>'0.0626'),'378'=>array('name'=>'SAN MARINO','ISO'=>'SM', 'costWhatsapp'=>'0.0135'),'221'=>array('name'=>'SENEGAL','ISO'=>'SN', 'costWhatsapp'=>'0.0626'),'252'=>array('name'=>'SOMALIA','ISO'=>'SO', 'costWhatsapp'=>'0.0626'),'597'=>array('name'=>'SURINAME','ISO'=>'SR', 'costWhatsapp'=>'0.0135'),'211'=>array('name'=>'SOUTH SUDAN','ISO'=>'SS', 'costWhatsapp'=>'0.0626'),'239'=>array('name'=>'SAO TOME AND PRINCIPE','ISO'=>'ST', 'costWhatsapp'=>'0.0135'),'503'=>array('name'=>'EL SALVADOR','ISO'=>'SV', 'costWhatsapp'=>'0.0565'),'1721'=>array('name'=>'SINT MAARTEN','ISO'=>'SX', 'costWhatsapp'=>'0.0135'),'963'=>array('name'=>'SYRIAN ARAB REPUBLIC','ISO'=>'SY', 'costWhatsapp'=>'0.0135'),'268'=>array('name'=>'SWAZILAND','ISO'=>'SZ', 'costWhatsapp'=>'0.0626'),'1649'=>array('name'=>'TURKS AND CAICOS ISLANDS','ISO'=>'TC', 'costWhatsapp'=>'0.0135'),'235'=>array('name'=>'CHAD','ISO'=>'TD', 'costWhatsapp'=>'0.0626'),'262'=>array('name'=>'FRENCH SOUTHERN TERRITORIES ','ISO'=>'TF', 'costWhatsapp'=>'0.0135'),'228'=>array('name'=>'TOGO','ISO'=>'TG', 'costWhatsapp'=>'0.0626'),'66'=>array('name'=>'THAILAND','ISO'=>'TH', 'costWhatsapp'=>'0.0555'),'992'=>array('name'=>'TAJIKISTAN','ISO'=>'TJ', 'costWhatsapp'=>'0.0555'),'690'=>array('name'=>'TOKELAU','ISO'=>'TK', 'costWhatsapp'=>'0.0135'),'670'=>array('name'=>'TIMOR-LESTE','ISO'=>'TL', 'costWhatsapp'=>'0.0135'),'993'=>array('name'=>'TURKMENISTAN','ISO'=>'TM', 'costWhatsapp'=>'0.0555'),'216'=>array('name'=>'TUNISIA','ISO'=>'TN', 'costWhatsapp'=>'0.0626'),
    '676'=>array('name'=>'TONGA','ISO'=>'TO', 'costWhatsapp'=>'0.0135'),'90'=>array('name'=>'TURKEY','ISO'=>'TR', 'costWhatsapp'=>'0.015'),'1868'=>array('name'=>'TRINIDAD AND TOBAGO','ISO'=>'TT', 'costWhatsapp'=>'0.0135'),'688'=>array('name'=>'TUVALU','ISO'=>'TV', 'costWhatsapp'=>'0.0135'),'886'=>array('name'=>'TAIWAN, PROVINCE OF CHINA','ISO'=>'TW', 'costWhatsapp'=>'0.0555'),'255'=>array('name'=>'TANZANIA, UNITED REPUBLIC OF','ISO'=>'TZ', 'costWhatsapp'=>'0.0626'),'380'=>array('name'=>'UKRAINE','ISO'=>'UA', 'costWhatsapp'=>'0.0669'),'256'=>array('name'=>'UGANDA','ISO'=>'UG', 'costWhatsapp'=>'0.0626'),'1'=>array('name'=>'U.S. OUTLYING ISLANDS','ISO'=>'UM', 'costWhatsapp'=>'0.0135'),'1'=>array('name'=>'UNITED STATES','ISO'=>'US', 'costWhatsapp'=>'0.0135'),'598'=>array('name'=>'URUGUAY','ISO'=>'UY', 'costWhatsapp'=>'0.0565'),'998'=>array('name'=>'UZBEKISTAN','ISO'=>'UZ', 'costWhatsapp'=>'0.0555'),'39'=>array('name'=>'HOLY SEE (VATICAN CITY STATE)','ISO'=>'VA', 'costWhatsapp'=>'0.0135'),'1784'=>array('name'=>'SAINT VINCENT AND THE GRENADINES','ISO'=>'VC', 'costWhatsapp'=>'0.0135'),'58'=>array('name'=>'VENEZUELA','ISO'=>'VE', 'costWhatsapp'=>'0.0565'),'1284'=>array('name'=>'VIRGIN ISLANDS, BRITISH','ISO'=>'VG', 'costWhatsapp'=>'0.0135'),'1340'=>array('name'=>'VIRGIN ISLANDS, U.S.','ISO'=>'VI', 'costWhatsapp'=>'0.0135'),'84'=>array('name'=>'VIETNAM','ISO'=>'VN', 'costWhatsapp'=>'0.0555'),'678'=>array('name'=>'VANUATU','ISO'=>'VU', 'costWhatsapp'=>'0.0135'),'681'=>array('name'=>'WALLIS AND FUTUNA','ISO'=>'WF', 'costWhatsapp'=>'0.0135'),'685'=>array('name'=>'SAMOA','ISO'=>'WS', 'costWhatsapp'=>'0.0135'),
    '383'=>array('name'=>'KOSOVO','ISO'=>'XK', 'costWhatsapp'=>'0.0135'),'967'=>array('name'=>'YEMEN','ISO'=>'YE', 'costWhatsapp'=>'0.0564'),'262'=>array('name'=>'MAYOTTE','ISO'=>'YT', 'costWhatsapp'=>'0.0135'),'27'=>array('name'=>'SOUTH AFRICA','ISO'=>'ZA', 'costWhatsapp'=>'0.0250'),'260'=>array('name'=>'ZAMBIA','ISO'=>'ZM', 'costWhatsapp'=>'0.0626'),'263'=>array('name'=>'ZIMBABWE','ISO'=>'ZW', 'costWhatsapp'=>'0.0626')
    );
    
    
    $ISOCountry = $arrayCodesISO[$prefix[0]]['ISO'];  //L'utilitzem per fer la crida a la API twilio SMS. Whatsapp de moment no té  API per saber el cost en temps real

    if($ISOCountry==''){
        $ISOCountry = 'US';    
    }
    
    if($metode == 3){
        if($ISOCountry==''){
            $cost = '0.0135';    
        }else{
            $cost = $arrayCodesISO[$prefix[0]]['costWhatsapp'];
        }
        
    }
    
    if($metode == 1){
        $curl = curl_init();

        if (!$curl) {
            die("Couldn't initialize a cURL handle");
        }

        // Set the file URL to fetch through cURL
        curl_setopt($curl, CURLOPT_URL, "https://pricing.twilio.com/v1/Messaging/Countries/".$ISOCountry);


        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, $authString);


        $responseCost = curl_exec($curl);


        $arrayCost = json_decode($responseCost);
        if (isset($arrayCost->price_unit)){
            $price_unit = $arrayCost->price_unit;
        }
        if (isset($arrayCost->outbound_sms_prices[0]->prices[0]->current_price)){
            $cost = $arrayCost->outbound_sms_prices[0]->prices[0]->current_price;
        }
        


        if (!curl_errno($curl)) {
                //guarda exit
                utils::log("$id --- Calcul costos", "logGestor");
                
                $arrayCost = json_decode($responseCost);
                
        } else {
                //guarda log negatiu
                utils::log("$id --- No s'ha pogut Calcular el cost", "logGestor");
               
        }


            curl_close($curl);
        
    }

    
    
}                

//$contacte = 'prova';
//$codiFoto = 'asdasdtsda';
//echo $contacte;
// si el gestor es necessita per escriure a la BBDD, ja sigui mitjançant POST o PBnew_Share.php s'hauran omplert les variables. 
// Fem un INSERT si hi han variables, sino, es una crida al gestor via Cron, i nomes volem revisar la BBDD, aixi que ens saltem el insert.   
if(isset($contacte)){
    $sql = "SELECT `code`, `contact` FROM `gestor` WHERE `code` = '$codiFoto' and `contact` = '$contacte'";

//    echo $sql;
    $CLD_CON->OpenRs($sql);
    while ($CLD_CON->FetchArray()) {
        $code_exist = $CLD_CON->GetArrayField("code");
        $contact_exist = $CLD_CON->GetArrayField("contact");
    }  
    
    
    
    if(!isset($code_exist) && !isset($contact_exist)){
       
        /*21-D-07 Consum SMS i WhatsApp
         * Afegim camps a inserir
         * `cost`, `price_unit`, `ISOCountry`, `prefix`, `owner`, `idBooth`
         * 
         * si es banned state=7 i no enviem res
         */
        $banned = 0;
        // Check owner value before running banned check query
        if ($owner !== null && $owner !== '' && is_numeric($owner)) {
            $CLD_BdD_banned = getNewBdD();
            $sql_banned = "SELECT `banned` FROM CLD_Login l WHERE l.`id_user` = $owner";
            $CLD_BdD_banned->OpenRs($sql_banned);
            while ($CLD_BdD_banned->FetchArray()) {
                $banned = $CLD_BdD_banned->GetArrayField("banned");
            }
        } else {
            utils::log("Warning: Empty or invalid owner value for Code: $codiFoto", "logGestor");
        }

        if($banned){       
            $state = 7;
        }elseif($versioPB != 'web'){
             $state=0;
        }else{             
            $state=1;
        }
        utils::log("esOK: $esOK --- state: $state --- Mirem si entra quan el tinguem banejat", "logGestor");
        
        $CLD_CON->Execute("INSERT INTO gestor(`code`, `method`, `contact`, `timestamp`, `state`, `versioPB`, `token`, `cost`, `price_unit`, `ISOCountry`, `prefix`, `owner`, `idBooth` ) VALUES ('$codiFoto', '$metode', '$contacte', '$now', '$state', '$versioPB', '$token', '$cost', '$price_unit', '$ISOCountry', '$prefix[0]', '$owner', '$idBooth')");
       
//        if($versioPB != 'web'){
//                $CLD_CON->Execute("INSERT INTO gestor(`code`, `method`, `contact`, `timestamp`, `state`, `versioPB`, `token`, `cost`, `price_unit`, `ISOCountry`, `prefix`, `owner`, `idBooth` ) VALUES ('$codiFoto', '$metode', '$contacte', '$now', 0, '$versioPB', '$token', '$cost', '$price_unit', '$ISOCountry', '$prefix[0]', '$owner', '$idBooth')");
//        }
//
//        else{ 
//             utils::log("INSERT INTO gestor(`code`, `method`, `contact`, `timestamp`, `state`, `versioPB`, `token`, `cost`, `price_unit`, `ISOCountry`, `prefix`, `owner`, `idBooth`) VALUES ('$codiFoto', '$metode', '$contacte', '$now', 1, '$versioPB', '$token', '$cost', '$price_unit', '$ISOCountry', '$prefix[0]', '$owner', '$idBooth')", "logGestor");
//            $CLD_CON->Execute("INSERT INTO gestor(`code`, `method`, `contact`, `timestamp`, `state`, `versioPB`, `token`, `cost`, `price_unit`, `ISOCountry`, `prefix`, `owner`, `idBooth`) VALUES ('$codiFoto', '$metode', '$contacte', '$now', 1, '$versioPB', '$token', '$cost', '$price_unit', '$ISOCountry', '$prefix[0]', '$owner', '$idBooth')");    
//           
//
//        }
        
    }
}

//Actualitzar les entrades que ja tenen foto
//$CLD_CON->Execute("UPDATE `gestor` JOIN `photos` ON `photos`.`code` = `gestor`.`code` AND `gestor`.`state` = '0' OR `gestor`.`state` = '1' OR `gestor`.`state` = '2' OR `gestor`.`state` = '3' SET `gestor`.`state` = '4'");
    
    
 
// Buscar entrades de les ultimes dues setmanes
$CLD_CONDades = getNewBdD();
    if ($CLD_CONDades->OpenRs("SELECT * FROM gestor WHERE `timestamp` > SUBTIME (NOW(), '14 0:00:00') AND `state` < 6")){ // Only get the entries with state less than 6
        utils::log("S'han trobat les entrades de les ultimes dues setmanes", "logGestor");
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
  
    while ($CLD_CONDades->FetchArray()) {
//        utils::log(sizeof($CLD_CONDades->FetchArray()), "logGestor");
//        $entry = array();
        $id = $CLD_CONDades->GetArrayField("id");
        $code = $CLD_CONDades->GetArrayField("code");
        $method = $CLD_CONDades->GetArrayField("method");
        $contact = $CLD_CONDades->GetArrayField("contact");
        $timestamp = $CLD_CONDades->GetArrayField("timestamp");
        $state = $CLD_CONDades->GetArrayField("state");
        $last = $CLD_CONDades->GetArrayField("last");
        $error = $CLD_CONDades->GetArrayField("error");
        $versioPB = $CLD_CONDades->GetArrayField("versioPB");
        $token = $CLD_CONDades->GetArrayField("token");
        $entryTime = new DateTime($timestamp);
        $lastTime = new DateTime($last);
        
        /***********************************************
         * 20211213 21-D-07 Consum SMS i whatsapp
         * Recuperem owner i idBooth per saber si els hem guardat ja a BD. Si no el anirem a buscar i actualitzarem
         */
        $owner = $CLD_CONDades->GetArrayField("owner");
        $idBooth = $CLD_CONDades->GetArrayField("idBooth");
        
        /***********************************************
         * 20211213 21-D-07 Consum SMS i whatsapp
         * Si en el moment de guardar a gestor que s'ha d'enviar el fitxer encara no s'ha pujat no tindrem el registre a photos i no haurà pogut guardar owner i idBooth
         * Ho actualitzem per si un cas.
         */
        if($owner==0 || $idBooth==0){
            $CLD_CONowner = getNewBdD();
            $sqlOwner = "SELECT ab.owner, p.pbs_id FROM `photos` p 
         LEFT JOIN App_booths ab ON ab.idBooth=p.pbs_id  
          WHERE p.code = '$code'";
            $CLD_CONowner->OpenRs($sqlOwner);
            while ($CLD_CONowner->FetchArray()) {
                $owner = $CLD_CONowner->GetArrayField("owner");
                $idBooth = $CLD_CONowner->GetArrayField("pbs_id");
            }  
        } 


        if (($owner !== null && $owner !== '' && is_numeric($owner)) && 
            ($idBooth !== null && $idBooth !== '' && is_numeric($idBooth))) {
            $CLD_CONUpdate = getNewBdD();
            $CLD_CONUpdate->Execute(
                "UPDATE gestor SET owner = '$owner', idBooth = '$idBooth' WHERE id = $id"
            );
        } else {
            utils::log("$id --- Warning: Invalid owner or idBooth values for update. Owner: $owner, Booth: $idBooth, Code: $code", "logGestor");
        }

        // ——— CONTACT CLEANUP for SMS and WHATSAPP
        if ($method == 1 || $method == 3) {
            // remove everything except digits and '+'
            $contact = preg_replace('/[^\d\+]/', '', $contact);
            // ensure thereis maximum one '+' in the contact start
            $contact = preg_replace('/^\++/', '+', $contact);
            // remove all other '+' after the first character
            $contact = preg_replace('/(?!^)\+/', '', $contact);
            // if there's no starting '+', add it
            if (substr($contact, 0, 1) !== '+') {
                $contact = '+' . $contact;
            }
        }
        
        $banned = 0;
        // Check owner value before running banned check query
        if ($owner !== null && $owner !== '' && is_numeric($owner)) {
            $CLD_BdD_banned2 = getNewBdD();
            $sql_banned = "SELECT `banned` FROM CLD_Login l WHERE l.`id_user` = $owner";
            $CLD_BdD_banned2->OpenRs($sql_banned);
            while ($CLD_BdD_banned2->FetchArray()) {
                $banned = $CLD_BdD_banned2->GetArrayField("banned");
            }
        } else {
            utils::log("$id --- Warning: Empty or invalid owner value. ID: $id, Code: $code", "logGestor");
        }

        if($banned){       
            $state = 7;
        }
        
        
        utils::log($code, "logGestor");
//        //array_push($entries, $entry);
//            array_push($entries, $entry);
    
//
//        utils::log("Trobades " . count($entries) . " als ultims 14 dies", "logGestor");
//        utils::log($code, "logGestor");
        //construim la petició del missatge

        utils::log("Construint llistes individuals", "logGestor");

//        foreach ($entries as $entry){
//            $entryID = $id;
//            if(!$code){
//                utils::log("no code", "logGestor");
//                $code = $code;
//                $contact = $contact;
//                utils::log($code, "logGestor");
////            }


            utils::log("       ", "logGestor");
            utils::log("--- Nova Entry $entryID ---", "logGestor");
            utils::log("       ", "logGestor");
            // amb 'state' comprobem en quin pas está la entrada i comparantho amb un dels timestamps 
            // sabem si ja ha passat suficient temps com per enviar un missatge
            // En el primer Switch triem la plantilla a utilitzar i el state en el que ha de acabar la entrada

            $enviar=false;
            $CLD_CONPhotos = getNewBdD();
            $CLD_CONPhotos->OpenRs("SELECT code FROM photos WHERE code = '$code'");
            utils::log("SELECT code FROM photos WHERE code = '$code'", "logGestor");
            while ($CLD_CONPhotos->FetchArray()) {
                utils::log("entra", "logGestor");
                $Pcode = $CLD_CONPhotos->GetArrayField("code");
                
            }
    
            switch ($state) {
                case '0': //(acaba de arribar)
                    utils::log("$entryID --- Case 0: Acaba de arribar", "logGestor");
    //                $code = $code;
                    $enviar=true;
                    $gestorID=null;



                    if ($Pcode != NULL){
                        //code a la bbdd. Ara comprovem si realment la foto existeix
                        utils::log("$entryID --- Hi ha una correspondencia a la BBDD, comprovant si l'arxiu existeix --- $Pcode", "logGestor");

                        //$filepath = $_SERVER['DOCUMENT_ROOT;
                        $filepath = G_PATH;
                        $CLD_CONExisteix = getNewBdD();
                        $CLD_CONExisteix->Execute("UPDATE `gestor` SET `state` = '6', `owner` = '$owner', `idBooth` = '$idBooth' WHERE `code` = '$code' AND `contact` = '$contact'");

                    }else{
                        $CLD_CONExisteix = getNewBdD();
                        $CLD_CONExisteix->Execute("UPDATE `gestor` SET `state` = '1', `owner` = '$owner', `idBooth` = '$idBooth' WHERE `code` = '$code' AND `contact` = '$contact'");
                    }

                    
                    $plantilla = "enviar.php";

                    break;

                case '1': //(no estaba abans, esperem 30 mins)  
                    if ($Pcode != NULL){                    
                        $plantilla = "enviar.php";
                        utils::log($contact, "logGestor");
                        $CLD_CONExisteix = getNewBdD();
                        $CLD_CONExisteix->Execute("UPDATE `gestor` SET `state` = '6', `owner` = '$owner', `idBooth` = '$idBooth' WHERE `code` = '$code' AND `contact` = '$contact'");
                        $enviar=true;
                    }


                    break;
                case '5': //(ha fallat al enviarse, retry)

                   // utils::log("$entryID --- Case 5: Ha fallat un envío, farem al menys un retry passades 24 hores", "logGestor");
                        if ($lastTime < $s1dia && $error!="No s'ha pogut enviar, esperant retry"){
                            utils::log("$entryID --- Ja han passat les 24 hores i no s'ha enviat cap retry, preparant per reintentar", "logGestor");
                            $enviar=true;
                            $plantilla = "enviar.php";
                        }else{
                            //utils::log("$entryID --- Encara no ha passat el temps necessari, o ja s'ha fet un reintent sense éxit", "logGestor");
                        }
                        break;//20250203breakGestor
                case '7': //banUser
                        $enviar=false;
                        $CLD_CONBanned3 = getNewBdD();
                        //Fem update state=7 perque no el tinguem en compte a partir d'ara en el SELECT
                        $CLD_CONBanned3->Execute("UPDATE `gestor` SET `state` = '7', `owner` = '$owner', `idBooth` = '$idBooth' WHERE `code` = '$code' AND `contact` = '$contact'");
                    break;
                
            }
          $Pcode = NULL;
//
            // ara triem el métode de enviament
            utils::log($enviar, "logGestor");
            if ($enviar){
                utils::log("$entryID --- Escollim métode de enviament", "logGestor");
                switch ($method){
                    case '0'://email
                        utils::log("$entryID --- Email", "logGestor");
                        $plantilla = "./templates/email/" . $plantilla;
                        array_push($llistaEmail, $entry);
                        break;
                    case '1'://sms
                        //no serveix de res
//                        $mini = substr($contacte,1);
//                        $contacte = "+". ltrim($mini,"0 ");
                        
                        
                        utils::log("$entryID --- SMS", "logGestor");
                        $plantilla = "./templates/sms/" . $plantilla;
                        array_push($llistaSMS, $entry);
                        break;
                    case '2'://telegram
                        utils::log("$entryID --- Telegram", "logGestor");
                        if($contact){
                            $plantilla = "./templates/telegram/" . $plantilla;
                            array_push($llistaTelegram, $entry);
                        }

                        break;
                    case '3'://whatsapp
                        utils::log("$entryID --- Whatsapp", "logGestor");
                        if($contact){
                            $plantilla = "./templates/whatsapp/" . $plantilla;
                            array_push($llistaWhatsapp, $entry);
                        }
                    break;
                }
            }

        
    

utils::log("S'han de enviar: Emails - ". count($llistaEmail). ", SMS - ".count($llistaSMS).", Telegram - ". count($llistaTelegram).", Whatsapp - ". count($llistaWhatsapp), "logGestor");        
    
utils::log("= Comencem a enviar els Emails =", "logGestor");
require_once G_PATH . 'common/mailer/class.phpmailer.php';

	$from = "noreply@myphotocode.com";
	$from_str = "noreply";
//20250203mail	$host = "smtp.ionos.com";
	$host = "smtp.ionos.es";//20250203mail
        
        $username = "noreply@myphotocode.com";
//20250203mail	$password = "d1g1t4lc3ntr3&";
	$password = "MyP0t0C0d3$!";//20250203mail

foreach ($llistaEmail as $entry){

    // to subject message
    include $plantilla;
    
    $id = $id;
    
    
    $mail = new PHPMailer;
    

    $mail->isSMTP();                                        // Set mailer to use SMTP
    $mail->Host = $host;                                    // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                                 // Enable SMTP authentication
    $mail->Username = $username;                            // SMTP username
    $mail->Password = $password;                            // SMTP password
    $mail->SMTPSecure = 'ssl';                              // Enable encryption, 'ssl' also accepted
    $mail->Port = 465;

    $mail->From = $from;
    $mail->FromName = 'noreply';
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
        $CLD_CONMail = getNewBdD();

        if (strpos($mail->ErrorInfo, "You must provide at least one recipient email address.") !== false) {
            // If the error is exactly about a missing recipient, update immediately to state 8.
            if ($CLD_CONMail->Execute("UPDATE gestor SET `last`='$now', `state`=8, `error`='$mail->ErrorInfo' WHERE `id`=$id")) {
                utils::log("$id --- EMAIL updated to state 8 (invalid email)", "logGestor");
            } else {
                utils::log("$id --- Failed to update EMAIL to state 8", "logGestor");
            }
        } else {
            // If we're already in state 5 and the last attempt is older than 24 hours, update to state 8, permanently failed and stop retry.
            if ($state == '5' && $lastTime < $s1dia) {
                if ($CLD_CONMail->Execute("UPDATE gestor SET `last`= '$now', `state`=8, `error`='$mail->ErrorInfo' WHERE `id`=$id")) {
                    utils::log("$id --- EMAIL updated to state 8 (permanent failure)", "logGestor");
                } else {
                    utils::log("$id --- Failed to update EMAIL to state 8", "logGestor");
                }
            } else {
                // Else, update/keep it in state 5 to allow further retries.
                if ($CLD_CONMail->Execute("UPDATE gestor SET `last`= '$now', `state`=5, `error`='$mail->ErrorInfo' WHERE `id`=$id")) {
                    utils::log("$id --- Set EMAIL in state 5", "logGestor");
                } else {
                    utils::log("$id --- Failed to update EMAIL to state 5", "logGestor");
                }
            }
        }
    } else {
        //guarda éxit
        utils::log("$id --- Email enviat a $to correctament", "logGestor");
        $CLD_CONMail = getNewBdD();
        if ($CLD_CONMail->Execute("UPDATE gestor SET `last`= '$now', `state`=6 WHERE `id`=$id")) {
            //guardar log positiu
            utils::log("$id --- UPDATE correcte", "logGestor");
        } else {
            //guardar log negatiu
            utils::log("$id --- Ha fallat el UPDATE", "logGestor");
        }
    }

}

utils::log("= Comencem a enviar els SMS =", "logGestor");
foreach ($llistaSMS as $entry) {

    include $plantilla;


    $id = $id;

    utils::log("--- $id --- ", "logGestor");


    $curl = curl_init();

    if (!$curl) {
        die("Couldn't initialize a cURL handle");
    }

    // Set the file URL to fetch through cURL
    curl_setopt($curl, CURLOPT_URL, $urlTwilio);

    $data = array(
        "To" => $contact,
        "From" => "MGa19ab83dcefdae8c6ed207158ab9c46e",
        "Body" => $message,
    );


    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_USERPWD, $authString);


    $response = curl_exec($curl);


    if (!curl_errno($curl)) {
        //guarda exit
        utils::log("$id --- SMS enviat", "logGestor");
        $CLD_CONSMS = getNewBdD();
        if ($CLD_CONSMS->Execute("UPDATE gestor SET `last`= '$now', `state`=6 WHERE `id`=$id")) {
            //guardar log positiu
            utils::log("$id --- UPDATE correcte", "logGestor");
        } else {
            //guardar log negatiu
            utils::log("$id --- Ha fallat el UPDATE", "logGestor");
        }
    } else {
        //guarda log negatiu
        utils::log("$id --- No s'ha pogut enviar el SMS", "logGestor");
        $error .= '\n cURL error: ' . curl_error($curl);
        $CLD_CONSMS = getNewBdD();
        // If we're already in state 5 and the last attempt is older than 24 hours, update to state 8, permanently failed and stop retry.
        if ($state == '5' && $lastTime < $s1dia) {
            if ($CLD_CONSMS->Execute("UPDATE gestor SET `last`='$now', `state`=8, `error`='$error' WHERE `id`=$id")) {
                utils::log("$id --- SMS updated to state 8 (permanent failure)", "logGestor");
            } else {
                utils::log("$id --- Failed to update SMS to state 8", "logGestor");
            }
        } else {
            // Else, update/keep it in state 5 to allow further retries.
            if ($CLD_CONSMS->Execute("UPDATE gestor SET `last`='$now', `state`=5, `error`='$error' WHERE `id`=$id")) {
                utils::log("$id --- Set SMS in state 5", "logGestor");
            } else {
                utils::log("$id --- Failed to update SMS to state 5", "logGestor");
            }
        }
    }


    curl_close($curl);
}

/**********************************************************************
 * 21-D-03-Total-Share-Whatsapp
 * ENVIEM WHATSAPP
 * 
 */

utils::log("= Comencem a enviar els Whatsapp =", "logGestor");
foreach ($llistaWhatsapp as $entry) {

    include $plantilla;


    $id = $id;

    utils::log("--- $id --- ", "logGestor");

    // --- HARD DISABLE WHATSAPP: set state=8 and skip Twilio ---
    $CLD_CONWapp = getNewBdD();
    $errorMsg = addslashes("WhatsApp disabled globally; skipping Twilio send.");
    if ($CLD_CONWapp->Execute(
        "UPDATE gestor
         SET `last`=NOW(),
             `state`=8,
             `error`=CONCAT(IFNULL(`error`,''),' | $errorMsg')
         WHERE `id`=$id"
    )) {
        utils::log("$id --- Whatsapp set to state 8 (global disable)", "logGestor");
    } else {
        utils::log("$id --- Failed to update Whatsapp to state 8", "logGestor");
    }
    continue;
    // --- END HARD DISABLE BLOCK ---
    
    
//    error_log( "TO_DELETE gestor 20250214whatsapp, message: $message" );//20250214whatsapp
    
    error_log( "TO_DELETE gestor 20250214whatsapp, $code: $code" );//20250214whatsapp

    $curl = curl_init();

    if (!$curl) {
        die("Couldn't initialize a cURL handle");
    }
    
    curl_setopt($curl, CURLOPT_POST, true);//20250216twilio
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);//20250216twilio

    // Set the file URL to fetch through cURL
    // curl_setopt($curl, CURLOPT_URL, $urlTwilio);
    
//20250216twilio_02    $content_variables = array("1" => $code);//20250216twilio
 //20250216twilio_03   $content_variables = array("1" => "\"$code\"");//20250216twilio_02
     $content_variables = json_encode(array("1" => "https://www.myphotocode.com/$token"));

    
    error_log( "TO_DELETE gestor 20250216whatsapp, content_variables: $content_variables ");//20250216twilio_03

    $data = array(
        "To" => "whatsapp:".$contact,
        "From" => "whatsapp:+15866857271",
//20250216twilio_04        "To" => $contact,//20250216twilio_04
        /*
           When sending a message with a messaging service, Twilio will immediately set the message’s status to accepted. 
         * Twilio will then determine the optimal From phone number from your service. 
         * Any delivery errors will be sent asynchronously to your StatusCallbackURL.
         *      */
        //"From" => "whatsapp:+14155238886", //sender sandbox
        //"From" => "whatsapp:+15866857271", //sender twilio Approved by WhatsApp. També funcionaria
        "MessagingServiceSid" => "MGa19ab83dcefdae8c6ed207158ab9c46e",
//20250216twilio        "From" => "+15866857271",//20250216twilio_05
        //Podem enviar les imatges així però no ho farem perque només està permés en cas que l'usuari respongui previament
        //TODO: provar si el que diu la documentacio de twilio es cert i no es pot enviar adjunt realment sense haver rebut resposta de l'usuari ;)
        //    "MediaUrl0" => "https://www.myphotocode.com/events/2021092340241/W7UXM5X6G3.jpg",
        //    "MediaUrl1" => "https://www.myphotocode.com/events/2021092340241/W7UXM5X6G3.jpg",
        //20250214whatsapp INICI
        //20250214whatsapp "Body" => $message,
        "ContentSid" => "HXa5b06d43981d595818485a00d889410d",
//        "ContentVariables" => { "1": $code }
//20250216twilio        "ContentVariables" => array( "1" => $code ),
        "ContentVariables" => $content_variables); //20250216twilio

        //20250214whatsapp FINAL
        //contentVariables
    


    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    // curl_setopt($curl, CURLOPT_USERPWD, $authString);


    $response = curl_exec($curl);
    
    error_log( "TO_DELETE gestor 20250214whatsapp,  curl response: $response. Errno: " . curl_errno($curl) );//20250214whatsapp


    if (!curl_errno($curl)) {
        //guarda exit
//20250207whatsapp        utils::log("$id --- Whatsapp enviat", "logGestor");
        utils::log("$id --- Whatsapp enviat, response: $response", "logGestor");//20250207whatsapp
        $CLD_CONSMS = getNewBdD();
        if ($CLD_CONSMS->Execute("UPDATE gestor SET `last`= '$now', `state`=6 WHERE `id`=$id")) {
            //guardar log positiu
            utils::log("$id --- UPDATE correcte", "logGestor");
        } else {
            //guardar log negatiu
            utils::log("$id --- Ha fallat el UPDATE", "logGestor");
        }
    } else {
        //guarda log negatiu
        utils::log("$id --- No s'ha pogut enviar el Whatsapp", "logGestor");
        $error .= '\n cURL error: ' . curl_error($curl);
        $CLD_CONSMS = getNewBdD();
        // If we're already in state 5 and the last attempt is older than 24 hours, update to state 8, permanently failed and stop retry.
        if ($state == '5' && $lastTime < $s1dia) {
            if ($CLD_CONSMS->Execute("UPDATE gestor SET `last`='$now', `state`=8, `error`='$error' WHERE `id`=$id")) {
                utils::log("$id --- Whatsapp updated to state 8 (permanent failure)", "logGestor");
            } else {
                utils::log("$id --- Failed to update Whatsapp to state 8", "logGestor");
            }
        } else {
            // Else, update/keep it in state 5 to allow further retries.
            if ($CLD_CONSMS->Execute("UPDATE gestor SET `last`='$now', `state`=5, `error`='$error' WHERE `id`=$id")) {
                utils::log("$id --- Set Whatsapp in state 5", "logGestor");
            } else {
                utils::log("$id --- Failed to update Whatsapp to state 5", "logGestor");
            }
        }
    }


    curl_close($curl);
}

/**********************************************************************
 * FINS AQUI WHATSAPP
 */



utils::log("= Comencem a enviar els Telegrams =", "logGestor");
foreach ($llistaTelegram as $entry){

    require $plantilla;
    
    $id = $id;
    
    utils::log("--- $id --- ", "logGestor");
    
    
    //$apiToken = "563225064:AAHiZ8ZJdoL144KMUkTQNEarKZa2pTdwMzg"; //ens han esborrat el booth DCphotobooth per inactivitat?
    $apiToken = "2127043465:AAHqIGAwX4fMwnO5HwcE3T5ijBd-8a25ptg";
    

    //$chatID = $entry["contact"];
    $chatID = $contact;
    $message = "https://api.telegram.org/bot$apiToken/sendMessage?chat_id=$chatID&text=$message";
    $response = file_get_contents("$message");

    if($response){
        utils::log("$id --- Telegram enviat correctament", "logGestor");
         //guarda exit
        $CLD_CONTel = getNewBdD();
        if($CLD_CONTel->Execute("UPDATE gestor SET `last`= '$now', `state`=6 WHERE `id`=$id")){
            //guardar log positiu
            utils::log("$id --- UPDATE correcte", "logGestor");
        }else{
            //guardar log negatiu
            utils::log("$id --- Ha fallat el UPDATE", "logGestor");
        }
    }else{
        //guarda log negatiu
        utils::log("$id --- No s'ha pogut enviar el Telegram", "logGestor");
        utils::log("$message --- no pot enviar Telegram entry -- $contact", "logGestor");
        $CLD_CONTel = getNewBdD();
        // If we're already in state 5 and the last attempt is older than 24 hours, update to state 8, permanently failed and stop retry.
        if ($state == '5' && $lastTime < $s1dia) {
            if ($CLD_CONTel->Execute("UPDATE gestor SET `last`= '$now', `state`=8, `error`='$error' WHERE `id`=$id")) {
                utils::log("$id --- Telegram updated to state 8 (permanent failure)", "logGestor");
            } else {
                utils::log("$id --- Failed to update Telegram to state 8", "logGestor");
            }
        } else {
            // Else, update/keep it in state 5 to allow further retries.
            if ($CLD_CONTel->Execute("UPDATE gestor SET `last`= '$now', `state`=5, `error`='$error' WHERE `id`=$id")) {
                utils::log("$id --- Set Telegram in state 5", "logGestor");
            } else {
                utils::log("$id --- Failed to update Telegram to state 5", "logGestor");
            }
        }
        
    }
  
}
unset($llistaEmail);
$llistaEmail = array();  
unset($llistaSMS);
$llistaSMS = array(); 
unset($llistaWhatsapp);
$llistaWhatsapp = array(); 
unset($llistaTelegram);
$llistaTelegram = array();  
}

utils::log("       ", "logGestor");
utils::log("====== Fi del Cicle ======", "logGestor");
utils::log("       ", "logGestor");
ob_get_contents();
ob_end_clean();
