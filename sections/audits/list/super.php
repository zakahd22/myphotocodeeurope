<?php

include '../../../sessio.php';
require_once G_PATH . "common/Classes/baseController.php";
require_once G_PATH . 'sections/audits/functions/RepdcManager.php';

class auditsManager extends baseController {

    public $action = false;
    public $individual = TRUE;
    public $serialNumber = false;
    public $idOwner = false;
    public $auditType = false;
    public $change;
    public $auditTypeName = "";
    public $intervalHistory = "";
    public $idUser;
    public $userType;
    public $startDate;
    public $endDate;
    public $idPb = FALSE;
    public $hoursOperationArray;
  
    public function __construct($USERID, $USERTYPE) {
        parent::__construct();

        $this->createModel('App_booths');
        $this->createModel('CLD_boothTypes');
        $this->createModel('App_sessions');
        $this->createModel('Repdc');
        $this->createModel('rentals');

       $this->idUser = $USERID;
//        $this->userType = $USERTYPE;
       if (isset($_POST['idOwner'])){
            $this->idUser = $_POST['idOwner'];
            $this->idOwner = $_POST['idOwner'];
            $this->superUser = 1;
        }
        $this->UserType = 1;
        $this->indexAction();
    }

    public function indexAction() {
        $cName = $_POST['cName'];
        $uName = $_POST['uName'];
        $result = array('success' => FALSE, 'message' => 'Unkown Error');

        $this->clearVars();
        $this->checkPost();
        utils::log($this->action, "logasd");
        switch ($this->action) {
            case 'getPBInfo':
                $this->checkFormPost();

                $message = $this->getPbInfo();
                $success = $this->idPb;
                break;

            case 'getOwnerPBs':
                $this->checkAutocompleteParams();
//                list($success, $message) = $this->getOwnerPBs( $this->serialNumber);
                list($success, $message) = $this->getOwnerPBs($this->serialNumber);
                if ($success) {
                    $result = array();
                    $i = 0;
                    foreach ($message as $pb) {
                        $result[$i]['value'] = $pb["serialnumber"];
                        $result[$i]['label'] = $pb["serialnumber"];
                        $result[$i]['owner'] = $pb["owner"];
                        $i++;
                    }

                    $message = $result;
                }
                break;

            case 'getAuditAndHistory':
                $this->idPb = $_POST['idPb'];
                $this->intervalHistory = $_POST['intervalHistory'];
                $this->numhistory = intval($_POST['numhistory']);
                $this->change = $_POST['change'];
         
                $html = $this->getAllAudit($this->numhistory);

                $success = TRUE;
                $message = $html;
                break;

            case 'getAudit':
                $idPb = $_POST['idPb'];
                $intervalStart = $_POST['startDate'];
                $intervalEnd = $_POST['endDate'];
                $AuditsNum = $_POST['AuditsNum'];

                $html = $this->getAudit($idPb, $intervalStart, $intervalEnd, $AuditsNum);

                $success = TRUE;
                $message = $html;
                break;

            case 'sendAuditMail':
                $idPb = $_POST['idPb'];
                $intervalStart = $_POST['startDate'];
                $intervalEnd = $_POST['endDate'];
                $AuditsNum = $_POST['AuditsNum'];

                list($success, $message) = $this->sendAuditMail($idPb, $intervalStart, $intervalEnd, $AuditsNum);
                break;

            case 'getAuditView':
            default:
                $this->checkFormPost();
                
                $this->individual = TRUE;
                $this->auditType = RepdcManager::WEEKLY;
                $_SESSION['auditType'] = $this->auditType;
                
                list($success, $message) = $this->getAuditsView();
                
                break;
        }
        
        $result['success'] = $success;
        $result['message'] = $message;
        echo json_encode($result);
    }

    private function clearVars() {
        $this->action = null;

        $this->individual = TRUE;
        $this->serialNumber = false;
        $this->auditType = false;
    }

    private function checkPost() {
        if (isset($_GET['a']))
            $this->action = $_GET['a'];

        if (isset($_POST['auditType'])) {
            $_SESSION['auditType'] = $_POST['auditType'];
        }
        $this->auditType = $_SESSION['auditType'];

        if (isset($_POST['individual'])) {
            if ($_POST['individual'] == 'false') {
                $this->individual = FALSE;
            } else {
                $this->individual = TRUE;
            }
        } else {
            $this->individual = TRUE;
        }
    }

    private function checkFormPost() {
        if (isset($_POST['serialNumber']))
            $this->serialNumber = $_POST['serialNumber'];
        if (isset($_POST['idPb'])){
            $this->idPb = $_POST['idPb'];
        }
        if (isset($_POST['idOwner'])){
            $this->idOwner = $_POST['idOwner'];
            $this->idUser = $_POST['idOwner'];
            //TODO: cal?? eloi
//            $idPb = $this->RepdcModel->getOwnerPb($this->idUser);
//            $this->idPb = $idPb;
        }
//        else{
//            $idPb = $this->RepdcModel->getOwnerPb($this->idUser);
//            $this->idPb = $idPb;
//        }
    }

    private function checkAutocompleteParams() {
        if (isset($_POST['serialNumber']))
            $this->serialNumber = $_POST['serialNumber'];
    }

    private function checkSerialNumberFormat($serialNumber) {
        $result = TRUE;
        $message = "";

        $count = strlen($serialNumber);
        if ($count < 9) {
            $result = FALSE;
            $message = "Invalid Serial Number format!";
        }

        return array($result, $message);
    }

    private function validateParams() {
        $result = FALSE;
        $message = "";

        if ($this->individual !== FALSE) {
            if ($this->serialNumber == "" && $this->auditType == false) {
                $message = "Fill the form first!";
            } else if ($this->serialNumber == "" && $this->idPb === FALSE) {
                $message = "Insert a Serial Number or select the global option!";
            } else if ($this->auditType == null) {
                $message = "Select an auditType";
            } else {
                list($result, $message) = $this->checkSerialNumberFormat($this->serialNumber);
            }
        } else {
            if ($this->auditType == null) {
                $message = "Select an auditType";
            } else {
                $result = TRUE;
            }
        }

        return array($result, $message);
    }

    /**
     * Metode per a buscar els PBs d'un owner.
     * 
     * @param Integer $idOwner Id de l'owner que es vol buscar el PB
     * @param  String $serialNumber Default string buit, total o parcial per a fer la cerca.
     * @return Array Array amb tots els resultats ordenats per serialNumber.
     */
//    private function getOwnerPBs($idOwner, $serialNumber = "") {
    private function getOwnerPBs($serialNumber = "",$owner="") {
        $result = FALSE;
        $message = array();

        if ($serialNumber == "") {
            $serialNumber = FALSE;
        }
        if ($owner == "") {
            $owner = FALSE;
        }

//        $pbs_array = $this->App_boothsModel->getPbsListFilter($serialNumber, FALSE, FALSE, FALSE, FALSE, array($idOwner));
        $pbs_array = $this->App_boothsModel->getPbsListFilterAuditsSuperWithDongle($serialNumber, FALSE, FALSE, FALSE, FALSE,$owner);
//        print count($pbs_array);
//        print_r($pbs_array);exit;
        
        if (is_array($pbs_array) && count($pbs_array) > 0) {
            $result = TRUE;
            $message = $pbs_array;
        }

        return array($result, $message);
    }

    /**
     * Metode per a agafar la info d'un sol PB
     * 
     * @param String $serialNumber
     * @return Array [result, message], if result is false, the error message, if true, a PB entity, and an additional parameter called typeName
     */
    private function getInfoPB($serialNumber = "") {
        $result = FALSE;
        $message = "No results for this serialNumber";

        $Pb = $this->App_boothsModel->getBoothID($this->idPb);
        if (is_array($Pb) && count($Pb) > 0) {
            $typeName = $this->CLD_boothTypesModel->getBoothTypeName($this->entity->getValue('CLD_idType'));
            $Pb[0]['typeName'] = $typeName[0]['name'];

            $result = TRUE;
            $message = $Pb;
        }

        return array($result, $message);
    }

    /**
     * Metode per saber la primera data de l'enviament
     * 
     * @param Integer $idPb
     * @return DateTime Object containing the first sending Date
     */
    private function getFirstSendingDate($idPb) {
        $startDate = FALSE;
        $this->startDate = FALSE;

        if ($this->individual !== FALSE) {
            if ($idPb !== FALSE) {
                $startDate = $this->RepdcModel->getFirstApp_info($idPb);
                if($startDate){
                    $this->startDate = $startDate->format('m/d/Y');
                }
            }
        } else {
            $startDate = $this->RepdcModel->getFirstOwnerPbsLastConnection($this->idUser);
            if($startDate){
                $this->startDate = $startDate->format('m/d/Y');
            }
        }

        return $startDate;
    }

    /**
     * Metode per saber l'ultima data de l'enviament
     * 
     * @param Integer $idPb
     * @return DateTime Object containing the last sending Date
     */
    private function getLastSendingDate($idPb) {
        $date = new DateTime();
        $dateSend = FALSE;

        if ($idPb !== FALSE) {
            $dateSend = clone($date);
            $todayDayNumber = $date->format('w');
            $this->endDate = $date->format('m/d/Y');
        }

        return $dateSend;
    }

    /**
     * Metode per saber el weekly audit history d'un PB
     * 
     * @param Integer $idPb
     * @return Array Amb el format ['startDate', 'endDate', 'auditName']
     */
    private function getWeeklyHistory($idPb = false) {
        $result = array();

        if(!$this->individual) $idPb = FALSE;
        
        $firstSendingDate = DateTime::createFromFormat('m/d/Y', $this->startDate);
        if ($firstSendingDate !== FALSE) {
            $lastSendingDate = DateTime::createFromFormat('m/d/Y', $this->endDate);
            $lastSendingDate->modify('next sunday');
            $lastSendingDate->setTime(23,59,59);

            $auditsCounter = $this->RepdcModel->getAuditsStatus($firstSendingDate, $lastSendingDate, $this->idUser, $idPb);
            $lastInfo = \DateTime::createFromFormat('Y-m-d', $auditsCounter[0]['when_']);
            
            $decreasingDate = clone($lastSendingDate);
            $decreasingDate->setTime(0,0,0);
            $lastDecreasingDate = clone($lastSendingDate);
            while ($decreasingDate > $firstSendingDate) {
                $endDate = $decreasingDate->format('m/d/Y');
                $weekNumberEnd = $decreasingDate->format('W');

                $decreasingDate->modify('-6 days');

                $startDate = $decreasingDate->format('m/d/Y');
                $weekNumberStart = $decreasingDate->format('W');
                $weekYear = $decreasingDate->format('Y');

                $status = 'emptyHistory';
                if($lastInfo){
                    if($lastInfo > $decreasingDate && $lastInfo < $lastDecreasingDate){
                        $status = 'partialHistory';
                    } else if($lastInfo < $decreasingDate) {
                        $status = 'emptyHistory';
                    } else {
                        $status = 'completeHistory';
                    }
                    
                    $lastDecreasingDate = clone($decreasingDate);
                    $lastDecreasingDate->setTime(23,59,59);
                    $lastDecreasingDate->modify('-1 days');
                }
                
                $interval = array('startDate' => $startDate, 'endDate' => $endDate, 'number' => $weekNumberStart, 'year' => $weekYear, 'status' => $status);
                array_push($result, $interval);

                $decreasingDate->modify('-1 day');
            }
        }

        return $result;
    }

    /**
     * Metode per saber el monthly audit history d'un PB
     * 
     * @param Integer $idPb
     * @return Array Amb el format ['startDate', 'endDate', 'auditName']
     */
    private function getMonthlyHistory($idPb = false) {
        $result = array();
        
        if(!$this->individual) $idPb = FALSE;

        $firstSendingDate = DateTime::createFromFormat('m/d/Y', $this->startDate);
        if ($firstSendingDate !== FALSE) {
            $firstSendingDate->modify('first day of this month');
            $lastSendingDate = DateTime::createFromFormat('m/d/Y', $this->endDate);
            $lastSendingDate->setTime(23,59,59);
            
            $auditsCounter = $this->RepdcModel->getAuditsStatus($firstSendingDate, $lastSendingDate, $this->idUser, $idPb);
            $lastInfo = \DateTime::createFromFormat('Y-m-d', $auditsCounter[0]['when_']);
            
            $decreasingDate = clone($lastSendingDate);
            $decreasingDate->setTime(0,0,0);
            $lastDecreasingDate = clone($lastSendingDate);
            while ($decreasingDate > $firstSendingDate) {
                $endDate = $decreasingDate->format('m/d/Y');
                $monthNumberEnd = $decreasingDate->format('n');

                $decreasingDate->modify('first day of this month');

                $startDate = $decreasingDate->format('m/d/Y');
                $monthNumberStart = $decreasingDate->format('n');
                $monthYear = $decreasingDate->format('Y');

                if($lastInfo){
                    if($lastInfo > $decreasingDate && $lastInfo < $lastDecreasingDate){
                        $status = 'partialHistory';
                    } else if($lastInfo < $decreasingDate) {
                        $status = 'emptyHistory';
                    } else {
                        $status = 'completeHistory';
                    }
                    
                    $lastDecreasingDate = clone($decreasingDate);
                    $lastDecreasingDate->setTime(23,59,59);
                    $lastDecreasingDate->modify('-1 day');
                }
                
                
                $interval = array('startDate' => $startDate, 'endDate' => $endDate, 'number' => $monthNumberStart, 'year' => $monthYear, 'status' => $status);
                array_push($result, $interval);

                $decreasingDate->modify('-1 day');
            }
        }

        return $result;
    }

    /**
     * Metode per saber el yearly audit history d'un PB
     * 
     * @param Integer $idPb
     * @return Array Amb el format ['startDate', 'endDate', 'auditName']
     */
    private function getYearHistory($idPb = false) {
        $result = array();

        if(!$this->individual) $idPb = FALSE;

//        $firstSendingDate = $this->getFirstSendingDate($idPb);
        $firstSendingDate = DateTime::createFromFormat('m/d/Y', $this->startDate);
        if ($firstSendingDate !== FALSE) {
            $firstSendingDate->modify('first day of January this year');
            
            $lastSendingDate = DateTime::createFromFormat('m/d/Y', $this->endDate);
            $lastSendingDate->setTime(23,59,59);

            $auditsCounter = $this->RepdcModel->getAuditsStatus($firstSendingDate, $lastSendingDate, $this->idUser, $idPb);
            $lastInfo = \DateTime::createFromFormat('Y-m-d', $auditsCounter[0]['when_']);
            
            $decreasingDate = clone($lastSendingDate);
            $decreasingDate->setTime(0,0,0);
            $lastDecreasingDate = clone($lastSendingDate);

            while ($decreasingDate >= $firstSendingDate) {
                $endDate = $decreasingDate->format('m/d/Y');

                $decreasingDate->modify('first day of January this year');

                $startDate = $decreasingDate->format('m/d/Y');
                $yearNumber = $decreasingDate->format('Y');

                if($lastInfo){
                    if($lastInfo > $decreasingDate && $lastInfo < $lastDecreasingDate){
                        $status = 'partialHistory';
                    } else if($lastInfo < $decreasingDate) {
                        $status = 'emptyHistory';
                    } else {
                        $status = 'completeHistory';
                    }
                    
                    $lastDecreasingDate = clone($decreasingDate);
                    $lastDecreasingDate->setTime(23,59,59);
                    $lastDecreasingDate->modify('-1 day');
                }
                
                $interval = array('startDate' => $startDate, 'endDate' => $endDate, 'number' => $yearNumber, 'year' => $yearNumber, 'status' => $status);
                array_push($result, $interval);

                $decreasingDate->modify('-1 day');
            }
        }

        return $result;
    }

    private function setAuditsHeader() {
        $html = <<<HTML
            <link href='sections/audits/resources/css/auditsList.css' rel='stylesheet' />
            <script src='sections/audits/resources/js/auditsList.js'></script>
            <link rel="stylesheet" href="assets/libraries/font-awesome-4.7.0/css/font-awesome.min.css"> 
HTML;

        return $html;
    }

    private function getAuditsView() {
        /*******************************************************
         * alguna cosa així
         *  */
        if(isset($_POST['idOwner'])){        
            $idOwner = $_POST['idOwner']; 
            $this->idOwner = $_POST['idOwner']; 
            $this->idUser = $_POST['idOwner']; 
        }
        else{
        $idOwner = ""; 
        $this->idOwner = "";
        }    
        if(isset($_POST['nameOwner'])){        
        $nameOwner = $_POST['nameOwner']; 
        $allString = "";
        }
        else{
        $nameOwner = "";   
        $allString = "All Owners - All PBs";
        }  
        
        $html = "";
        
      
        
        //To enter directly to the first PB
        if ($this->individual !== FALSE) {
            if ($this->idPb == FALSE) {
                            
                
                $Pb = $this->RepdcModel->getOwnerPb($this->idOwner);
                
                $this->idPb = $Pb[0]['idBooth'];
                $this->serialNumber = $Pb[0]['serialnumber'];
            }
        }
        
        list($result, $html) = $this->validateParams();
        
        if ($result) {
            
                  
            
            $html .= $this->setAuditsHeader();
            $PbInfo = $this->getPbInfo();
            list($PbListHtml, $PbList) = $this->getPbList();
            $countPBs = count($PbList);
            
            
            $allAudit = $this->getAllAudit();
            if($this->idUser){
                $AllPbs = '<button type="button" class="popup-confirm selectedBtn" id="allPbs">All Pbs</button>';
            }
            
            $html .= <<<HTML
                <div id="audits_wrap">
                <div id="primer">
                    <div id="info">
                        {$PbInfo}
                        
                    </div>
                    <div id="pbList">
                        <div id="pbList_title">
                            <h5>PB List - $nameOwner $allString ($countPBs PBs)</h5>
                        </div>
                         <div id="idOwnerList" class="hidden">{$this->idUser}</div>
                        <div id="contentFilterPb">
                            <input class='serialNumberInput' type="text" name="serialNumber" placeholder='Filter by PB S/N or String' value="" autocomplete="off" autofocus>
                         {$AllPbs}
                        </div>
                        <div id="pbListTable">
                            {$PbListHtml}
                        </div>
                    </div>
                </div>
                <div id="segon">
                    {$allAudit}
                </div>
            </div>
HTML;

            $result = TRUE;
        }

        return array($result, $html);
    }
    

    private function getPbInfo() {
        $web_url = G_PAGE;
        
        if ($this->individual !== FALSE) { 
            utils::log("Trace 2.2.4.1", "logFerran");
            $imageType = 'images/web/pb/no-machine.png';
            $pbInfo = $this->getInfoPB($this->serialNumber);
            utils::log("Trace 2.2.4.2", "logFerran");
            if ($pbInfo[0] !== FALSE) {
                $this->idPb = $pbInfo[1][0]['idBooth'];
                $this->idOwner = $pbInfo[1][0]['owner'];
              
                $serialnumber = $pbInfo[1][0]['serialnumber'];
                $pbName = $pbInfo[1][0]['name'];
                $typeId = $pbInfo[1][0]['CLD_idType'];
                $typeName = $pbInfo[1][0]['typeName'];
                $version = $pbInfo[1][0]['version'];
                
                $RepdcManager = new RepdcManager($this->idUser, $this->userType);
                
                //Volem mes dades
                $SinglePBInfo = $RepdcManager->getSinglePBInfo($this->idPb, $this->idUser);
                $rand_string = $SinglePBInfo['rand_string'];
                
                $lastConnLocal = $RepdcManager->getPbLastConnectionLocal_appBooths($this->idPb);            
                
                
                
                $lastConnection = $RepdcManager->getLastCon($this->idPb);
                if($lastConnLocal){
                    $lastConnLocal = DateTime::createFromFormat('Y-m-d H:i:s', $lastConnLocal);
                    $lastConnection = $lastConnLocal->format('m/d/Y H:i:s');
                }
                
                $imageTypeAbs = G_PATH . "images/web/pb/{$typeId}.png";
                if (file_exists($imageTypeAbs)) {
                    $imageType = "images/web/pb/{$typeId}.png";
                }

                $info_title = "{$serialnumber}";
                $info_text = <<<HTML
                        <div class="label">Photobooth ID:</div>
                        <div id="idPb" class="value">{$this->idPb}</div>
                        <div class="label">String:</div>
                        <div id="idPb" class="value">{$rand_string}</div>   
                        <div class="label">Owner ID:</div>                        
                        <div id="idOwner" class="value">{$this->idOwner}</div>
                        
                        <div class="label">Photobooth Name:</div>
                        <div class="value">{$pbName}</div>
                        
                        <div class="label">Photobooth Model:</div>
                        <div class="value">{$typeName}</div>
                        
                        <div class="label">Current Software Ver:</div>
                        <div class="value">{$version}</div>
                        <div class="label">Last PB connection:</div>
                        <div class="value">{$lastConnection}</div>
HTML;
            } else {
                $imageType = 'images/web/pb/no-machine.png';
                $info_title = "No results for this SerialNumber";
                $info_text = "<div id='idPb'></div>";
            }
        } else {
            $imageType = 'images/web/pb/all-pbs.png';
            $info_title = "GLOBAL ";
            $info_text = "<div id='idPb'></div><div id='idOwner'>$this->idOwner</div>";
        }

        $html .= <<<HTML
            <div id="info_img">
                <img id="pbsImg" src="{$web_url}{$imageType}">
            </div>
            <div id="info_text">
                <p class='info_title'>
                    {$info_title}
                </p>
                {$info_text}
            </div>
HTML;

        return $html;
    }

    private function getPbList() {
//        list($result, $PbList) = $this->getOwnerPBs($this->idUser);
       // list($result, $PbList) = $this->getOwnerPBs();//parametres posibles: serialnumber, idOwner
        list($result, $PbList) = $this->getOwnerPBs("",$this->idOwner);

        $html = "";

        foreach ($PbList as $pb) { //
            $selected = '';
            $separator = '';

            if (($pb["serialnumber"] != NULL && $pb["serialnumber"] != "") && ($pb['name'] != NULL && $pb['name'] != "")) {
                $separator = "/";
            }

            $serialNumberHtml = "<span style='font-size: 8.8pt!important;'>{$pb['serialnumber']}</span>";

            if ($pb["serialnumber"] == $this->serialNumber && $pb["serialnumber"] != "") {
                $selected = 'selected';
            }

            $html .= <<<HTML
                <div class='row {$selected}' idOwner='{$pb['owner']}' idPb='{$pb['idBooth']}' serialNumber='{$pb['serialnumber']}' rand_string='{$pb['rand_string']}'  individual='true'>               
                    <p class='text'>
                        {$pb['name']} {$separator} {$serialNumberHtml}<br/>
                    </p>
                </div>
HTML;
        }
        return array($html, $PbList);
//        $this->idUser = $pb['owner']; 
        //return $html;
    }

    /**
     * Method to calculate if all info is from the same SW version. If not, it will return the lower version. 
     * If no version registered, False.
     * 
     * @param type $array_info
     * @return boolean
     */
    private function checkPBVersion($array_info) {
        $version = FALSE;
        
        $min_version = FALSE;
        foreach($array_info as $single_info){
            if(isset($single_info['PBnew'])){
                if($min_version !== FALSE){
                    if($min_version > $single_info['PBnew'] && $min_version != NULL){
                        $min_version = $single_info['PBnew'];
                    }
                } else {
                    $min_version = $single_info['PBnew'];
                }
            }
        }
        
        if($min_version != NULL){
            $version = $min_version;
        }
        
        return $version;
    }
    
    private function buildAlertMessage($type, $message) {
        $html = '';
        
        switch($type){
            case 'info':
                $html = "<div class='inputErrorContent alertMessage-info'>{$message}</div>";
                break;
            case 'danger':
            default:
                $html = "<div class='inputErrorContent alertMessage-error'>{$message}</div>";
                break;
        }
        
        return $html;
    }
    
    private function getAudit($idPb, $intervalStart = FALSE, $intervalEnd = FALSE, $AuditNum = FALSE) {
        $showSendBtn = TRUE;
        $error_1 = "";
        $error_1 = "";
                
        if ($intervalStart && $intervalEnd) {
            if (isset($_POST['idOwner']) && !is_nan($_POST['idOwner'])){
                $this->idUser = $_POST['idOwner'];
                $this->idOwner = $_POST['idOwner'];
                $this->superUser = 1;
            }
            $RepdcManager = new RepdcManager($this->idUser, $this->userType);

            if ($this->individual === FALSE) {
                $idPbString = 'GLOBAL';
                $idPb = FALSE;
                $showSendBtn = FALSE;
                
                list($hasData, $hasOlderData, $PBLastConnection) = $RepdcManager->checkReportIntegrityByOwner($intervalStart, $intervalEnd);
            } else {
                $idPbString = $idPb;
                list($hasData, $hasOlderData, $PBLastConnection) = $RepdcManager->checkReportIntegrityByPb($idPb, $intervalStart, $intervalEnd);
            }

            if ($hasData) {
                if ($hasData && !$hasOlderData) {
                    //Maybe no data to show
                    $dateString = ($PBLastConnection !== FALSE) ? "since {$PBLastConnection->format('m/d/Y H:i:s')}" : "";
                    $error_2 .= $this->buildAlertMessage('danger', "**Partially data received from the photobooth**<br/> This audit may be incomplete, because no data received {$dateString}");
                }
                
                $array_info = $RepdcManager->getSummaryByDayInfo($idPb, $intervalStart, $intervalEnd, $this->auditType);
                
                $startDateTime = DateTime::createFromFormat('m/d/Y H:i:s', $intervalStart . ' 00:00:00');
                $endDateTime = DateTime::createFromFormat('m/d/Y H:i:s', $intervalEnd . ' 00:00:00');
                
//                utils::log($array_info, 'logAleix');
                $minPBversion = $this->checkPBVersion($array_info);
//                $minPBversion = 21;
//                utils::log("minVersion = {$minPBversion}", 'logAleix');
                
                if(!$minPBversion || $minPBversion < 23){
                    $error_1 .= $this->buildAlertMessage('info', "The information may not be completely accurate until you install Britta v3.1.6 version.");                    
                }
                
                $translateClass = 'lessTranslate';                
                if($minPBversion >= 21){
                    $title = "title='Only available if PB version >= Britta v2.2'";
                    $this->hoursOperationArray = $RepdcManager->getHoursOperation($idPb, $intervalStart, $intervalEnd, $this->auditType);

                    $translateClass = 'moreTranslate';
                    $hoursOperation_label = "<td class='rotate borderLeft info' style='height: 127px;' {$title}><div><span>Hours Operation*</div></span></td>";
                    $overpayment_label = "<td class='rotate borderRight info' style='padding-bottom: 0px;' {$title}><div><span>Overpayment*</div></span></td>";
                }
                
                $stock_label = "";
                $collection_label = "";
                if($idPb){
                    $stock_label = "<td class='rotate borderRight'><div><span>Stock</div></span></td>";
                    $collection_label = "<td class='no-color td-small'></td><td class='rotate borderRight'><div><span>Collection</div></span></td>";
                }
               
                
                $twilio_label ='<td class="rotate"><div><span>Whats Cost</div></span></td><td class="rotate"><div><span>SMS Cost</div></span></td><td class="rotate borderRight totalization"><div><span>Total Cost</div></span></td>';      
                
                $html .= $error_1;
                $html .= $error_2;
                //Completed
                $html .= <<<HTML
                        <table class="reportTable">
                            <tr class="labelsTable {$translateClass}">
                                <td class='no-color'></td>
                                {$hoursOperation_label}
                                <td class="rotate borderLeft"><div><span>Plays</div></span></td>
                                <td class="rotate"><div><span>FreePlays</div></span></td>
                                <td class="rotate"><div><span>Upsells</div></span></td>
                                <td class="rotate"><div><span>Passport</div></span></td>
                                <td class="rotate"><div><span>SmartPrint</div></span></td>
                                <td class="rotate borderRight totalization"><div><span>Prints</div></span></td>
                                <td class="rotate"><div><span>Cash</div></span></td>
                                <td class="rotate"><div><span>CreditCard</div></span></td>
                                <td class="rotate"><div><span>Net</div></span></td>
                                <td class="rotate borderRight totalization"><div><span>Money</div></span></td>
                                {$overpayment_label}
                                {$stock_label}
                                {$collection_label}
                                {$twilio_label}
                                
                            </tr>
HTML;
                
                switch ($this->auditType) {
                    case RepdcManager::WEEKLY:
                        $html .= $this->getSumaryWeeklyReport($startDateTime, $endDateTime, $array_info, $idPb, $minPBversion);
                        break;
                    case RepdcManager::MONTHLY:
                        $html .= $this->getSumaryMonthlyReport($startDateTime, $endDateTime, $array_info, $idPb, $minPBversion);
                        break;
                    case RepdcManager::YEARLY:
                        $html .= $this->getSumaryYearReport($startDateTime, $endDateTime, $array_info, $idPb, $minPBversion);
                        break;
                }
                
                $html .= "</table>";
            } 
            else {
                //No data for sure
                $dateString = ($PBLastConnection !== FALSE) ? "since {$PBLastConnection->format('m/d/Y H:i:s')}" : "";
                $html .= $this->buildAlertMessage('danger', "**No data received from the photobooth**<br/> Audit not available because no data received {$dateString}");
            }
        } else {
            $html = $this->buildAlertMessage('danger', "This Photobooth has no registered data for this dates");
        }
        if ($showSendBtn ) {
            $html .= "<div id='bottom'>";
            $html .= "<button id='sendAuditMail' class='popup-confirm' idPb='{$idPb}' stdate='{$intervalStart}' endate='{$intervalEnd}' aunum='{$AuditNum}'><i class='fa fa-paper-plane-o icoSend' aria-hidden='true'></i> Resend E-mail </button>";
            $html .= "<img id='gifSendingMail' src='images/web/loading.gif' style='width: 8%!important; margin-top: 0px!important;'>";
            $html .= "</div>";
        }
        return $html;
    }

    private function getAuditList() {
        $history = $this->getAuditTypeHistory();

        $i = 0;
        $html = "";
        $selected = ($this->intervalHistory) ? '' : 'selected';
        $individualStr = ($this->individual) ? 'true' : 'false';
        $historyStatus = 'emptyHistory';
//        $historyStatus = 'partialHistory';
//        $historyStatus = 'completeHistory';
        
        foreach ($history as $audit) {
            $startDate = $audit['startDate'];
            $endDate = $audit['endDate'];
            $number = $audit['number'];
            $year = $audit['year'];
            
            if(isset($audit['status'])){
                $historyStatus = $audit['status'];
            }
            
            if ($this->change == "false") {
                if (strval($year . $number) == $this->intervalHistory) {
                    $selected = 'selected';
                }
            } else {
                if ($i == 0) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
            }
            
            $html .= "<div id='{$year}{$number}' class='row {$selected} {$historyStatus}' stDate='{$startDate}' enDate='{$endDate}' aunum='{$number}' year='{$year}' num='{$i}' individual='{$individualStr}'>";
            $html .= "<i class='fa fa-file-text-o' aria-hidden='true'></i>";
            $html .= "<div class='text'>{$startDate} - {$endDate} #{$number}</div>";
            $html .= "</div>";

            $i++;
            $selected = "";
        }

        return $html;
    }

    private function getAuditInfo($auditInfo = FALSE) {
        $html = "";

        $weeklySelected = "";
        $monthlySelected = "";
        $yearSelected = "";


        switch ($this->auditType) {
            case RepdcManager::WEEKLY:
                $weeklySelected = "selected";
                break;
            case RepdcManager::MONTHLY:
                $monthlySelected = "selected";
                break;
            case RepdcManager::YEARLY:
                $yearSelected = "selected";
                break;
        }

        if ($auditInfo) {
            $title = "{$auditInfo['startDate']} - {$auditInfo['endDate']} #{$auditInfo['number']}";
        } else {
            $title = "ND";
        }

        $weekly = RepdcManager::WEEKLY;
        $monthly = RepdcManager::MONTHLY;
        $year = RepdcManager::YEARLY;

        $html .= <<<HTML
            <div id="div_pre">
                <div id="pre"> 
                    <i class="myArrow fa fa-arrow-left" aria-hidden="true"></i>
                </div>
            </div>
            <div id="div_title">
                 <div id="title">
                    <select id="auditSelector">
                        <option value="{$weekly}" {$weeklySelected}>WEEKLY Report</option>
                        <option value="{$monthly}" {$monthlySelected}>MONTHLY Report</option>
                        <option value="{$year}" {$yearSelected}>YEAR Report</option>
                    </select>     
                 </div>
                 <div id="interval">
                     <input historyid="{$auditInfo['year']}{$auditInfo['number']}" id="inputInterval" type='text' value='{$title}' disabled/>
                 </div>
             </div>
             <div id="div_next">
                <div id="next">
                     <i class="myArrow fa fa-arrow-right" aria-hidden="true"></i>
                </div>
             </div>           
HTML;

        return $html;
    }

    private function getAuditTypeHistory() {
        $history = FALSE;
        switch ($this->auditType) {
            case RepdcManager::WEEKLY:
                $history = $this->getWeeklyHistory($this->idPb);
                break;
            case RepdcManager::MONTHLY:
                $history = $this->getMonthlyHistory($this->idPb);
                break;
            case RepdcManager::YEARLY:
                $history = $this->getYearHistory($this->idPb);
                break;
        }

        return $history;
    }

    private function historyTitle() {
        return <<<HTML
            <div id="history_title">
            <h5>History</h5>
            </div>    
HTML;
    }

    private function getAllAudit($n = 0) {
        $AuditInfo = "";
        $Audit = "";
        $AuditList = "";

        $this->getFirstSendingDate($this->idPb);
        $this->getLastSendingDate($this->idPb);

        //check if PB has History
        if ($this->startDate !== FALSE) {
            //function get AuditType History
            $history = $this->getAuditTypeHistory();
            $intervalStart = $history[$n]['startDate'];
            $intervalEnd = $history[$n]['endDate'];

            $AuditInfo = $this->getAuditInfo($history[$n]);
            $Audit = $this->getAudit($this->idPb, $intervalStart, $intervalEnd, $history[$n]['number']);
            $historyTitle = $this->historyTitle();
            $AuditList = $this->getAuditList();
        } else {
            $Audit = "<div id='inputErrorContent'>The photobooth had no activity</div>";
        }

        return <<<HTML
            <div id="audits_wrap_content">
                <div id="title_wrap">
                    {$AuditInfo}
                </div>
                <div id="audit">
                    {$Audit}
                </div>
            </div>
            <div id="history">
                {$historyTitle}
                <div id="historyTable">
                    <p>{$AuditList}</p>
                </div>
            </div>
HTML;
    }

    private function sendAuditMail($idPb, $intervalStart, $intervalEnd, $AuditsNum) {
        $RepdcManager = new RepdcManager($this->idUser, $this->userType);
        
        ob_start();
        switch ($this->auditType) {
            case RepdcManager::WEEKLY:
                $success = $RepdcManager->sendSingleWeeklyReport($idPb, $intervalStart, $intervalEnd);
                break;
            case RepdcManager::MONTHLY:
                $success = $RepdcManager->sendSingleMonthlyReport($idPb, $intervalStart, $intervalEnd);
                break;
            case RepdcManager::YEARLY:
                $success = $RepdcManager->sendSingleYearReport($idPb, $intervalStart, $intervalEnd);
                break;
        }

        $messageLog = ob_get_contents();
        ob_clean();

        if ($success === FALSE) {
            $message = "Something wrong happened, please try to resend the audit later.";
        } else {
            $message = "Audit sent, check the inbox of your alert-email.";
        }

        return array($success, $message);
    }

    private function getSumaryWeeklyReport($startDateTime, $endDateTime, $array_info, $idPb, $minPBversion) {
        $html = "";
        $i = 1;
        $z = 0;
        $currencyChange = 0;
        
        $auditsCounter = $this->RepdcModel->getAuditLastConection($this->idUser, $idPb);
        $lastInfo = \DateTime::createFromFormat('Y-m-d', $auditsCounter);
               
        $totalPrints         = 0;
        $totalPlays          = 0;
        $totalFreePlays      = 0;
        $totalUpsells        = 0;
        $totalpassport       = 0;
        $totalCash           = 0;
        $totalCreditCard     = 0;
        $totalNet            = 0;
        $totalmoney          = 0;
        $totalOverpayment    = 0;
        $totalHoursOperation = 0;
        $totalMinutesOperation = 0;
        $totalTimeOperation  = "0:00";
        $totalCostWhats      = 0;
        $totalCostSMS        = 0;
        $totalCostTwilio     = 0;
        
        while ($startDateTime <= $endDateTime) {
//            $day = $startDateTime->format('l m/d/Y');
//            $day = $startDateTime->format('r');
            $day = $startDateTime->format('D, j M Y');
            $day_ = $startDateTime->format('Y-m-d');
            
            //La maquina podria haver estat encesa i no tenir activitat igualment
            $hoursOperation = "-";
            $minutesOperation = "-";
            $hoursOperation_time = "-";
            if(isset($this->hoursOperationArray)){
                $hoursOperation = "0";
                $minutesOperation = "0";
                $hoursOperation_time = "0:00";
                if(isset($this->hoursOperationArray[$day_])){
                    $hoursOperation = $this->hoursOperationArray[$day_]['hours'];
                    $minutesOperation = $this->hoursOperationArray[$day_]['minutes'];
                    $hoursOperation_time = $this->hoursOperationArray[$day_]['time'];
                }
            }
            
            list($totalHoursOperation, $totalMinutesOperation) = utils::sumHours($totalHoursOperation, $totalMinutesOperation, $hoursOperation, $minutesOperation);

//            utils::log("Hours Operation = {$hoursOperation}", "logAleix");
             
            // Overpayment and HoursOperation Rows
            $hoursOperation_empty = "";
            $overpayment_empty = "";
            $hoursOperation_unknown = "";
            $overpayment_unknown = "";
            if($minPBversion >= 21){
                $hoursOperation_empty = "<td class='borderLeft'>{$hoursOperation_time}h</td>";
                $overpayment_empty = "<td class='borderRight'>0</td>";
                $hoursOperation_unknown = "<td class='borderLeft'>-</td>";
                $overpayment_unknown = "<td class='borderRight'>-</td>";
            }

            // Stock Row
            $stock_empty = "";
            $stock_unknown = "";
            $collection_empty = "";
            $collection_unknown = "";
            if($idPb){
                $stock_empty .= "<td class='borderRight'>-</td>";
                $stock_unknown .= "<td class='borderRight'>-</td>";
                $collection_empty   .= "<td class='no-color td-small'></td><td class='borderRight'>0</td>";
                $collection_unknown .= "<td class='no-color td-small'></td><td class='borderRight'>-</td>";
            }
            
            $twilio_empty = "<td>0</td><td>0</td><td class='borderRight totalization'>0</td>";
            $twilio_unknown = "<td>-</td><td>-</td><td class='borderRight totalization'>-</td>";
            
            $empty_htmldata = <<<HTML
                {$hoursOperation_empty}
                <td class='borderLeft'>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td class='borderRight totalization'>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td class='borderRight totalization'>0</td>
                {$overpayment_empty}
                {$stock_empty}
                {$collection_empty}
                {$twilio_empty}
                
HTML;

            $unknown_htmldata = <<<HTML
                {$hoursOperation_unknown}
                <td class='borderLeft'>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td class='borderRight totalization'>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td class='borderRight totalization'>-</td>
                {$overpayment_unknown}
                {$stock_unknown}
                {$collection_unknown}
                {$twilio_unknown}
                
HTML;
            
            if($lastInfo > $startDateTime){
                $htmldata_ = $empty_htmldata;
            }
            else{
                $htmldata_ = $unknown_htmldata;
            }                
                
            $html .="<tr>";
            $html .="<td>$day</td>";
            
            if(isset($array_info) && count($array_info)>0){
                if(isset($array_info[$day_])){
                        
//                    if($array_info[$day_]['when_'] == $day_){
                    $prints      = ($array_info[$day_]['prints']) ? $array_info[$day_]['prints'] : "0";
                    $pasport = ($array_info[$day_]['pas']) ? $array_info[$day_]['pas'] : "0";
                    $SmartPrint = ($array_info[$day_]['SmartPrint']) ? $array_info[$day_]['SmartPrint'] : "0";
                    $freePlays   = ($array_info[$day_]['freePlays']) ? $array_info[$day_]['freePlays'] : "0";
//                    $freePlays = $this->calcPlaysFP($array_info[$day_]['freePlays'], intval($pasport), intval($SmartPrint));
                    $plays = $this->calcPlays($array_info[$day_]['plays'], intval($freePlays), intval($pasport), intval($SmartPrint));
                    $upsells     = ($array_info[$day_]['in4']) ? $array_info[$day_]['in4'] : '0';
                    $cash        = ($array_info[$day_]['i3']) ? $array_info[$day_]['i3'] : "0";
                    $cash_60     = ($array_info[$day_]['i3_60']) ? $array_info[$day_]['i3_60'] : "0";
                    $cash        = intval($cash) + intval($cash_60);
                    $creditCard  = ($array_info[$day_]['i4']) ? $array_info[$day_]['i4'] : "0";
                    $creditCard_60     = ($array_info[$day_]['i4_60']) ? $array_info[$day_]['i4_60'] : "0";
                    $creditCard        = intval($creditCard) + intval($creditCard_60);
                    $net         = ($array_info[$day_]['i5']) ? $array_info[$day_]['i5'] : "0";
                    $money       = ($array_info[$day_]['money_']) ? $array_info[$day_]['money_'] : "0";
                    $overpayment = (intval($cash) + intval($creditCard) + intval($net)) - intval($money);
                    utils::log('super AUDIT DAY', 'logAleix');
                    utils::log('day_:'.$day_, 'logAleix');
                    utils::log('cash_60:'.$cash_60, 'logAleix');
                    utils::log('creditCard_60:'.$creditCard_60, 'logAleix');
                    utils::log('cash:'.$money, 'logAleix');
                    utils::log('creditCard:'.$creditCard, 'logAleix');
                    utils::log('Money:'.$money, 'logAleix');
                    if($overpayment<0){
                         
                        $sumaOver = -$overpayment;
                        $overpayment = 0;
                        $creditCard = $creditCard + $sumaOver;
                    }
                    $money       = intval($cash) + intval($creditCard) + intval($net);
                    $stock       = ($array_info[$day_]['stock']) ? $array_info[$day_]['stock'] : "0";
                    $costWhats   = ($array_info[$day_]['costWhats']) ? $array_info[$day_]['costWhats'] : "0"; 
                    $costSMS     = ($array_info[$day_]['costSMS']) ? $array_info[$day_]['costSMS'] : "0"; 
                    $costTwilio  = ($array_info[$day_]['costTwilio']) ? $array_info[$day_]['costTwilio'] : "0"; 
//                    $stock = ($stock_) ? $stock_ : "-";

                    $collection         =   $array_info[$day_]['collection'];
                    $collectionNum      =   ($array_info[$day_]['collectionNum']) ? $array_info[$day_]['collectionNum'] : "0";

                    $currency = $array_info[$day_]['currency_symbol'];
                    $currency_postition =  $array_info[$day_]['currency_position'];

                    $totalPrints      += intval($prints);
                    $totalPlays       += intval($plays);
                    $totalFreePlays   += intval($freePlays);
                    $totalUpsells     += intval($upsells);
                    $totalpassport += intval($pasport);
                    $totalSmartPrint += intval($SmartPrint);
                    $totalCash        += intval($cash);
                    $totalCreditCard  += intval($creditCard);
                    $totalNet         += intval($net);
                    $totalmoney       += intval($money);
                    $totalOverpayment += intval($overpayment);
                    $totalCostWhats   += $costWhats;
                    $totalCostSMS     += $costSMS;
                    $totalCostTwilio  += $costTwilio;

                    $cash = utils::putCurrency($cash, $currency, $currency_postition);
                    $creditCard = utils::putCurrency($creditCard, $currency, $currency_postition);
                    $net = utils::putCurrency($net, $currency, $currency_postition);
                    $money = utils::putCurrency($money, $currency, $currency_postition);
                    $overpayment = utils::putCurrency($overpayment, $currency, $currency_postition);
                    $costWhats = utils::putCurrencyFloat($costWhats, '&#36;', 1);
                    $costSMS = utils::putCurrencyFloat($costSMS, '&#36;', 1);
                    $costTwilio = utils::putCurrencyFloat($costTwilio, '&#36;', 1);

                    if($z == 0){
                        $totalCurrency = $currency;
                    }
                    elseif ($currencyChange == 0) {
                        if($totalCurrency != $currency){
                            $currencyChange = 1;
                        }
                    }

                    $hoursOperation_row = "";
                    $overpayment_row = "";
                    if($minPBversion >= 21){
                        $hoursOperation_row = "<td class='borderLeft'>{$hoursOperation_time}h</td>";
                        $overpayment_row = "<td class='borderRight'>{$overpayment}</td>";
                    }

                    $stock_row = "";
                    $collection_rows = "";
                    if($idPb){                           
                        $less_stock_title_class = "";
                        $less_stock_title_text = "";
                        $less_stock_asterisk = "";

                        $collection_rows = $collection_empty;
                        if($collection){
                            utils::log('TRACE 4', 'logAleix');
//                            utils::log($array_info[$day_]['when_datetime'], 'logAleix');
//                            utils::log($array_info[$day_]['collection_time'], 'logAleix');

                            if($array_info[$day_]['when_datetime'] < $array_info[$day_]['collection_time']){
//                                utils::log('TRACE 0', 'logAleix');
                                $less_stock = FALSE;
                                $less_stock_title_class = "info";
                                $less_stock_title_text = "title='The stock may be lower due printing the collection report'";
                                $less_stock_asterisk = "*";
                            }

                            $collectionNum = "<p class='no-breakLine'>";
                            $i = 0;
                            foreach ($array_info[$day_]['collectionNum'] as $collectionNumber){
                                if($i < 2){
                                    $collectionNum .= "#{$collectionNumber} ";
                                }
                                $i++;
                            }
                            if($i > 2){
                                $otherCollections = $i-2;
                                $collectionNum .= "(<i class='fa fa-plus' style='font-size: 8pt!important;' aria-hidden='true'></i>{$otherCollections})";
                            }
                            $collectionNum .= "</p>";

                            $collection_rows = "<td class='no-color td-small'></td><td class='borderRight'>{$collectionNum}</td>";                                
                        }

                        $stock_row = "<td class='borderRight {$less_stock_title_class}' {$less_stock_title_text}>{$stock}{$less_stock_asterisk}</td>";
                    }
                    
                     $twilio_row = "<td>{$costWhats}</td><td>{$costSMS}</td><td class='borderRight totalization'>{$costTwilio}</td>";
                   


                    $htmldata_ = <<<HTML
                        {$hoursOperation_row}
                        <td class='borderLeft'>{$plays}</td>
                        <td>{$freePlays}</td>
                        <td>{$upsells}</td>
                        <td>{$pasport}</td>
                        <td>{$SmartPrint}</td>
                        <td class='borderRight totalization'>{$prints}</td>
                        <td>{$cash}</td>
                        <td>{$creditCard}</td>
                        <td>{$net}</td>
                        <td class='borderRight totalization'>{$money}</td>
                        {$overpayment_row}
                        {$stock_row}
                        {$collection_rows}
                        {$twilio_row}
HTML;

                    $z++;
//                    }
                }
            }
            
            $html .= $htmldata_;
            $html .="</tr>";
            
            
            $i++;
            $startDateTime->modify("+1 day");
        }
        
        if($currencyChange == 0){
            $totalCash = utils::putCurrency($totalCash, $currency, $currency_postition);
            $totalCreditCard = utils::putCurrency($totalCreditCard, $currency, $currency_postition);
            $totalNet = utils::putCurrency( $totalNet, $currency, $currency_postition);
            $totalmoney = utils::putCurrency($totalmoney, $currency, $currency_postition);   
            $totalOverpayment = utils::putCurrency($totalOverpayment, $currency, $currency_postition); 
            $totalCostWhats = utils::putCurrencyFloat($totalCostWhats, '&#36;', 1); //sempre ens cobren en dollar USD $ &#36;
            $totalCostSMS = utils::putCurrencyFloat($totalCostSMS, '&#36;', 1); //sempre ens cobren en dollar USD $ &#36;
            $totalCostTwilio = utils::putCurrencyFloat($totalCostTwilio, '&#36;', 1); //sempre ens cobren en dollar USD $ &#36;
            
        }
        
        $totalTimeOperation = utils::printHours($totalHoursOperation, $totalMinutesOperation);
        $totalPrints = ($totalPrints)? $totalPrints : "0";
        $totalPlays = ($totalPlays)? $totalPlays : "0";
        $totalFreePlays = ($totalFreePlays)? $totalFreePlays : "0";
        $totalUpsells = ($totalUpsells)? $totalUpsells : "0";
        $totalpassport = ($totalpassport) ? $totalpassport : "0";
        $totalSmartPrint = ($totalSmartPrint) ? $totalSmartPrint : "0";
        
        $totalHoursOperation_row = "";
        $totalOverpayment_row = "";
        if($minPBversion >= 21){
            $totalHoursOperation_row = "<td class='borderLeft'>{$totalTimeOperation}h</td>";
            $totalOverpayment_row = "<td class='borderRight'>{$totalOverpayment}</td>";
        }
        
        $TotalStock_row = "";
        $TotalCollection_rows = "";
        if($idPb){
            $TotalStock_row .= "<td class='borderRight'></td>";
            $TotalCollection_rows .= "<td class='no-color td-small'></td><td class='no-color borderRight'></td>";
        }
        
      
        $TotalTwilio_rows = "<td class='totalization'>{$totalCostWhats}</td><td class='totalization'>{$totalCostSMS}</td><td class='borderRight totalization'>{$totalCostTwilio}</td>";

        
        $html .= <<<HTML
            <tr class='RowTotal'>
                <td>Total</td>
                {$totalHoursOperation_row}
                <td class='borderLeft'>$totalPlays</td>
                <td>$totalFreePlays</td>
                <td>$totalUpsells</td>
                <td>$totalpassport</td>
                <td>$totalSmartPrint</td>
                <td class='borderRight'>$totalPrints</td>
                <td>$totalCash</td>
                <td>$totalCreditCard</td>
                <td>$totalNet</td>
                <td class='borderRight'>$totalmoney</td>
                {$totalOverpayment_row}
                {$TotalStock_row}
                {$TotalCollection_rows}
                {$TotalTwilio_rows}
            </tr>
HTML;
                
        return $html;
    }

    private function getSumaryMonthlyReport($startDateTime, $endDateTime, $array_info, $idPb, $minPBversion) {
        $html = "";
        $i = 1;
        $z = 0;
        $currencyChange = 0;
        
        $auditsCounter = $this->RepdcModel->getAuditLastConection($this->idUser, $idPb);
        $lastInfo = \DateTime::createFromFormat('Y-m-d', $auditsCounter);
        
        $totalPrints         = 0;
        $totalPlays          = 0;
        $totalFreePlays      = 0;
        $totalUpsells        = 0;
        $totalCash           = 0;
        $totalCreditCard     = 0;
        $totalNet            = 0;
        $totalmoney          = 0;
        $totalOverpayment    = 0;
        $totalHoursOperation = 0;
        $totalMinutesOperation = 0;
        $totalTimeOperation  = "0:00";
        $totalCostWhats      = 0;
        $totalCostSMS        = 0;
        $totalCostTwilio     = 0;
        
        while ($startDateTime <= $endDateTime) {
//            $day = $startDateTime->format('l m/d/Y');
//            $day = $startDateTime->format('r');
            $day = $startDateTime->format('D, j M Y');
            $day_ = $startDateTime->format('Y-m-d');
            
            //La maquina podria haver estat encesa i no tenir activitat igualment
            $hoursOperation = "-";
            $minutesOperation = "-";
            $hoursOperation_time = "-";
            if(isset($this->hoursOperationArray)){
                $hoursOperation = "0";
                $minutesOperation = "0";
                $hoursOperation_time = "0:00";
                if(isset($this->hoursOperationArray[$day_])){
                    $hoursOperation = $this->hoursOperationArray[$day_]['hours'];
                    $minutesOperation = $this->hoursOperationArray[$day_]['minutes'];
                    $hoursOperation_time = $this->hoursOperationArray[$day_]['time'];
                }
            }
            
            list($totalHoursOperation, $totalMinutesOperation) = utils::sumHours($totalHoursOperation, $totalMinutesOperation, $hoursOperation, $minutesOperation);
//            utils::log("Hours Operation = {$hoursOperation}", "logAleix");
             
            // Overpayment and HoursOperation Rows
            $hoursOperation_empty = "";
            $overpayment_empty = "";
            $hoursOperation_unknown = "";
            $overpayment_unknown = "";
            if($minPBversion >= 21){
                $hoursOperation_empty = "<td class='borderLeft'>{$hoursOperation_time}h</td>";
                $overpayment_empty = "<td class='borderRight'>0</td>";
                $hoursOperation_unknown = "<td class='borderLeft'>-</td>";
                $overpayment_unknown = "<td class='borderRight'>-</td>";
            }

            // Stock Row
            $stock_empty = "";
            $stock_unknown = "";
            $collection_empty = "";
            $collection_unknown = "";
            if($idPb){
                $stock_empty .= "<td class='borderRight'>-</td>";
                $stock_unknown .= "<td class='borderRight'>-</td>";
                $collection_empty   .= "<td class='no-color td-small'></td><td class='borderRight'>0</td>";
                $collection_unknown .= "<td class='no-color td-small'></td><td class='borderRight'>-</td>";
            }
           
            $twilio_empty = "<td>0</td><td>0</td><td class='borderRight totalization'>0</td>";
            $twilio_unknown = "<td>-</td><td>-</td><td class='borderRight totalization'>-</td>";
           

            $empty_htmldata = <<<HTML
                {$hoursOperation_empty}
                <td class='borderLeft'>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td class='borderRight totalization'>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td class='borderRight totalization'>0</td>
                {$overpayment_empty}
                {$stock_empty}
                {$collection_empty}
                {$twilio_empty}
HTML;

            $unknown_htmldata = <<<HTML
                {$hoursOperation_unknown}
                <td class='borderLeft'>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td class='borderRight totalization'>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td class='borderRight totalization'>-</td>
                {$overpayment_unknown}
                {$stock_unknown}
                {$collection_unknown}
                {$twilio_unknown}
HTML;
            
            if($lastInfo > $startDateTime){
                $htmldata_ = $empty_htmldata;
            }
            else{
                $htmldata_ = $unknown_htmldata;
            }        
            
            $html .="<tr>";
            $html .="<td>$day</td>";
            
            if(isset($array_info) && count($array_info)>0){
                if(isset($array_info[$day_])){
                    
                    $prints      = ($array_info[$day_]['prints']) ? $array_info[$day_]['prints'] : "0";
                    $pasport = ($array_info[$day_]['pas']) ? $array_info[$day_]['pas'] : "0";
                    $SmartPrint = ($array_info[$day_]['SmartPrint']) ? $array_info[$day_]['SmartPrint'] : "0";
                    $freePlays   = ($array_info[$day_]['freePlays']) ? $array_info[$day_]['freePlays'] : "0";
//                    $freePlays = $this->calcPlaysFP($array_info[$day_]['freePlays'], intval($pasport), intval($SmartPrint));
                    $plays = $this->calcPlays($array_info[$day_]['plays'], intval($freePlays), intval($pasport), intval($SmartPrint));
                    $upsells     = ($array_info[$day_]['in4']) ? $array_info[$day_]['in4'] : "0";
                    $cash        = ($array_info[$day_]['i3']) ? $array_info[$day_]['i3'] : "0";
                    $cash_60     = ($array_info[$day_]['i3_60']) ? $array_info[$day_]['i3_60'] : "0";
                    $cash        = intval($cash) + intval($cash_60);
                    $creditCard  = ($array_info[$day_]['i4']) ? $array_info[$day_]['i4'] : "0";
                    $creditCard_60     = ($array_info[$day_]['i4_60']) ? $array_info[$day_]['i4_60'] : "0";
                    $creditCard        = intval($creditCard) + intval($creditCard_60);
                    $net         = ($array_info[$day_]['i5']) ? $array_info[$day_]['i5'] : "0";
                    $money       = ($array_info[$day_]['money_']) ? $array_info[$day_]['money_'] : "0";
                    $overpayment = (intval($cash) + intval($creditCard) + intval($net)) - intval($money);
                    
                    if($overpayment<0){
                         
                        $sumaOver = -$overpayment;
                        $overpayment = 0;
                        $creditCard = $creditCard + $sumaOver;
                    }
                    $money       = intval($cash) + intval($creditCard) + intval($net);
                    $stock       = ($array_info[$day_]['stock']) ? $array_info[$day_]['stock'] : "0";
                    $costWhats   = ($array_info[$day_]['costWhats']) ? $array_info[$day_]['costWhats'] : "0"; 
                    $costSMS     = ($array_info[$day_]['costSMS']) ? $array_info[$day_]['costSMS'] : "0"; 
                    $costTwilio  = ($array_info[$day_]['costTwilio']) ? $array_info[$day_]['costTwilio'] : "0"; 
    //                    $stock = ($stock_) ? $stock_ : "-";
                    $collection         =   $array_info[$day_]['collection'];                        
                        
                    $currency = $array_info[$day_]['currency_symbol'];
                    $currency_postition =  $array_info[$day_]['currency_position'];
                    
                    $totalPrints      += intval($prints);
                    $totalPlays       += intval($plays);
                    $totalFreePlays   += intval($freePlays);
                    $totalUpsells     += intval($upsells);
                    $totalpassport    += intval($pasport);
                    $totalSmartPrint  += intval($SmartPrint);
                    $totalCash        += intval($cash);
                    $totalCreditCard  += intval($creditCard);
                    $totalNet         += intval($net);
                    $totalmoney       += intval($money);
                    $totalOverpayment += intval($overpayment);
                    $totalCostWhats   += $costWhats;
                    $totalCostSMS     += $costSMS;
                    $totalCostTwilio  += $costTwilio;
                   
                    $cash = utils::putCurrency($cash, $currency, $currency_postition);
                    $creditCard = utils::putCurrency($creditCard, $currency, $currency_postition);
                    $net = utils::putCurrency($net, $currency, $currency_postition);
                    $money = utils::putCurrency($money, $currency, $currency_postition);
                    $overpayment = utils::putCurrency($overpayment, $currency, $currency_postition);
                    $costWhats = utils::putCurrencyFloat($costWhats, '&#36;', 1);
                    $costSMS = utils::putCurrencyFloat($costSMS, '&#36;', 1);
                    $costTwilio = utils::putCurrencyFloat($costTwilio, '&#36;', 1);
                    
                    if($z == 0){
                        $totalCurrency = $currency;
                    }
                    elseif ($currencyChange == 0) {
                        if($totalCurrency != $currency){
                            $currencyChange = 1;
                        }
                    }
                    
                    $hoursOperation_row = "";
                    $overpayment_row = "";
                    if($minPBversion >= 21){
                        $hoursOperation_row = "<td class='borderLeft'>{$hoursOperation_time}h</td>";
                        $overpayment_row = "<td class='borderRight'>{$overpayment}</td>";
                    }

                    $stock_row = "";
                    $collection_rows = "";
                    if($idPb){                           
                        $less_stock_title_class = "";
                        $less_stock_title_text = "";
                        $less_stock_asterisk = "";

                        $collection_rows = $collection_empty;
                        if($collection){
//                            utils::log('TRACE 4', 'logAleix');
//                            utils::log($array_info[$day_]['when_datetime'], 'logAleix');
//                            utils::log($array_info[$day_]['collection_time'], 'logAleix');

                            if($array_info[$day_]['when_datetime'] < $array_info[$day_]['collection_time']){
//                                utils::log('TRACE 0', 'logAleix');
                                $less_stock = FALSE;
                                $less_stock_title_class = "info";
                                $less_stock_title_text = "title='The stock may be lower due printing the collection report'";
                                $less_stock_asterisk = "*";
                            }

                            $collectionNum = "<p class='no-breakLine'>";
                            $i = 0;
                            foreach ($array_info[$day_]['collectionNum'] as $collectionNumber){
                                if($i < 2){
                                    $collectionNum .= "#{$collectionNumber} ";
                                }
                                $i++;
                            }
                            if($i > 2){
                                $otherCollections = $i-2;
                                $collectionNum .= "(<i class='fa fa-plus' style='font-size: 8pt!important;' aria-hidden='true'></i>{$otherCollections})";
                            }
                            $collectionNum .= "</p>";

                            $collection_rows = "<td class='no-color td-small'></td><td class='borderRight'>{$collectionNum}</td>";                                
                        }

                        $stock_row = "<td class='borderRight {$less_stock_title_class}' {$less_stock_title_text}>{$stock}{$less_stock_asterisk}</td>";
                    }
                    
                    $twilio_row = "<td>{$costWhats}</td><td>{$costSMS}</td><td class='borderRight totalization'>{$costTwilio}</td>";
                    
                    $htmldata_ = <<<HTML
                        {$hoursOperation_row}
                        <td class='borderLeft'>{$plays}</td>
                        <td>{$freePlays}</td>
                        <td>{$upsells}</td>
                        <td>{$pasport}</td>
                        <td>{$SmartPrint}</td>
                        <td class='borderRight totalization'>{$prints}</td>
                        <td>{$cash}</td>
                        <td>{$creditCard}</td>
                        <td>{$net}</td>
                        <td class='borderRight totalization'>{$money}</td>
                        {$overpayment_row}
                        {$stock_row}
                        {$collection_rows}
                        {$twilio_row}
HTML;
                        
                    $z++;
                }  
            }
            
            $html .= $htmldata_;
            $html .="</tr>";
            
            
            $i++;
            $startDateTime->modify("+1 day");
        }
        
        if($currencyChange == 0){
            $totalCash = utils::putCurrency($totalCash, $currency, $currency_postition);
            $totalCreditCard = utils::putCurrency($totalCreditCard, $currency, $currency_postition);
            $totalNet = utils::putCurrency( $totalNet, $currency, $currency_postition);
            $totalmoney = utils::putCurrency($totalmoney, $currency, $currency_postition);   
            $totalOverpayment = utils::putCurrency($totalOverpayment, $currency, $currency_postition); 
            $totalCostWhats = utils::putCurrencyFloat($totalCostWhats, '&#36;', 1); //sempre ens cobren en dollar USD $ &#36;
            $totalCostSMS = utils::putCurrencyFloat($totalCostSMS, '&#36;', 1); //sempre ens cobren en dollar USD $ &#36;
            $totalCostTwilio = utils::putCurrencyFloat($totalCostTwilio, '&#36;', 1); //sempre ens cobren en dollar USD $ &#36;
        }
        
        $totalTimeOperation = utils::printHours($totalHoursOperation, $totalMinutesOperation);
        $totalPrints = ($totalPrints)? $totalPrints : "0";
        $totalPlays = ($totalPlays)? $totalPlays : "0";
        $totalFreePlays = ($totalFreePlays)? $totalFreePlays : "0";
        $totalUpsells = ($totalUpsells)? $totalUpsells : "0";
        $totalpassport = ($totalpassport) ? $totalpassport : "0";
        $totalSmartPrint = ($totalSmartPrint) ? $totalSmartPrint : "0";

        $totalHoursOperation_row = "";
        $totalOverpayment_row = "";
        if($minPBversion >= 21){
            $totalHoursOperation_row = "<td class='borderLeft'>{$totalTimeOperation}h</td>";
            $totalOverpayment_row = "<td class='borderRight'>{$totalOverpayment}</td>";
        }
        
        $TotalStock_row = "";
        $TotalCollection_rows = "";
        if($idPb){
            $TotalStock_row .= "<td class='borderRight'></td>";
            $TotalCollection_rows .= "<td class='no-color td-small'></td><td class='no-color borderRight'></td>";
        }
       
        $TotalTwilio_rows = "<td class='totalization'>{$totalCostWhats}</td><td class='totalization'>{$totalCostSMS}</td><td class='borderRight totalization'>{$totalCostTwilio}</td>";
        
        $html .= <<<HTML
            <tr class='RowTotal'>
                <td>Total</td>
                {$totalHoursOperation_row}
                <td class='borderLeft'>$totalPlays</td>
                <td>$totalFreePlays</td>
                <td>$totalUpsells</td>
                <td>$totalpassport</td>
                <td>$totalSmartPrint</td>        
                <td class='borderRight'>$totalPrints</td>
                <td>$totalCash</td>
                <td>$totalCreditCard</td>
                <td>$totalNet</td>
                <td class='borderRight'>$totalmoney</td>
                {$totalOverpayment_row}
                {$TotalStock_row}
                {$TotalCollection_rows}
                {$TotalTwilio_rows}
            </tr>
HTML;
        
        return $html;
    }

    private function getSumaryYearReport($startDateTime, $endDateTime, $array_info, $idPb, $minPBversion) {
        $html = "";
        $i = 1;
        $z = 0;
        $currencyChange = 0;
        
        $auditsCounter = $this->RepdcModel->getAuditLastConection($this->idUser, $idPb);
        $lastInfo = \DateTime::createFromFormat('Y-m-d', $auditsCounter);
        
        $totalPrints         = 0;
        $totalPlays          = 0;
        $totalFreePlays      = 0;
        $totalUpsells        = 0;
        $totalCash           = 0;
        $totalCreditCard     = 0;
        $totalNet            = 0;
        $totalmoney          = 0;
        $totalOverpayment    = 0;
        $totalHoursOperation = 0;
        $totalMinutesOperation = 0;
        $totalTimeOperation  = "0:00";
        $totalCostWhats      = 0;
        $totalCostSMS        = 0;
        $totalCostTwilio     = 0;
        
//        utils::log($array_info, 'logAleix');
        
        while ($startDateTime <= $endDateTime) {
//            $day = $startDateTime->format('l m/d/Y');
//            $day = $startDateTime->format('r');
            $day = $startDateTime->format('F Y');
            
            $day_ = $startDateTime->format('Y-m');
            $month_ = $startDateTime->format('m');
            $year_ = $startDateTime->format('Y');
            
            //La maquina podria haver estat encesa i no tenir activitat igualment
            $hoursOperation = "-";
            $minutesOperation = "-";
            $hoursOperation_time = "-";
            if(isset($this->hoursOperationArray)){
                $hoursOperation = "0";
                $minutesOperation = "0";
                $hoursOperation_time = "0:00";
                if(isset($this->hoursOperationArray[$day_])){
                    $hoursOperation = $this->hoursOperationArray[$day_]['hours'];
                    $minutesOperation = $this->hoursOperationArray[$day_]['minutes'];
                    $hoursOperation_time = $this->hoursOperationArray[$day_]['time'];
                }
            }
            
            list($totalHoursOperation, $totalMinutesOperation) = utils::sumHours($totalHoursOperation, $totalMinutesOperation, $hoursOperation, $minutesOperation);

//            utils::log("Hours Operation = {$hoursOperation}", "logAleix");
             
            // Overpayment and HoursOperation Rows
            $hoursOperation_empty = "";
            $overpayment_empty = "";
            $hoursOperation_unknown = "";
            $overpayment_unknown = "";
            if($minPBversion >= 21){
                $hoursOperation_empty = "<td class='borderLeft'>{$hoursOperation_time}h</td>";
                $overpayment_empty = "<td class='borderRight'>0</td>";
                $hoursOperation_unknown = "<td class='borderLeft'>-</td>";
                $overpayment_unknown = "<td class='borderRight'>-</td>";
            }

            // Stock Row
            $stock_empty = "";
            $stock_unknown = "";
            $collection_empty = "";
            $collection_unknown = "";
            if($idPb){
                $stock_empty .= "<td class='borderRight'>-</td>";
                $stock_unknown .= "<td class='borderRight'>-</td>";
                $collection_empty   .= "<td class='no-color td-small'></td><td class='borderRight'>0</td>";
                $collection_unknown .= "<td class='no-color td-small'></td><td class='borderRight'>-</td>";
            }
            
            $twilio_empty = "<td>0</td><td>0</td><td class='borderRight totalization'>0</td>";
            $twilio_unknown = "<td>-</td><td>-</td><td class='borderRight totalization'>-</td>";
           

            $empty_htmldata = <<<HTML
                {$hoursOperation_empty}
                <td class='borderLeft'>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td class='borderRight totalization'>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td class='borderRight totalization'>0</td>
                {$overpayment_empty}
                {$stock_empty}
                {$collection_empty}
                {$twilio_empty}
HTML;

            $unknown_htmldata = <<<HTML
                {$hoursOperation_unknown}
                <td class='borderLeft'>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td class='borderRight totalization'>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td class='borderRight totalization'>-</td>
                {$overpayment_unknown}
                {$stock_unknown}
                {$collection_unknown}
                {$twilio_unknown}
HTML;
            
            if($lastInfo > $startDateTime){
                $htmldata_ = $empty_htmldata;
            }
            else{
                $htmldata_ = $unknown_htmldata;
            }        
            
            $html .="<tr>";
            $html .="<td>$day</td>";
   
            if(isset($array_info) && count($array_info)>0){
                if(isset($array_info[$day_])){
                    
                    $month = $array_info[$day_]['month'];
                    $year  = $array_info[$day_]['year'];

                    if(intval($month) == intval($month_) && intval($year) == intval($year_) ){
                        $prints      = ($array_info[$day_]['prints']) ? $array_info[$day_]['prints'] : "0";
                        $pasport = ($array_info[$day_]['pas']) ? $array_info[$day_]['pas'] : "0";
                        $SmartPrint = ($array_info[$day_]['SmartPrint']) ? $array_info[$day_]['SmartPrint'] : "0";
                        $freePlays   = ($array_info[$day_]['freePlays']) ? $array_info[$day_]['freePlays'] : "0";
//                        $freePlays = $this->calcPlaysFP($array_info[$day_]['freePlays'], intval($pasport), intval($SmartPrint));
                        $plays = $this->calcPlays($array_info[$day_]['plays'], intval($freePlays), intval($pasport), intval($SmartPrint));
                        $upsells     = ($array_info[$day_]['in4']) ? $array_info[$day_]['in4'] : "0";
                        
                        $cash        = ($array_info[$day_]['i3']) ? $array_info[$day_]['i3'] : "0";
                        $cash_60     = ($array_info[$day_]['i3_60']) ? $array_info[$day_]['i3_60'] : "0";
                        $cash        = intval($cash) + intval($cash_60);
                        
                        $creditCard  = ($array_info[$day_]['i4']) ? $array_info[$day_]['i4'] : "0";
                        $creditCard_60     = ($array_info[$day_]['i4_60']) ? $array_info[$day_]['i4_60'] : "0";
                        $creditCard        = intval($creditCard) + intval($creditCard_60);
                        $net         = ($array_info[$day_]['i5']) ? $array_info[$day_]['i5'] : "0";
                        $money       = ($array_info[$day_]['money_']) ? $array_info[$day_]['money_'] : "0";
                        $overpayment = (intval($cash) + intval($creditCard) + intval($net)) - intval($money);
                        if($overpayment<0){

                            $sumaOver = -$overpayment;
                            $overpayment = 0;
                            $creditCard = $creditCard + $sumaOver;
                        }
                        $money       = intval($cash) + intval($creditCard) + intval($net);
                        $stock       = ($array_info[$day_]['stock']) ? $array_info[$day_]['stock'] : "0";
                        $costWhats   = ($array_info[$day_]['costWhats']) ? $array_info[$day_]['costWhats'] : "0"; 
                        $costSMS     = ($array_info[$day_]['costSMS']) ? $array_info[$day_]['costSMS'] : "0"; 
                        $costTwilio  = ($array_info[$day_]['costTwilio']) ? $array_info[$day_]['costTwilio'] : "0"; 
    //                    $stock = ($stock_) ? $stock_ : "-";
                        
                        $collection         =   $array_info[$day_]['collection'];
                        $collectionNum      =   ($array_info[$day_]['collectionNum']) ? $array_info[$day_]['collectionNum'] : "0";
                        
                        $currency = $array_info[$day_]['currency_symbol'];
                        $currency_postition =  $array_info[$day_]['currency_position'];
                        
                        $totalPrints         += intval($prints);
                        $totalPlays          += intval($plays);
                        $totalFreePlays      += intval($freePlays);
                        $totalUpsells        += intval($upsells);
                        $totalpassport += intval($pasport);
                        $totalSmartPrint += intval($SmartPrint);
                        $totalCash           += intval($cash);
                        $totalCreditCard     += intval($creditCard);
                        $totalNet            += intval($net);
                        $totalmoney          += intval($money);
                        $totalOverpayment    += intval($overpayment);
                        $totalCostWhats      += $costWhats;
                        $totalCostSMS        += $costSMS;
                        $totalCostTwilio     += $costTwilio;
                        
                        $cash = utils::putCurrency($cash, $currency, $currency_postition);
                        $creditCard = utils::putCurrency($creditCard, $currency, $currency_postition);
                        $net = utils::putCurrency($net, $currency, $currency_postition);
                        $money = utils::putCurrency($money, $currency, $currency_postition);
                        $overpayment = utils::putCurrency($overpayment, $currency, $currency_postition);
                        $costWhats = utils::putCurrencyFloat($costWhats, '&#36;', 1);
                        $costSMS = utils::putCurrencyFloat($costSMS, '&#36;', 1);
                        $costTwilio = utils::putCurrencyFloat($costTwilio, '&#36;', 1);

                        if($z == 0){
                            $totalCurrency = $currency;
                        }
                        elseif ($currencyChange == 0) {
                            if($totalCurrency != $currency){
                                $currencyChange = 1;
                            }
                        }

                        $overpayment_row = "";
                        $hoursOperation_row = "";
                        if($minPBversion >= 21){
                            $hoursOperation_row = "<td class='borderLeft'>{$hoursOperation_time}h</td>";
                            $overpayment_row = "<td class='borderRight'>{$overpayment}</td>";
                        }
                        
                        $stock_row = "";
                        $collection_rows = "";
                        if($idPb){                           
                            $less_stock_title_class = "";
                            $less_stock_title_text = "";
                            $less_stock_asterisk = "";

                            $collection_rows = $collection_empty;
                            if($collection){
//                                utils::log('TRACE 4', 'logAleix');
//                                utils::log($array_info[$day_]['when_datetime'], 'logAleix');
//                                utils::log($array_info[$day_]['collection_time'], 'logAleix');

                                if($array_info[$day_]['when_datetime'] < $array_info[$day_]['collection_time']){
//                                    utils::log('TRACE 0', 'logAleix');
                                    $less_stock = FALSE;
                                    $less_stock_title_class = "info";
                                    $less_stock_title_text = "title='The stock may be lower due printing the collection report'";
                                    $less_stock_asterisk = "*";
                                }

                                $collectionNum = "<p class='no-breakLine'>";
                                $i = 0;
                                foreach ($array_info[$day_]['collectionNum'] as $collectionNumber){
                                    if($i < 2){
                                        $collectionNum .= "#{$collectionNumber} ";
                                    }
                                    $i++;
                                }
                                if($i > 2){
                                    $otherCollections = $i-2;
                                    $collectionNum .= "(<i class='fa fa-plus' style='font-size: 8pt!important;' aria-hidden='true'></i>{$otherCollections})";
                                }
                                $collectionNum .= "</p>";

                                $collection_rows = "<td class='no-color td-small'></td><td class='borderRight'>{$collectionNum}</td>";                                
                            }

                            $stock_row = "<td class='borderRight {$less_stock_title_class}' {$less_stock_title_text}>{$stock}{$less_stock_asterisk}</td>";
                        }
                       
                        
                        $twilio_row = "<td>{$costWhats}</td><td>{$costSMS}</td><td class='borderRight totalization'>{$costTwilio}</td>";
                        
                        $htmldata_ = <<<HTML
                            {$hoursOperation_row}
                            <td class='borderLeft'>{$plays}</td>
                            <td>{$freePlays}</td>
                            <td>{$upsells}</td>
                            <td>{$pasport}</td>
                            <td>{$SmartPrint}</td>
                            <td class='borderRight totalization'>{$prints}</td>
                            <td>{$cash}</td>
                            <td>{$creditCard}</td>
                            <td>{$net}</td>
                            <td class='borderRight totalization'>{$money}</td>
                            {$overpayment_row}
                            {$stock_row}
                            {$collection_rows}
                            {$twilio_row}
HTML;
                            
                        $z++;
                    }
                }
            }

            
            $html .= $htmldata_;
            $html .="</tr>";
            
            
            $i++;
            $startDateTime->modify("+1 month");
        }
        
        if($currencyChange == 0){
            $totalCash = utils::putCurrency($totalCash, $currency, $currency_postition);
            $totalCreditCard = utils::putCurrency($totalCreditCard, $currency, $currency_postition);
            $totalNet = utils::putCurrency( $totalNet, $currency, $currency_postition);
            $totalmoney = utils::putCurrency($totalmoney, $currency, $currency_postition);   
            $totalOverpayment = utils::putCurrency($totalOverpayment, $currency, $currency_postition);   
            $totalCostWhats = utils::putCurrencyFloat($totalCostWhats, '&#36;', 1); //sempre ens cobren en dollar USD $ &#36;
            $totalCostSMS = utils::putCurrencyFloat($totalCostSMS, '&#36;', 1); //sempre ens cobren en dollar USD $ &#36;
            $totalCostTwilio = utils::putCurrencyFloat($totalCostTwilio, '&#36;', 1); //sempre ens cobren en dollar USD $ &#36;
        }
        
        $totalTimeOperation = utils::printHours($totalHoursOperation, $totalMinutesOperation);
        $totalPrints = ($totalPrints)? $totalPrints : "0";
        $totalPlays = ($totalPlays)? $totalPlays : "0";
        $totalFreePlays = ($totalFreePlays)? $totalFreePlays : "0";
        $totalUpsells = ($totalUpsells)? $totalUpsells : "0";
        $totalpassport = ($totalpassport) ? $totalpassport : "0";
        $totalSmartPrint = ($totalSmartPrint) ? $totalSmartPrint : "0";
        
        $totalHoursOperation_row = "";
        $totalOverpayment_row = "";
        if($minPBversion >= 21){
            $totalHoursOperation_row = "<td class='borderLeft'>{$totalTimeOperation}h</td>";
            $totalOverpayment_row = "<td class='borderRight'>{$totalOverpayment}</td>";
        }
        
        $TotalStock_row = "";
        $TotalCollection_rows = "";
        if($idPb){
            $TotalStock_row .= "<td class='borderRight'></td>";
            $TotalCollection_rows .= "<td class='no-color td-small'></td><td class='no-color'></td>";
        }
      
        $TotalTwilio_rows = "<td class='totalization'>{$totalCostWhats}</td><td class='totalization'>{$totalCostSMS}</td><td class='borderRight totalization'>{$totalCostTwilio}</td>";
       
        $html .= <<<HTML
            <tr class='RowTotal'>
                <td>Total</td>
                {$totalHoursOperation_row}
                <td class='borderLeft'>$totalPlays</td>
                <td>$totalFreePlays</td>
                <td>$totalUpsells</td>
                <td>$totalpassport</td>
                <td>$totalSmartPrint</td>
                <td class='borderRight'>$totalPrints</td>
                <td>$totalCash</td>
                <td>$totalCreditCard</td>
                <td>$totalNet</td>
                <td class='borderRight'>$totalmoney</td>
                {$totalOverpayment_row}
                {$TotalStock_row}
                {$TotalCollection_rows}
                {$TotalTwilio_rows}
            </tr>
HTML;

        return $html;
    }
    
    private function calcPlays($plays, $freePlays, $pasport, $SmartPrint) {
        if($freePlays !== FALSE){
            $plays = $plays - $freePlays - $pasport - $SmartPrint;
        } 
        else {
            $plays = $plays - $pasport - $SmartPrint;
        }
        
        return $plays;
    }
}

new auditsManager($USERID, $USERTYPE);
//new auditsManager(305, $USERTYPE);
//new auditsManager(448, $USERTYPE);
