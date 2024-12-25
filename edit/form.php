<?php
require_once dirname(__FILE__) . '/../common/global.php';

include '../sessio.php';
require_once G_PATH . 'common/conexio.php';

$form = $_POST['form'];
$ID = $_POST['id'];

$baseController = new baseController;

//echo "<div id='loadingEdit' style='display:none;position: relative;width: 90%;height: 90%;margin-left: 5%;margin-top: 4%;'>";
//echo "<img src='images/web/loading.gif' style='width:20%;margin-left:40%;margin-top:20%;'>";
//echo "</div>";
//echo "<div id='contentEdit'>";
switch($form){
    case 1: //EDIT PHOTO OF PROFILE
        include './forms/owner/setPhoto.php';
        break;
    case 2: //OWNER , Alert Email
        include './forms/owner/setAlertEmail.php';
        break;
    case 3: //OWNER , Alert Email
        include './forms/owner/setPassword.php';
        break;
    case 4://EDITAR CONTACTE
        include './forms/owner/setContact.php';
        break;
    case 5: //EDITA Addreça
        $type = 0;
        include './forms/owner/setAddress.php';
        break;
    case 6: //LOCATION NAME , PHOTOBOOTH
        include './forms/photobooths/setLocationName.php';
        break; 
    case 7://COORDENADES , PHOTOBOOTH
        include './forms/photobooths/setCoordenades.php';
        break;
    case 8: //PAYPAL MERCHANT ID , PHOTOBOOTHS
        include './forms/photobooths/setPayPalMerchantID.php';
        break;
    case 9: // NO FET, mail report
        include './forms/photobooths/ActiveDesactiveReports.php';
        break;
    case 10: //CANVIA VALORS DE LES ALERTES DE FILM
        include './forms/photobooths/setFilmAlert.php';
        break;
    case 11://CANVIA VALORS DE LES ALERTES DE MONEY
        include './forms/photobooths/setMoneyAlert.php';
        break;
    case 12://CANVIA VALORS DE LES ALERTES DE OFFLINE
        include './forms/photobooths/setOfflineAlert.php';
        break;
    case 13://CANVIA EVENT MANAGER
        include './forms/events/setEventManager.php';
        break;
    case 14://CANVIA TITOL EVENT
        include './forms/events/setTitleEvent.php';
        break;
    case 15://CANVIA BACKGROUND CLOUD
        include './forms/events/setBackgroud.php';
        break;
    case 16://CANVIA PRIVADESA EVENT
        include './forms/events/setPrivate.php';
        break;
    case 17://CANVIA BANNER
        include './forms/events/setBanner.php';
        break;
    case 18://CANVIA REGUNTA 1
        include './forms/events/setQuestion1.php';
        break;
    case 19://CANVIA REGUNTA 2
        include './forms/events/setQuestion2.php';
        break;
    case 20://CANVIA REGUNTA 3
        include './forms/events/setQuestion3.php';
        break;
    case 21://CANVIA LOGO
        include './forms/events/setLogo.php';
        break;
    case 22://CANVIA Text Lateral
        include './forms/events/setTextPhoto.php';
        break;
    case 23://CANVIA Afegeix Frames
        include './forms/events/addFrames.php';
        break;
    case 24://CANVIA Afegeix Frames
        include './forms/events/addDCFrames.php';
        break;
    case 25://CANVIA Afegeix Frames
        include './forms/events/addWelcomes.php';
        break;
   case 26://CANVIA Afegeix Frames
        include './forms/events/addByes.php';
        break;
   case 27://CANVIA Afegeix Frames
        include './forms/events/addCustomShots.php';
        break;
   case 28://CANVIA Afegeix Frames
        include './forms/events/addBGmusic.php';
        break;
   case 29://CANVIA Afegeix Frames
        include './forms/events/addHeader.php';
        break;
    case 30://Importa fotos de un event a un altre
        include './forms/events/importPhotos.php';
        break;
    case 31://  Canvia el nom de la companyia de un owner
        include './forms/owner/setCompanyName.php';
        break;
    case 32://Canvia el nom d'usuari
        include './forms/owner/setUsername.php';
        break;
    case 33://canvia el SN de una màquina
        include './forms/photobooths/setSN.php';
        break;
    case 34://Afegir Bussiness Address
        $type=1;
        include './forms/owner/addAddress.php';
        break;
    case 35://Afegir Shiping Address
        $type=0;
        include './forms/owner/addAddress.php';
        break;
    case 36://Afegir Owner Contact 
        $type=0;
        include './forms/owner/addContact.php';
        break;
    case 37://Canvia el dongle Pairing
        include './forms/photobooths/addDonglePairing.php';
        break;
    case 38://Afegir Incidencia sobre un photobooth
        include './forms/photobooths/addIncident.php';
        break;
    case 39://Afegir comentari a una Incidencia sobre un photobooth
        include './forms/photobooths/addIncidentComent.php';
        break;
    case 40://Afegir comentari a una Incidencia sobre un photobooth
        include './forms/photobooths/setBoothOwner.php';
        break;
    case 41://Afegir comentari a una Incidencia sobre un photobooth
        include './forms/photobooths/newOwner.php';
        break;
    case 42://Afegir comentari a una Incidencia sobre un photobooth
        include './forms/photobooths/oldOwner.php';
        break;
    case 43://Afegir comentari a una Incidencia sobre un photobooth
        $type=1;
        $to = 2;
        include './forms/owner/addAddress.php';
        break;
    case 44://Production to        
        include './forms/photobooths/productionTo.php';
        break;
    case 45://Production to Finish Factory Product       
        include './forms/photobooths/productionToFinishProduct.php';
        break;
    case 46:       
        include './forms/photobooths/toDistributorFromFinish.php';
        break;
    case 47:  
        include './forms/photobooths/fromDistributorStock.php';
        break;
    case 48:   
        include './forms/photobooths/toDamage.php';
        break;
    case 49:
        include './forms/photobooths/toRefurbished.php';
        break;
    case 50://From Refurbished Status
        include './forms/photobooths/fromRefurbished.php';
        break;
    case 51://To Incomplete Form
        include './forms/photobooths/toIncomplete.php';
        break;
    case 52://To Incomplete Form
        include './forms/photobooths/fromIncomplet.php';
        break;
    case 53://To Incomplete Form
        include './forms/photobooths/fromIncompletToStock.php';
        break;
    case 54://To Incomplete Form
        include './forms/photobooths/toIncomplete2.php';
        break;
    case 55://To Incomplete Form
        include './forms/photobooths/fromOwner.php';
        break;
    case 56://To Incomplete Form
        include './forms/photobooths/toReturned.php';
        break;
    case 57://To Incomplete Form
        include './forms/photobooths/fromReturned.php';
        break;
    case 58://To Incomplete Form
        include './forms/photobooths/toDistributorStock.php';
        break;
    case 59://To Incomplete Form
        include './forms/components/toOwner.php';
        break;
    case 60://To Incomplete Form
        include './forms/components/setDistributor.php';
        break;
    case 61://To Incomplete Form
        include './forms/components/addComponent.php';
        break;
    case 62://To Incomplete Form
        include './forms/events/addNew.php';
        break;
    case 63:
        include './forms/productions/newProduction.php';
        break;
    case 64:
        include './forms/productions/toIncompleteManufacturer.php';
        break;
    case 65:
        include './forms/owner/generateUser.php';
        break;
    case 66:
        include './forms/events/hastags.php';
        break;
    case 67:
        include './forms/payxprint/editPayPrintDongle.php';
        break;
    case 68:
        include './forms/payxprint/editPayPrintOrder.php';
        break;
    case 69: 
        include './forms/photobooths/editPhotoBoothManufacturer.php';
        break;
    case 70: //EDITA Addreça
        $type = 1;
        include './forms/owner/setAddress.php';
        break;
    case 71: 
        include './forms/financingCode/controllerFormFinancingCode.php';
        break;
    case 72: 
        include './forms/events/selectedDCFrames.php';
        break;
    case 73:
        include './forms/events/addDCCollages.php';
        break;
    case 74: 
        include './forms/events/selectedDCCollages.php';
        break;
    case 75: 
        include './forms/events/addCollages.php';
        break;
    case 76: 
        include './forms/events/addTemplates.php';
        break;
    case 77: 
        include './forms/manuals/addManual.php';
        break;
    case 78: 
        include './forms/manuals/editManual.php';
        break;
    case 79:
        include './forms/manuals/manageItems.php';
        break;
    case 80: 
        include './forms/events/deleteTemplates.php';
        break;
    case 81: 
        include './forms/events/addDCTemplates.php';
        break;
    case 82: 
        include './forms/photobooths/setTeamviewerId.php';
        break;
    case 83:
        include './forms/upgrade/editBootDCAllowed.php';       
        break;
    
//20150709pbname INICI
    case 101: //PHOTOBOOTH NAME , PHOTOBOOTH
        include './forms/photobooths/setPhotoBoothName.php';
        break; 
//20150709pbname FINAL
}
//echo "</div>";
?>
