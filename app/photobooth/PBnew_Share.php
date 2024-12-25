<?php

// * common.php carregarà:
// * $APP_dongle de param dongle
// * $APP_rand_string de param idmaq (sense la primera lletra) 
// * $APP_inTimeSerial de param t (o time satamp actual si no existeix). Perarat per a query sql 
// * $APP_idBooth nou o de param idb
// * $APP_okResp per a fer echo ok
// * $APP_araTimeSerial, time stamp actual del servidor

//20181010 require_once "../../common/global.php";
//20181010 require_once '../../common/conexio.php';
require("common.php");
include_once '../../common/conexio.php';
$APP_BdD_banned = getNewBdD();

if($APP_common_error){
    APP_fesLog("Error common in PBnew_Share($idb): $APP_common_error.");
    APP_fesLogDebbug("Error common in PBnew_Share($idb): $APP_common_error.","logPBnew_Share");
    return;
}
if(!$APP_dongleOK) return;

//params
if(isset($_REQUEST['pb'])){ $versioPB = $_REQUEST['pb'];}
if(isset($_REQUEST['code'])){ $codiFoto = $_REQUEST['code'];}
if(isset($_REQUEST['mtd'])){ $metode = $_REQUEST['mtd'];}
if(isset($_REQUEST['dt'])){ $contacte = APP_base64_decode_custom($_REQUEST['dt']);}
if(isset($_REQUEST['idb'])){ $idb = $_REQUEST['idb'];}
//Inicialitzem variables de costos SMS o whatsapp
    APP_fesLogDebbug("$codiFoto --- Inicialitzem vars... Calcular el cost", "logPBnew_Share");
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
    APP_fesLogDebbug("$idBooth --- $owner --- Inicialitzem vars... Calcular el cost", "logPBnew_Share");
 

APP_fesLogDebbug("TRACE to delete PBnew_Share($versioPB,'$codiFoto',$metode,'$contacte','{$_REQUEST['dt']}','$idb)","logPBnew_Share");
$token = "";
if($metode == 1){
    $rand = rand(1000, 9999);
    $c = substr($codiFoto, -3);
    $token = 'SMS'.$c.$rand;
 } 

$sql = "SELECT id FROM `gestor` WHERE `code` = '$codiFoto' AND `contact` = '$contacte' AND `method`='$metode';";
APP_fesLogDebbug("TRACE to delete PBnew_Share, sql check: $sql","logPBnew_Share");
$esOK = $APP_BdD->OpenRs($sql);

if(!$esOK){
    APP_fesLogDebbug("ERROR on PBnew_Share, sql check ($sql) error: $APP_BdD->errno, $APP_BdD->error","logPBnew_Share");
    echo "Error - code TS01";
    return;
}
//treu espais i els 0 del davant del numero
if ($metode == 1 || $metode == 3) {
    
    
    
    //21-D-07 Consum SMS i WhatsApp
    //RECOPILEM INFO COSTOS SMS o Whatsapp
     APP_fesLogDebbug("$codiFoto --- Iniciem calculs... Calcular el cost", "logPBnew_Share");
     
    
    $mini = substr($contacte,1);  
    $mini = ltrim($mini,"0 ");    
    $telefonArray = split(' ',$mini);  
    $prefix = $telefonArray[0];
    $telefon = $telefonArray[1];    
    $telefon = preg_replace("/[^0-9]/", "", $telefon );     //li treiem -  i . que deixa escriure el teclat wtf
    $contacte = "+". $prefix. " ".$telefon;
    
    
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
        curl_setopt($curl, CURLOPT_USERPWD, "ACa495bb879ddb69a2c3afbdd8eba6cfbf:05aad8b9b2c4aa71f2e7ccb2ba014527");


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
                APP_fesLogDebbug("$id --- Calcul costos", "logPBnew_Share");
                
                $arrayCost = json_decode($responseCost);
                
        } else {
                //guarda log negatiu
                APP_fesLogDebbug("$id --- No s'ha pogut Calcular el cost", "logPBnew_Share");
               
        }


            curl_close($curl);
        
    } 
    
}

$exist = false;
if($APP_BdD->FetchRs()){//exist
    $exist = true;
}
$APP_BdD->CloseRs();

//Això ho controlarem a gestor. El PB no envia sempre ni el pbid ni tindrem l'owner si encara no hi ha la foto a la BD
//$sql_banned = "SELECT `banned` FROM CLD_Login l, App_booths b WHERE b.`idBooth` = $versioPB AND b.`owner` = l.`id_user`";
//$esOK = $APP_BdD_banned->OpenRs($sql_banned);
//
//if($APP_BdD_banned->FetchRs()){//exist
//    $exist_ban = true;
//}

if(!$exists){
    
        $state = 0;
    
    $sql = "INSERT INTO gestor SET `code`='$codiFoto', `method`='$metode', `contact`='$contacte', `timestamp`=$APP_araTimeSerial, `state`='$state', `versioPB`='$versioPB', `token`='$token', `cost`='$cost', `price_unit`='$price_unit', `ISOCountry`='$ISOCountry', `prefix`='$prefix[0]', `owner`='$owner', `idBooth`='$idBooth' ";
    APP_fesLogDebbug("TRACE to delete PBnew_Share, sql insert: $sql","logPBnew_Share");
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK) {
        APP_fesLogDebbug("ERROR on PBnew_Share, sql insert ($sql) error: $APP_BdD->errno, $APP_BdD->error","logPBnew_Share");
        echo  "Error - code TS02";
        return;
    }
}
ob_start();
echo $APP_okResp;
APP_fesLogDebbug("TRACE to delete PBnew_Share, ob_: " . ob_get_contents(),"logPBnew_Share");
header('Connection: close');
header('Content-Length: '.ob_get_length());
ob_end_flush();
ob_flush();
flush();
//include '../../sections/totalshare/gestor.php';
?>
