<?php
include_once dirname(__FILE__) . '../../../common/global.php';
require_once G_PATH . "common/Classes/baseController.php";
require_once G_PATH . "common/Classes/MailViewRender.php";

include_once G_PATH . 'common/conexio.php';
include G_PATH . 'app/owner/Repdc_common.php';

function APP_fesLogDebbug($text, $filename) {
    if(filesize($filename . ".dat") > 5000000){
        rename($filename . ".dat" , "$filename-".  rndm32(3).".bak" );
        $fh = fopen($filename . ".dat", 'w');
    }
    else $fh = fopen($filename . ".dat", 'a');
    
    $ara = new DateTime("now");
    fwrite($fh, $ara->format("mdhis"). "\t$text\r");
    fclose($fh);
}

 class RepdcManager extends baseController{
    const WEEKLY = 1;
    const MONTHLY = 2;
    const YEARLY = 3;
    const COLLECTION = 4;
    
    public $reportType = 0;
    public $idUser = FALSE;
    public $userType = FALSE;
    
    public $hasOlderData      = FALSE;
    public $PBLastConnection  = FALSE;
    public $reportTypeName  = NULL;
    public $titleColor      = NULL;
    public $booth_type      = NULL;
    public $serialnumber    = NULL;
    public $idBooth         = NULL;
    public $rand_string     = NULL;
    public $owner_name      = NULL;
    public $both_name       = NULL;
    public $location        = NULL;
    public $version         = NULL;
    public $alert_email     = NULL;
    
    public $week_number     = NULL;
    public $month_number    = NULL;
    public $year_number     = NULL;
    public $last_connection = NULL;
    public $last_connection_str = NULL;

    public $subject = NULL;
    
    /**
     * Necessary only to call specific app methods
     */
    public $APP_BdD = NULL;

    public $repdc_body = "";

    public function __construct($USERID, $USERTYPE){
        parent::__construct();
        
        $this->APP_BdD = getNewBdD();

        $this->createModel('Repdc');
        
        $this->idUser = $USERID;
        $this->userType = $USERTYPE;
    }
    
    public function changeUser($USERID, $USERTYPE){    
        $this->idUser = $USERID;
        $this->userType = $USERTYPE;
    }    
    
    /**
     * Generates and sends a Weekly Report
     * 
     * @param Integer $idBooth 
     * @param DateTime $dataInicial
     * @param DateTime $dataFinal
     */
    public function sendSingleWeeklyReport($idBooth, $dataInicial, $dataFinal){    
        
        $success = FALSE;
                
        $this->reportType = RepdcManager::WEEKLY;
        $this->reportTypeName = "WEEKLY Report";
        $this->titleColor = "#FF0";
        $this->subject = "WEEKLY Report from your PB ";
        
        $data = $this->getDataReportToOwner($idBooth, $dataInicial, $dataFinal);
        
        if($data){
            $mailView = new MailViewRender();
            $mailView->setEmail($this->ownerEmail);
            $mailView->setEmailName($this->owner_name);
            $success = $mailView->sendRepdcWeekly(
                $this->subject, 
                $this->titleColor, 
                $this->reportTypeName, 
                $dataInicial, 
                $dataFinal, 
                $this->booth_type, 
                $this->serialnumber, 
                $this->idBooth, 
                $this->rand_string, 
                $this->owner_name, 
                $this->both_name, 
                $this->location, 
                $this->version, 
                $this->week_number,
                $this->last_connection_str,
                $this->repdc_body
            );
        }
        
        return $success;        
    }
    
    /**
     * Generates and sends a Monthly Report
     * 
     * @param Integer $idBooth 
     * @param DateTime $dataInicial
     * @param DateTime $dataFinal
     */
    public function sendSingleMonthlyReport($idBooth, $dataInicial, $dataFinal){
        $success = FALSE;

        $this->reportType = RepdcManager::MONTHLY;
        $this->reportTypeName = "MONTHLY Report";
        $this->titleColor = "#0F0";
        $this->subject = "MONTHLY Report from your PB ";
        
        $data = $this->getDataReportToOwner($idBooth, $dataInicial, $dataFinal);
        if($data){
            $mailView = new MailViewRender();
            $mailView->setEmail($this->ownerEmail);
            $mailView->setEmailName($this->owner_name);
            $success = $mailView->sendRepdcMonthly(
                $this->subject, 
                $this->titleColor, 
                $this->reportTypeName, 
                $dataInicial, 
                $dataFinal, 
                $this->booth_type, 
                $this->serialnumber, 
                $this->idBooth, 
                $this->rand_string, 
                $this->owner_name, 
                $this->both_name, 
                $this->location, 
                $this->version, 
                $this->month_number,
                $this->last_connection_str,
                $this->repdc_body
            );
        }

        return $success;
    }
    
    /**
     * Generates and sends a Monthly Report
     * 
     * @param Integer $idBooth 
     * @param DateTime $dataInicial
     * @param DateTime $dataFinal
     */
    public function sendSingleYearReport($idBooth, $dataInicial, $dataFinal){
        $success = FALSE;

        $this->reportType = RepdcManager::YEARLY;
        $this->reportTypeName = "YEAR Report";
        $this->titleColor = "#0FF";
        $this->subject = "YEAR Report from your PB ";
        
        $data = $this->getDataReportToOwner($idBooth, $dataInicial, $dataFinal);
        if($data){
            $mailView = new MailViewRender();
            $mailView->setEmail($this->ownerEmail);
            $mailView->setEmailName($this->owner_name);
            $success = $mailView->sendRepdcYear(
                $this->subject, 
                $this->titleColor, 
                $this->reportTypeName, 
                $dataInicial, 
                $dataFinal, 
                $this->booth_type, 
                $this->serialnumber, 
                $this->idBooth, 
                $this->rand_string, 
                $this->owner_name, 
                $this->both_name, 
                $this->location, 
                $this->version, 
                $this->year_number,           
                $this->last_connection_str,
                $this->repdc_body
            );
        }

        return $success;
    }
    
    /**
     * Generates and sends a Collection Report
     * 
     * @param Integer $idBooth 
     * @param DateTime $dataInicial
     * @param DateTime $dataFinal
     */
    public function sendSingleCollectionReport($idBooth, $dataInicial, $dataFinal){
        $success = FALSE;

        $this->reportType = RepdcManager::COLLECTION;
        $this->reportTypeName = "COLLECTION Report";
        $this->titleColor = "#F00";
        $this->subject = "COLLECTION Report {$reportNum} from your PB {$pbName}. Location name: {$pbLocation} ";
        
        $data = $this->getDataReportToOwner($idBooth, $dataInicial, $dataFinal);
        if($data){
            $mailView = new MailViewRender();
            $mailView->setEmail($this->ownerEmail);
            $mailView->setEmailName($this->owner_name);
            $success = $mailView->sendRepdcCollection(
                $this->subject, 
                $this->titleColor, 
                $this->reportTypeName, 
                $dataInicial, 
                $dataFinal, 
                $this->booth_type, 
                $this->serialnumber, 
                $this->idBooth, 
                $this->rand_string, 
                $this->owner_name, 
                $this->both_name, 
                $this->location, 
                $this->version, 
                $this->repdc_body
            );
        }

        return $success;
    }

    public function getCurrenciesView($idBooth, $startDate, $endDate){
        $html = FALSE;

        list($nCurrencies, $array_currencies, $array_currenciesName, $array_currenciesPosition, $array_currenciesSymbol) = $this->RepdcModel->checkForCurrencies($idBooth, $startDate, $endDate);
        
        if($nCurrencies > 0){                
            $html = "";
            for($c = 0; $c<$nCurrencies; $c++){
                $html .= "<p><strong>Amounts in $array_currenciesName[$c]</strong></p>";
                $html .= $this->Repdc_moneyByPayment($idBooth, $startDate, $endDate, $array_currencies[$c], $array_currenciesSymbol[$c], $array_currenciesPosition[$c]);
                $html .= $this->Repdc_moneyByProduct($idBooth, $startDate, $endDate, $array_currencies[$c], $array_currenciesSymbol[$c], $array_currenciesPosition[$c]);
                $html .= "<p></p>";
            }
        }
        
        return $html;
    }
    
    public function getFreePlaysView($idBooth, $startDate, $endDate){
        $html = FALSE;
        $hasFreeplays = FALSE;

        $hasFreeplays = $this->RepdcModel->checkForFreePlays($idBooth, $startDate, $endDate);
        
        if($hasFreeplays){
            $html = "";
            $html .= "<p><strong>Free plays</strong></p>";
            $html .= $this->Repdc_freeplaysByProduct($idBooth, $startDate, $endDate);
            $html .= "<p></p>";
        }

        return $html;
    }
    
    /**
     * Check if the report is completed for one PB in one interval
     * 
     * @param Integer $idBooth
     * @param String $startDate Must be formatted as 'm/d/Y'
     * @param String $endDate Must be formatted as 'm/d/Y'
     * @return Boolean, true if success, False otherwise
     */
    public function checkReportIntegrityByPb($idBooth, $startDate, $endDate){
        $this->hasData = FALSE;
        $this->hasOlderData = FALSE;
        //Si es la ultima setamana i tenim dades fins avui no mostrarem warning **Partially
        $nowDate = date("m/d/Y H:i:s");
        
        $nowDateTime = DateTime::createFromFormat('m/d/Y H:i:s', $nowDate);         
        $nowDateTime->sub(new DateInterval('PT6H10M')); //Restem 6h per posar a hora del server i 10 minuts que es mes del que triga entre crides PBnew_Alive (5minuts)
      
        $startDateTime = DateTime::createFromFormat('m/d/Y H:i:s', $startDate . ' 00:00:00');
        $endDateTime = DateTime::createFromFormat('m/d/Y H:i:s', $endDate . ' 23:59:59');
        $this->PBendDateTime = $endDateTime;
        $this->PBLastConnection = $this->getPbLastConnectionLocal($idBooth);
//        var_dump($this->PBLastConnection);exit;
        //$this->PBLastConnection = $this->getPbLastConnection($idBooth);
        
        //Agafem hora server per a comparar amb now. a la ultima pagina ens interessa saber-ho
        $this->PBLastConn = $this->getPbLastConn($idBooth);

        if($this->PBLastConnection > $endDateTime){
            $this->hasOlderData = TRUE;
            $this->hasData = TRUE;
        } else if($this->PBLastConnection >= $startDateTime && $this->PBLastConnection <= $endDateTime){
            $this->hasOlderData = FALSE;
            $this->hasData = TRUE;
        } else {
            $this->hasOlderData = FALSE;
            $this->hasData = FALSE;
        }
        //Si es la ultima setmana i tenim dades fins avui no mostrarem warning **Partially . 
        //Sobretot si la data d'avui es inferior a l'ultim dia de la setamana que mostrem
        if($endDateTime > $nowDateTime){
//           var_dump($this->PBLastConn); 
//            var_dump($nowDateTime);
      
      
      
            if( $this->PBLastConn < $nowDateTime){
                
                $this->hasOlderData = FALSE;
            }else{
                $this->hasOlderData = TRUE;
//                print $nowDate; exit;
            }
        }
        //ELIMINAR, nomes debug
        if( $this->hasOlderData){
           
           utils::log("TRUE te dades recents hasOlderData: {$this->hasOlderData} now: {$nowDate}", 'logFerran');
        }else{
            utils::log("FALSE te dades recents hasOlderData: {$this->hasOlderData}  now: {$nowDate}", 'logFerran');
        }
        
        return array($this->hasData, $this->hasOlderData, $this->PBLastConnection, $this->PBendDateTime );
    }
    
    /**
     * Check if the report is completed for all PBs of one owner in one interval
     * 
     * @param Integer $idBooth
     * @param String $startDate Must be formatted as 'm/d/Y'.
     * @param String $endDate Must be formatted as 'm/d/Y'.
     * @return Boolean, true if success, False otherwise
     */
    public function checkReportIntegrityByOwner($startDate, $endDate){
        $this->hasData = FALSE;
        $this->hasOlderData = FALSE;

        $startDateTime = DateTime::createFromFormat('m/d/Y H:i:s', $startDate . ' 00:00:00');
        $endDateTime = DateTime::createFromFormat('m/d/Y H:i:s', $endDate . ' 23:59:59');
        $this->PBendDateTime = $endDateTime;
        $this->PBLastConnection = $this->RepdcModel->getTotalOwnerPbsLastConnection($this->idUser);
        if($this->PBLastConnection > $endDateTime){
            $this->hasOlderData = TRUE;
            $this->hasData = TRUE;
        } else if($this->PBLastConnection >= $startDateTime && $this->PBLastConnection <= $endDateTime){
            $this->hasOlderData = FALSE;
            $this->hasData = TRUE;
        } else {
            $this->hasOlderData = FALSE;
            $this->hasData = FALSE;
        }
        
        return array($this->hasData, $this->hasOlderData, $this->PBLastConnection, $this->PBendDateTime);
    }
    
    public function getPbLastConn($idBooth){
        return $this->PBLastConn = $this->RepdcModel->getPbLastConn($idBooth);
    }
    
    public function getPbLastConnection($idBooth){
        return $this->PBLastConnection = $this->RepdcModel->getPbLastConnection($idBooth);
    }
    public function getPbLastConnectionLocal($idBooth){
        return $this->PBLastConnection = $this->RepdcModel->getPbLastConnectionLocal($idBooth);
    }
    public function getPbLastConnectionLocal_appBooths($idBooth){
        return $this->PBLastConnection = $this->RepdcModel->getPbLastConnectionLocal_appBooths($idBooth);
    }
    public function getSinglePBInfo($idBooth, $idUser){
        return $this->SinglePBInfo = $this->RepdcModel->getSinglePBInfo($idBooth, $idUser);
    }
    
    /**
     * Prepare the data to build a report for a specific dates
     * 
     * @param Integer $idBooth
     * @param String $startDate Must be formatted as 'm/d/Y'
     * @param String $endDate Must be formatted as 'm/d/Y'
     * @return Boolean, true if success, False otherwise
     */
    public function getDataReportToOwner($idBooth, $startDate, $endDate){
                
        $result = FALSE;
        $this->repdc_body = "";
        
        $pb = $this->RepdcModel->getSinglePBInfo($idBooth, $this->idUser);
        
        if(!empty($pb) && $pb['alert_email'] != NULL){
            $this->booth_type = $pb['booth_type'];
            $this->serialnumber = $pb['serialnumber'];
            $this->idBooth = $pb['idBooth'];
            $this->rand_string = $pb['rand_string'];
            $this->owner_name = $pb['owner_name'];
            $this->both_name = $pb['both_name'];
            $this->location = $pb['location'];
            $this->version = $pb['version'];
            $this->alert_email = $pb['alert_email'];
            $this->ownerEmail = $pb['alert_email'];
            $this->owner_name = $pb['owner_name'];
            
            $this->checkReportIntegrityByPb($idBooth, $startDate, $endDate);
            
            $lastConn = $this->getLastCon($idBooth);
            
            $this->subject = $this->subject . $this->both_name;
            
            if(!$this->hasOlderData){
                utils::log("This audit may be incompleted, no data receivasdfsaed since {$this->last_connection}", 'logFerran');
                
                if($this->PBLastConnection->format('m/d/Y') < $startDate){
                    $message = "
                        <p style='font-weight: bold;'>**No data received from the photobooth**</p>
                        <p>Audit not available because no data received since {$this->last_connection}</p>
                        <hr />
                    ";
                }
                elseif ($this->PBLastConnection->format('m/d/Y') >= $startDate && $this->PBLastConnection->format('m/d/Y') <= $endDate) {
                    $message = " 
                            <p style='font-weight: bold;'>**Partially data received from the photobooth** </p>
                            <p>This audit may be incomplete, because no data received since {$this->last_connection}</p>
                            <hr />";
                }
            }
            
            $this->repdc_body .= <<<HTML
                <p>NO INFORMATION AVAILABLE</p>
                <p  style='margin-bottom:0px;'>
                    No Information available means that the server didn´t receive any data from this PhotoBooth. That could be for the following reasons:
                </p>
                <ol style='margin-top:0px;'>
                    <li>The PhotoBooth is not connected to Internet</li>
                    <li>The PhotoBooth is connected to Internet, but the signal was down during that period of time.</li>
                    <li>The PhotoBooth had no activity or was off</li>
                </ol>
HTML;
            
            $startDateTime = DateTime::createFromFormat('m/d/Y H:i:s', $startDate . ' 00:00:00');
            $endDateTime = DateTime::createFromFormat('m/d/Y H:i:s', $endDate . ' 23:59:59');
            
            
            $this->week_number = $startDateTime->format('W');
            $this->month_number = $startDateTime->format('m');
            $this->year_number = $startDateTime->format('Y');
            
            
            if($this->hasData){
                $sumaryView = $this->RepdcSummary($idBooth , $startDateTime, $endDateTime);
                $currenciesView = $this->getCurrenciesView($idBooth, $startDateTime, $endDateTime);
                $freePlaysView = $this->getFreePlaysView($idBooth, $startDateTime, $endDateTime);
                
                $this->repdc_body = "";
                $this->repdc_body .= $message;
                $this->repdc_body .= $sumaryView;
                
                if($currenciesView || $freePlaysView){
                    $this->repdc_body .= $currenciesView;
                    $this->repdc_body .= $freePlaysView;
                }
                
                if($this->reportType == RepdcManager::WEEKLY or $this->reportType == RepdcManager::MONTHLY){
                    $this->repdc_body .= $this->DailyReport($idBooth, $this->idUser , $startDateTime, $endDateTime);
                    $this->repdc_body .= "<p></p>";
                    
                    $this->repdc_body .= $this->ActivityBySession($idBooth, $this->idUser, $startDateTime, $endDateTime, $lastConn);
                    $this->repdc_body .= "<p></p>";
                }
                
                if($this->reportType == RepdcManager::YEARLY){
                    $this->repdc_body .= $this->MonthlyReport($idBooth, $this->idUser , $startDateTime, $endDateTime);
                    $this->repdc_body .= "<p></p>";
                }
                
                if($this->reportType == RepdcManager::WEEKLY){
                    $this->repdc_body .= "<p></p>";
                    $this->repdc_body .= $this->Repdc_activity($idBooth, $startDateTime, $endDateTime);
                    $this->repdc_body .= "<p></p>";
                }
            }
            else{
                $this->repdc_body .= "**No data received from the photobooth**<br/> Audit not available because no data receivsafdsggaged since {$this->PBLastConnection->format('m/d/Y H:i:s')}";
                
            }
            
//            $this->last_connection = $this->last_connection->format('m/d/Y H:i');
            
            $result = TRUE;
            
        }
        
        return $result;
    }
    
    /**
     * Prepare the data to build a summary report for specific dates and PB for web-view purposes
     * 
     * @param Integer $idBooth
     * @param String $startDate Must be formatted as 'm/d/Y'
     * @param String $endDate Must be formatted as 'm/d/Y'
     * @return Boolean, true if success, False otherwise
     */
    public function getSummaryByDayInfo($idBooth, $startDate, $endDate, $auditType){
        $info_plays = FALSE;
        utils::log($this->idUser, "logAlex");
        $startDateTime = DateTime::createFromFormat('m/d/Y H:i:s', $startDate . ' 00:00:00');
        $endDateTime = DateTime::createFromFormat('m/d/Y H:i:s', $endDate . ' 23:59:59');
        
        if($auditType == 3){
            $info_plays = $this->RepdcModel->getYearSummaryReportByPb($idBooth, $this->idUser, $startDateTime, $endDateTime);
        }elseif($auditType == 4){ //nomes per a consultes de versio en array_info buit
            $info_plays = $this->RepdcModel->getVersionArrayByPb($idBooth, $this->idUser, $startDateTime, $endDateTime);
            
        }
        else{
            $info_plays = $this->RepdcModel->getSummaryReportByPb($idBooth, $this->idUser, $startDateTime, $endDateTime);
        }
        
        return $info_plays;
    }
    
    public function getHoursOperation($idPb, $startDate, $endDate, $auditType){
        $startDateTime = DateTime::createFromFormat('m/d/Y H:i:s', $startDate . ' 00:00:00');
        $endDateTime = DateTime::createFromFormat('m/d/Y H:i:s', $endDate . ' 23:59:59');
        
//        if($auditType == RepdcManager::YEARLY)
//            $result = $this->RepdcModel->getInfoSessionsByMonth($idPb, $this->idUser);
//        else
            $result = $this->RepdcModel->getInfoSessionsByDay($idPb, $this->idUser);
        
        $d1 = FALSE;
        $d2 = FALSE;
        $intervals_array = array();
//        print "<pre>";
//        print_r($result);
        foreach($result as $startStop){
            if($startStop['typeInfo'] == 50){
                $d1 = DateTime::createFromFormat('Y-m-d H:i:s', $startStop['pbs_time']);
            } else if($startStop['typeInfo'] == 60){
                if($d1){
                    $d2 = DateTime::createFromFormat('Y-m-d H:i:s', $startStop['pbs_time']);

                    while($d1 < $d2){
                        $diff = 0;

                        if($d1->format('Y-m-d') == $d2->format('Y-m-d')){
                            $to_d = clone($d2);                                
                        } else {
                            $to_d = clone($d1);
                            $to_d->setTime(23,59,59);
                        }

                        $partial_interval = $d1->diff($to_d);
                        $diff_hours = $partial_interval->h;
                        $diff_minutes = $partial_interval->i;
                        if($auditType == RepdcManager::YEARLY){
                            $diff_date = $d1->format('Y-m');
                        }else{
                            $diff_date = $d1->format('Y-m-d');
                        }
                        

                        if(array_key_exists($diff_date, $intervals_array)) {
                            $currentInterval = $intervals_array[$diff_date];

                            list($diff_hours, $diff_minutes) = utils::sumHours($diff_hours, $diff_minutes, $currentInterval['hours'], $currentInterval['minutes']);
                        }

                        $diff_time = utils::printHours($diff_hours, $diff_minutes);
                        
                        $intervals_array[$diff_date] = array('time' => $diff_time, 'hours' => $diff_hours, 'minutes' => $diff_minutes);        

                        $d1 = $to_d->modify('+1 second');
                    }
                }
                $d1 = FALSE;
            }
        }
        
        if($d1){
            //Igualar $to_d a lastConn del PB, fer query.
            $lastConn = $this->RepdcModel->getPbLastConnection_appBooths($idPb);
            if($lastConn){
                $d2 = DateTime::createFromFormat('Y-m-d H:i:s', $lastConn);
                while($d1 < $d2){

                    if($d1->format('Y-m-d') == $d2->format('Y-m-d')){
                        $to_d = clone($d2);
                    } else {
                        $to_d = clone($d1);
                        $to_d->setTime(23,59,59);
                    }

                    $partial_interval = $d1->diff($to_d);
                    $diff_hours = $partial_interval->h;
                    $diff_minutes = $partial_interval->i;
                     if($auditType == RepdcManager::YEARLY){
                        $diff_date = $d1->format('Y-m');
                    }else{
                        $diff_date = $d1->format('Y-m-d');
                    }

                    if(array_key_exists($d1->format('Y-m-d'), $intervals_array)) {
                        $currentInterval = $intervals_array[$diff_date];
                        
                        list($diff_hours, $diff_minutes) = utils::sumHours($diff_hours, $diff_minutes, $currentInterval['hours'], $currentInterval['minutes']);
                    }
                    $diff_time = utils::printHours($diff_hours, $diff_minutes);
                    
                    $intervals_array[$diff_date] = array('time' => $diff_time, 'hours' => $diff_hours, 'minutes' => $diff_minutes);        

                    $d1 = $to_d->modify('+1 second');
                }
            }
        }
//        print_r($intervals_array);exit;
        return $intervals_array;
    }
    
    public function Repdc_activity($idBooth, $startDateTime, $endDateTime){
        $html = "";
        
        $activitys = $this->RepdcModel->getReapDcActibity($idBooth, $startDateTime, $endDateTime);
        
        $minPBversion = $this->checkPBVersion($activitys);
        
        $overpayment_row = "";
        if($minPBversion >= 21){
            $overpayment_row = "<td style='border-top:#000 solid 2px;font-weight:bold;border-bottom:#000 solid 1px;'>Overpayment</td>";
        }
        
        $html.= "
        <table border='0' cellpadding='5' cellspacing='0'>
            <tr>
                <td colspan='9' style='font-weight: bold;'>Activity report</td>
            </tr>
            <tr>
                <td style='border-top:#000 solid 2px;font-weight:bold;border-bottom:#000 solid 1px;' colspan='2'>Date time</td>
                <td style='border-top:#000 solid 2px;font-weight:bold;border-bottom:#000 solid 1px;'>&nbsp;</td>
                <td style='border-top:#000 solid 2px;font-weight:bold;border-bottom:#000 solid 1px;'>Cash</td>
                <td style='border-top:#000 solid 2px;font-weight:bold;border-bottom:#000 solid 1px;'>Card</td>
                <td style='border-top:#000 solid 2px;font-weight:bold;border-bottom:#000 solid 1px;'>Net</td>
                <td style='border-top:#000 solid 2px;font-weight:bold;border-bottom:#000 solid 1px;'>Total</td>
                {$overpayment_row}
                <td style='border-top:#000 solid 2px;font-weight:bold;border-bottom:#000 solid 1px;'>Stock</td>
            </tr>
        ";
        
        
        
        $lastData = 0;
        
        if($activitys){
            foreach($activitys as $activity){
                $quan = $activity["when"];
                $tipus = $activity["typeInfo"];
                $i1 = $activity["i1"];
                $descr = $activity["Product"];

                $data = intval($quan->format("Ymd"));
                $temps = $quan->format("h:i A");
                $text = "&nbsp;";
                
                $stock      = ($activity['stock'])  ? $activity['stock'] : "0";
                $Cash       = ($activity['Cash'])   ? $activity['Cash'] : "0";
                $Card       = ($activity['Card'])   ? $activity['Card'] : "0";
                $Net        = ($activity['Net'])    ? $activity['Net'] : "0";
                $Money      = ($activity['money_']) ? $activity['money_'] : "0";
                
                if($minPBversion >= 21){
                    $Overpayment = (intval($Cash) + intval($Card) + intval($Net)) - intval($Money);
                    if($Overpayment<0){
                         
                        $sumaOver = -$Overpayment;
                        $Overpayment = 0;
                        $Card = $Card + $sumaOver;
                    }
                }
                $Money       = intval($Card) + intval($Net) + intval($Money);
                
                $PBnew = $activity['PBnew'];
                
                if($tipus != 50){
                    $currPosition = $activity['currency_position'];
                    $currSymbol = $activity['currency_symbol'];
                    
                    $Cash = utils::putCurrency($Cash,$currSymbol,$currPosition);
                    $Card = utils::putCurrency($Card,$currSymbol,$currPosition);
                    $Net = utils::putCurrency($Net,$currSymbol,$currPosition);
                    $Money = utils::putCurrency($Money, $currSymbol, $currPosition);
                    
                    if($minPBversion >= 21){
                        $Overpayment = utils::putCurrency($Overpayment, $currSymbol, $currPosition);
                    }
                }
                
                if($data > $lastData){
                    $html.="<tr>";
                    $html.="<td colspan='2' style='font-weight:bold;'>{$quan->format("m-d-Y")}</td>";
                    $html.="</tr>";

                     $lastData = $data;
                }

                switch($tipus){
                    case 10:
                        $text = "Play: $descr";
                        break;
                    case 20:
                        $text = "Cash Collection";
                        $Overpayment = 0;
                        break;
                    case 40:
                        switch($i1){
                            case 1:
                                $text = "Printer Error";
                                break;
                            case 2:
                                $text = "Paper Error";
                                break;
                            case 3:
                                $text = "I/O Board Error";
                                break;
                            case 4:
                                $text = "Camera Error";
                                break;
                            }
                        break;

                    case 50:
                        $text = "Session Start";
                        $stock = "-";
                        $Cash  = "-";
                        $Card  = "-";
                        $Net   = "-";
                        $Money = "-";
                        $Overpayment = "-";
                        break;
                    case 60:
                        $text = "Session End";
                        $stock = "-";
                        break;
                }
                
                $overpayment_row = "";
                if($minPBversion >= 21){
                    $overpayment_row = "<td style='text-align:center;'>{$Overpayment}</td>";
                }
               
                $html.="
                <tr>
                    <td style='text-align:center;'>&nbsp;</td>
                    <td style='text-align:center;'>$temps</td>
                    <td style='text-align:center;'>$text</td> 
                    <td style='text-align:center;'>{$Cash}</td>
                    <td style='text-align:center;'>{$Card}</td>       
                    <td style='text-align:center;'>{$Net}</td>       
                    <td style='text-align:center;'>{$Money}</td> 
                    {$overpayment_row}
                    <td style='text-align:center;'>{$stock}</td>
                </tr>
                ";
            }
        }
        $html.="<tr>";
        $html.="<td colspan='9' style='border-bottom:#000 solid 2px;'>&nbsp;</td>";
        $html.="</tr>";
        $html.="</table>";
        
        return $html;

    }
    
    public function Repdc_moneyByPayment($idBooth,$startDateTime,$endDateTime,$currency,$currSymbol,$currPosition, $cashFromMoney = true){
        $html = "";

        $payments = $this->RepdcModel->moneyByPayment($idBooth, $startDateTime, $endDateTime, $currency);
        
        $Cash   = $payments[0]["Cash"];
        $Card   = $payments[0]["Card"];
        $Net    = $payments[0]["Net"];
        $Money  = $payments[0]["Money"];
        $Money  = $Cash + $Card + $Net;
        
        if($cashFromMoney) $Cash = $Money - $Card - $Net;
        
        $CashC = utils::putCurrency($Cash,$currSymbol,$currPosition);
        $CardC = utils::putCurrency($Card,$currSymbol,$currPosition);
        $NetC = utils::putCurrency($Net,$currSymbol,$currPosition);
        
        $TotalC = utils::putCurrency(intval($Cash) + intval($Card) + intval($Net), $currSymbol, $currPosition);
        
        $startDateTime_lifeTime = DateTime::createFromFormat('m/d/Y H:i:s', '01/01/2000 00:00:00');
        $payments_lifeTime = $this->RepdcModel->moneyByPayment($idBooth, $startDateTime_lifeTime, $endDateTime, $currency);
        
        $Cash_lifeTime   = $payments_lifeTime[0]["Cash"];
        $Card_lifeTime   = $payments_lifeTime[0]["Card"];
        $Net_lifeTime    = $payments_lifeTime[0]["Net"];
        $Money_lifeTime  = $payments_lifeTime[0]["Money"];
        $Money_lifeTime  = $Cash_lifeTime + $Card_lifeTime + $Net_lifeTime;
        
        if($cashFromMoney) $Cash_lifeTime = $Money_lifeTime - $Card_lifeTime - $Net_lifeTime;
        
        $CashC_lifeTime = utils::putCurrency($Cash_lifeTime,$currSymbol,$currPosition);
        $CardC_lifeTime = utils::putCurrency($Card_lifeTime,$currSymbol,$currPosition);
        $NetC_lifeTime = utils::putCurrency($Net_lifeTime,$currSymbol,$currPosition);
        
        $TotalC_lifeTime = utils::putCurrency(intval($Cash_lifeTime) + intval($Card_lifeTime) + intval($Net_lifeTime), $currSymbol, $currPosition);
        
        

        $html.= "
        <table width='400' border='0' cellpadding='1' cellspacing='0' >
            <tr>
                <td colspan='3' style='border-bottom:#000 solid 1px;font-weight:bold;'>Income by payment mode</td>
            </tr>
            <tr>
                <td></td>
                <td colspan='2' style='border-bottom:#000 solid 1px;font-weight:bold;' align='center'>Money</td>
            </tr>
            <tr>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Payment mode</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Current</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Lifetime</td>
            </tr>
            <tr>
                <td >Cash</td>
                <td align='center'>{$CashC}</td>
                <td align='center'>{$CashC_lifeTime}</td>
            </tr>
            <tr>
                <td>Card</td>
                <td align='center'>{$CardC}</td>
                <td align='center'>{$CardC_lifeTime}</td>
            </tr>
                <tr>
                <td>Net</td>
                <td align='center'>{$NetC}</td>
                <td align='center'>{$NetC_lifeTime}</td>
            </tr>
            <tr style=''>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 1px; font-weight:bold;'>
                    Total
                </td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 1px;font-weight:bold;' align='center'>
                    {$TotalC}
                </td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 1px;font-weight:bold;' align='center'>
                    {$TotalC_lifeTime}
                </td>
            </tr>
        </table>
        <br/>
        ";
        
        return $html;
    }
    
    public function Repdc_moneyByProduct($idBooth,$startDateTime, $endDateTime, $currency, $currSymbol, $currPosition){
        $html = "";
        
        $html .= "
        <table width='400' border='0' cellpadding='1' cellspacing='0' >
            <tr>
                <td colspan='5' style='border-bottom:#000 solid 1px;font-weight:bold;'>Money by product in play mode</td>
            </tr>
            <tr>
                <td colspan='3'></td>
                <td colspan='2' style='border-bottom:#000 solid 1px;font-weight:bold;' align='center'>Money</td>
            </tr>
            <tr>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Product</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Plays</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Prints</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Current</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Lifetime</td>
            </tr>
        ";
        
        $products = $this->RepdcModel->getProducts($idBooth, $startDateTime, $endDateTime, $currency);
        
        /*
        06/09/2017 S'ha vist que no té sentit la columna lifetime. Ja que s'hauria de duplicar les columnes('Plays','Prints'). 	
        */

        $startDateTime_lifeTime = DateTime::createFromFormat('m/d/Y H:i:s', '01/01/2000 00:00:00');
        $products_lifeTime = $this->RepdcModel->getProducts($idBooth, $startDateTime_lifeTime, $endDateTime, $currency);
        
        $totalPlays = 0;
        $totalMoney = 0;
        $i = 0;
        foreach ($products as $product){
            $Prod = $product["Product"];
            $Plays = $product["Play"];
            $Money = $product["Money"];
            $Prints = $product["Prints"];
            $lifeTimeMoney = $products_lifeTime[$i]['Money'];
            
            if(!$Plays) $Plays = 0;
            if(!$Prints) $Prints = 0;
            
            $MoneyC = utils::putCurrency($Money,$currSymbol,$currPosition);
            $lifeTimeMoneyC = utils::putCurrency($lifeTimeMoney,$currSymbol,$currPosition);
            
            $html .= "  
            <tr>
                <td>$Prod</td>
                <td align='center'>$Plays</td>
                <td align='center'>$Prints</td>
                <td align='center'>{$MoneyC}</td>
                <td align='center'>{$lifeTimeMoneyC}</td>
            </tr>
            ";

            $totalPlays         += $Plays;
            $totalPrints        += $Prints;
            $totalMoney         += $Money;
            $totalLifeTimeMoney += $lifeTimeMoney;
            $i++;
        }
        
        $otherProducts = $this->RepdcModel->getTotalitzacioProducts($idBooth, $startDateTime, $endDateTime, $currency);
        
        $startDateTime_lifeTime = DateTime::createFromFormat('m/d/Y H:i:s', '01/01/2000 00:00:00');
        $otherProducts_lifetime = $this->RepdcModel->getTotalitzacioProducts($idBooth, $startDateTime_lifeTime, $endDateTime, $currency);
        
        if($otherProducts){
            $i = 0;
            foreach ($otherProducts as $otherProduct){
                $Plays  = $otherProduct["Play"];
                $Money  = $otherProduct["Money"];
                $Prints = $otherProduct["Prints"];
                $lifeTimeMoney = $otherProducts_lifetime[$i]["Money"];

                if(!$Plays) $Plays = 0;
                if($Plays > 0){
                    $MoneyC = utils::putCurrency($Money,$currSymbol,$currPosition);
                    $lifeTimeMoneyC = utils::putCurrency($lifeTimeMoney,$currSymbol,$currPosition);
                    $html .= " 
                        <tr>
                            <td>Other products</td>
                            <td align='center'>$Plays</td>
                            <td align='center'>$Prints</td>
                            <td align='center'>{$MoneyC}</td>
                            <td align='center'>{$lifeTimeMoneyC}</td>
                        </tr>
                    ";

                    $totalPlays         += $Plays;
                    $totalPrints       += $Prints;
                    $totalMoney         += $Money;
                    $totalLifeTimeMoney += $lifeTimeMoney;
                }
                $i++;
            }
        }
        
        $totalMoneyC         = utils::putCurrency($totalMoney,$currSymbol,$currPosition);
        $totalLifeTimeMoneyC = utils::putCurrency($totalLifeTimeMoney,$currSymbol,$currPosition);
        
        $html .= "
            <tr>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;' align='left'>Total</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;' align='center'>$totalPlays</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;' align='center'>$totalPrints</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;' align='center'>{$totalMoneyC}</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;' align='center'>{$totalLifeTimeMoneyC}</td>
            </tr>
        </table>
        ";
                
        return $html;
    }
    
    public function Repdc_freeplaysByProduct($idBooth,$startDateTime,$endDateTime){
        $ret_html = "";
        
        $ret_html.= "
        <table width='300' border='0' cellpadding='1' cellspacing='0' >
            <tr>
              <td style='border-bottom:#000 solid 1px;font-weight:bold;' colspan='3'>Free plays by product</td>
            </tr>
            <tr>
              <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Product</td>
              <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Plays</td>
              <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Prints</td>
            </tr>
        ";
        
        $freeplays = $this->RepdcModel->getfreeplays($idBooth, $startDateTime, $endDateTime);
        
        if($freeplays){
            $totalPlays = 0;
            foreach ($freeplays as $freeplay){
                
                $Prod  = $freeplay["Product"];
                $plays = $freeplay["Plays"];
                $prints = $freeplay ["Prints"];
                
                if(!$plays) $plays = "0";
                if(!$prints) $prints = "0";
                
                $ret_html.= " 
                <tr>
                    <td>$Prod</td>
                    <td align='center'>$plays</td>
                    <td align='center'>$prints</td>
                </tr>
                ";
                $totalPlays+= $plays;
                $totalPrints+= $prints;
            }
        }
        
        $otherProducts = $this->RepdcModel->getOtherProducts($idBooth, $startDateTime, $endDateTime);
        
        if($otherProducts){
            $othePlays = $otherProducts[0]["myPlays"];
            $otherPrins = $otherProducts[0]["prints"];
            
            if($othePlays > 0){
                $ret_html.= "  
                <tr>
                    <td>Other products</td>
                    <td align='center'>$othePlays</td>
                    <td align='center'>$otherPrins</td>
                </tr>
                ";
                $totalPlays  += $othePlays;
                $totalPrints += $otherPrins;
            }
        }
        
        $ret_html.= "
            <tr>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;'>Total</strong></td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;' align='center'>$totalPlays</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;' align='center'>$totalPrints</td>
            </tr>
        </table>
            ";
        return $ret_html;
    }
    
    public function RepdcSummary($idBooth, $startDateTime, $endDateTime){
        $html = "";
        
        $html .= "
        <table width='300' border='0' cellpadding='1' cellspacing='0' >
            <tr>
                <td colspan='3'>&nbsp;</td>
            </tr>
            <tr>
              <td style='border-bottom:#000 solid 1px;font-weight:bold;' colspan='3'>Summary</td>
            </tr>
            <tr>
              <td style='border-bottom:#000 solid 1px;font-weight:bold;'></td>
              <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Current</td>
              <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Lifetime</td>
            </tr>
        ";

        $summary_current = $this->RepdcModel->getMailSummary($idBooth, $startDateTime, $endDateTime);
        
        $plays = $summary_current[0]["plays"];
        $freeplays = $summary_current[0]["freeplays"];
        $prints = $summary_current[0]["prints"];
        $money = $summary_current[0]["money"];
        $overpayment = intval($summary_current[0]["overpayment"]);
        $errors = $summary_current[0]["errors"];
        $stock = $summary_current[0]["stock"];
        
        $startDateTime_lifeTime = DateTime::createFromFormat('m/d/Y H:i:s', '01/01/2000 00:00:00');
        $summary_lifeTime = $this->RepdcModel->getMailSummary($idBooth, $startDateTime_lifeTime, $endDateTime);
        
        $lifetime_plays = $summary_lifeTime[0]["plays"];
        $lifetime_freeplays = $summary_lifeTime[0]["freeplays"];
        $lifetime_prints = $summary_lifeTime[0]["prints"];
        $lifetime_money = $summary_lifeTime[0]["money"];
        $lifetime_overpayment = intval($summary_lifeTime[0]["overpayment"]);
        $lifetime_errors = $summary_lifeTime[0]["errors"];
        
        $html .= "
            <tr>
                <td >Plays</td>
                <td >$plays</td>
                <td >$lifetime_plays</td>
            </tr>
            <tr>
                <td >FreePlays</td>
                <td >$freeplays</td>
                <td >$lifetime_freeplays</td>
            </tr>
            <tr>
                <td >Prints</td>
                <td >$prints</td>
                <td >$lifetime_prints</td>
            </tr>
            <tr>
                <td >Money</td>
                <td >$money</td>
                <td >$lifetime_money</td>
            </tr>
            <tr>
                <td >Overpayment</td>
                <td >$overpayment</td>
                <td >$lifetime_overpayment</td>
            </tr>
            <tr>
                <td >Errors</td>
                <td >$errors</td>
                <td >$lifetime_errors</td>
            </tr>
            <tr>
                <td style='border-bottom:#000 solid 1px;' >Paper stock</td>
                <td style='border-bottom:#000 solid 1px;' >$stock</td>
                <td style='border-bottom:#000 solid 1px;' >-</td>
            </tr>
            <tr>
                <td colspan='3'>&nbsp;</td>
            </tr>
        </table>
        <hr />
        ";
        
        return $html;
        
    }
    
    public function DailyReport($idBooth, $idOwner, $startDateTime, $endDateTime){
        $html = "";
        $i = 1;
        $z = 0;
        $currencyChange = 0;
        
        $auditsCounter = $this->RepdcModel->getAuditLastConection($idOwner, $idBooth);
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
        $totalTimeOperation = "0:00";
        
        $array_info = $this->RepdcModel->getSummaryReportByPb($idBooth, $idOwner, $startDateTime, $endDateTime);

        $str_startDateTime = $startDateTime->format('m/d/Y');
        $str_endDateTime = $endDateTime->format('m/d/Y');
        $hoursOperationArray = $this->getHoursOperation($idBooth, $str_startDateTime, $str_endDateTime, $this->reportType);
        
        $minPBversion = $this->checkPBVersion($array_info);
        
        $hoursOperation_row = "";
        $overpayment_row = "";
        if($minPBversion >= 21){
            $hoursOperation_row = "<td style='border-bottom:#000 solid 1px;font-weight:bold;'>Hours Operation</td>";
            $overpayment_row = "<td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Overpayment</td>";
        }
        
        $html .= "
        <table border='0' cellpadding='1' cellspacing='0' >
            <tr>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;' colspan='12'><strong>Daily report</strong></td>
            </tr>
            <tr>
                <td colspan='6'>&nbsp;</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;text-align: center;' colspan='5'>Money</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Date</td>
                {$hoursOperation_row}
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Plays</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>FreePlays</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Upsells</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Prints</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Cash</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Card</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Net</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Total</td>
                {$overpayment_row}
                <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Stock</td>
            </tr>
        ";
                
        $startDateTime_DailyReport = clone($startDateTime);
        while ($startDateTime_DailyReport <= $endDateTime) {
            $day = $startDateTime_DailyReport->format('m-d-Y');
            $day_ = $startDateTime_DailyReport->format('Y-m-d');
            
            //La maquina podria haver estat encesa i no tenir activitat igualment
            $hoursOperation = "-";
            $minutesOperation = "-";
            $hoursOperation_time = "-";
            if(isset($hoursOperationArray)){
                $hoursOperation = "0";
                $minutesOperation = "0";
                $hoursOperation_time = "0:00";
                if(isset($hoursOperationArray[$day_])){
                    $hoursOperation = $hoursOperationArray[$day_]['hours'];
                    $minutesOperation = $hoursOperationArray[$day_]['minutes'];
                    $hoursOperation_time = $hoursOperationArray[$day_]['time'];
                }
            }
            
            list($totalHoursOperation, $totalMinutesOperation) = utils::sumHours($totalHoursOperation, $totalMinutesOperation, $hoursOperation, $minutesOperation);
            
            // Overpayment and HoursOperation Rows
            $hoursOperation_empty = "";
            $overpayment_empty = "";
            $hoursOperation_unknown = "";
            $overpayment_unknown = "";
            if($minPBversion >= 21){
                $hoursOperation_empty = "<td style='text-align:center;padding:5px;'>{$hoursOperation_time}h</td>";
                $overpayment_empty = "<td style='text-align:center;padding:5px;'>0</td>";
                $hoursOperation_unknown = "<td style='text-align:center;padding:5px;'>-</td>";
                $overpayment_unknown = "<td style='text-align:center;padding:5px;'>-</td>";
            }

            $empty_htmldata = <<<HTML
                {$hoursOperation_empty}
                <td style='text-align:center;padding:5px;'>0</td>
                <td style='text-align:center;padding:5px;'>0</td>
                <td style='text-align:center;padding:5px;'>0</td>
                <td style='text-align:center;padding:5px;'>0</td>
                <td style='text-align:center;padding:5px;'>0</td>
                <td style='text-align:center;padding:5px;'>0</td>
                <td style='text-align:center;padding:5px;'>0</td>
                <td style='text-align:center;padding:5px;'>0</td>
                {$overpayment_empty}
                <td style='text-align:center;padding:5px;'>-</td>
HTML;

            $unknown_htmldata = <<<HTML
                {$hoursOperation_unknown}
                <td style='text-align:center;padding:5px;'>-</td>
                <td style='text-align:center;padding:5px;'>-</td>
                <td style='text-align:center;padding:5px;'>-</td>
                <td style='text-align:center;padding:5px;'>-</td>
                <td style='text-align:center;padding:5px;'>-</td>
                <td style='text-align:center;padding:5px;'>-</td>
                <td style='text-align:center;padding:5px;'>-</td>
                <td style='text-align:center;padding:5px;'>-</td>
                {$overpayment_unknown}
                <td style='text-align:center;padding:5px;'>-</td>
HTML;
            
            if($lastInfo > $startDateTime_DailyReport){
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
                    $freePlays   = ($array_info[$day_]['freePlays']) ? $array_info[$day_]['freePlays'] : "0";
                    $plays       = $this->calcPlays($array_info[$day_]['plays'], $freePlays);
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
                    $collection         =   $array_info[$day_]['collection'];                        
                        
                    $currency = $array_info[$day_]['currency_symbol'];
                    $currency_postition =  $array_info[$day_]['currency_position'];
                    
                    $totalPrints      += intval($prints);
                    $totalPlays       += intval($plays);
                    $totalFreePlays   += intval($freePlays);
                    $totalUpsells     += intval($upsells);
                    $totalCash        += intval($cash);
                    $totalCreditCard  += intval($creditCard);
                    $totalNet         += intval($net);
                    $totalmoney       += intval($money);
                    $totalOverpayment += intval($overpayment);
                   
                    $cash = utils::putCurrency($cash, $currency, $currency_postition);
                    $creditCard = utils::putCurrency($creditCard, $currency, $currency_postition);
                    $net = utils::putCurrency($net, $currency, $currency_postition);
                    $money = utils::putCurrency($money, $currency, $currency_postition);
                    $overpayment = utils::putCurrency($overpayment, $currency, $currency_postition);
                    
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
                        $hoursOperation_row = "<td style='text-align:center;'>{$hoursOperation_time}h</td>";
                        $overpayment_row = "<td style='text-align:center;padding:5px;'>{$overpayment}</td>";
                    }

                    $htmldata_ = <<<HTML
                        {$hoursOperation_row}
                        <td style='text-align:center;padding:5px;'>{$plays}</td>
                        <td style='text-align:center;padding:5px;'>{$freePlays}</td>
                        <td style='text-align:center;padding:5px;'>{$upsells}</td>
                        <td style='text-align:center;padding:5px;'>{$prints}</td>
                        <td style='text-align:center;padding:5px;'>{$cash}</td>
                        <td style='text-align:center;padding:5px;'>{$creditCard}</td>
                        <td style='text-align:center;padding:5px;'>{$net}</td>
                        <td style='text-align:center;padding:5px;'>{$money}</td>
                        {$overpayment_row}
                        <td style='text-align:center;padding:5px;'>{$stock}</td>
HTML;
                        
                    $z++;
                }  
            }
            
            $html .= $htmldata_;
            $html .="</tr>";
            
            
            $i++;
            $startDateTime_DailyReport->modify("+1 day");
        }
        
        if($currencyChange == 0){
            $totalCash = utils::putCurrency($totalCash, $currency, $currency_postition);
            $totalCreditCard = utils::putCurrency($totalCreditCard, $currency, $currency_postition);
            $totalNet = utils::putCurrency( $totalNet, $currency, $currency_postition);
            $totalmoney = utils::putCurrency($totalmoney, $currency, $currency_postition);   
            $totalOverpayment = utils::putCurrency($totalOverpayment, $currency, $currency_postition); 
        }
        
        $totalTimeOperation = utils::printHours($totalHoursOperation, $totalMinutesOperation);
        $totalPrints = ($totalPrints)? $totalPrints : "0";
        $totalPlays = ($totalPlays)? $totalPlays : "0";
        $totalFreePlays = ($totalFreePlays)? $totalFreePlays : "0";
        $totalUpsells = ($totalUpsells)? $totalUpsells : "0";

        $totalHoursOperation_row = "";
        $totalOverpayment_row = "";
        if($minPBversion >= 21){
            $totalHoursOperation_row = "<td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>{$totalTimeOperation}h</td>";
            $totalOverpayment_row = "<td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>{$totalOverpayment}</td>";
        }
        
        $TotalStock_row = "";
        $TotalCollection_rows = "";
        if($idPb){
            $TotalStock_row .= "<td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;></td>";
            $TotalCollection_rows .= "<td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'></td><td class='no-color'></td>";
        }
        
        $html .= <<<HTML
            <tr class='RowTotal'>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;'>Total</td>
                {$totalHoursOperation_row}
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalPlays</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalFreePlays</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalUpsells</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalPrints</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalCash</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalCreditCard</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalNet</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalmoney</td>
                {$totalOverpayment_row}
                {$TotalStock_row}
                {$TotalCollection_rows}
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'></td>
            </tr>
        </table>
        </br>
HTML;
        
        return $html;
    }
    
    public function MonthlyReport($idBooth, $idOwner, $startDateTime, $endDateTime){
        $html = "";
        $i = 1;
        $z = 0;
        $currencyChange = 0;
        
        $auditsCounter = $this->RepdcModel->getAuditLastConection($idOwner, $idBooth);
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
        $totalTimeOperation = "0:00";
        
        $array_info = $this->RepdcModel->getYearSummaryReportByPb($idBooth, $idOwner, $startDateTime, $endDateTime);
        
        if($str_startDateTime)$str_startDateTime = $startDateTime->format('m/d/Y');
        if($str_endDateTime)$str_endDateTime = $endDateTime->format('m/d/Y');
          
        $hoursOperationArray = $this->getHoursOperation($idBooth, $str_startDateTime, $str_endDateTime, $this->reportType);
        
        $minPBversion = $this->checkPBVersion($array_info);
        
        $hoursOperation_row = "";
        $overpayment_row = "";
        if($minPBversion >= 21){
            $hoursOperation_row = "<td style='border-bottom:#000 solid 1px;font-weight:bold;'>Hours Operation</td>";
            $overpayment_row = "<td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Overpayment</td>";
        }
        
        $html .= "
        <table border='0' cellpadding='1' cellspacing='0' >
            <tr>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;' colspan='12'><strong>Month report</strong></td>
            </tr>
            <tr>
                <td colspan='6'>&nbsp;</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;text-align: center;' colspan='5'>Money</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Date</td>
                {$hoursOperation_row}
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Plays</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>FreePlays</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Upsells</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Prints</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Cash</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Card</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Net</td>
                <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;'>Total</td>
                {$overpayment_row}
                <td style='border-bottom:#000 solid 1px;font-weight:bold;'>Stock</td>
            </tr>
        ";
                
        $startDateTime_MonthlyReport = clone($startDateTime);
        while ($startDateTime_MonthlyReport <= $endDateTime) {
            $day = $startDateTime_MonthlyReport->format('F Y');
            $day_ = $startDateTime_MonthlyReport->format('Y-m');
            
            $month_ = $startDateTime_MonthlyReport->format('m');
            $year_ = $startDateTime_MonthlyReport->format('Y');
            
            //La maquina podria haver estat encesa i no tenir activitat igualment
            $hoursOperation = "-";
            $minutesOperation = "-";
            $hoursOperation_time = "-";
            if(isset($hoursOperationArray)){
                $hoursOperation = "0";
                $minutesOperation = "0";
                $hoursOperation_time = "0:00";
                if(isset($hoursOperationArray[$day_])){
                    $hoursOperation = $hoursOperationArray[$day_]['hours'];
                    $minutesOperation = $hoursOperationArray[$day_]['minutes'];
                    $hoursOperation_time = $hoursOperationArray[$day_]['time'];
                }
            }
            
            list($totalHoursOperation, $totalMinutesOperation) = utils::sumHours($totalHoursOperation, $totalMinutesOperation, $hoursOperation, $minutesOperation);
            
            // Overpayment and HoursOperation Rows
            $hoursOperation_empty = "";
            $overpayment_empty = "";
            $hoursOperation_unknown = "";
            $overpayment_unknown = "";
            if($minPBversion >= 21){
                $hoursOperation_empty = "<td style='text-align:center;padding:5px;'>{$hoursOperation_time}h</td>";
                $overpayment_empty = "<td style='text-align:center;padding:5px;'>0</td>";
                $hoursOperation_unknown = "<td style='text-align:center;padding:5px;'>-</td>";
                $overpayment_unknown = "<td style='text-align:center;padding:5px;'>-</td>";
            }

            $empty_htmldata = <<<HTML
                {$hoursOperation_empty}
                <td style='text-align:center;padding:5px;'>0</td>
                <td style='text-align:center;padding:5px;'>0</td>
                <td style='text-align:center;padding:5px;'>0</td>
                <td style='text-align:center;padding:5px;'>0</td>
                <td style='text-align:center;padding:5px;'>0</td>
                <td style='text-align:center;padding:5px;'>0</td>
                <td style='text-align:center;padding:5px;'>0</td>
                <td style='text-align:center;padding:5px;'>0</td>
                {$overpayment_empty}
                <td style='text-align:center;padding:5px;'>-</td>
HTML;

            $unknown_htmldata = <<<HTML
                {$hoursOperation_unknown}
                <td style='text-align:center;padding:5px;'>-</td>
                <td style='text-align:center;padding:5px;'>-</td>
                <td style='text-align:center;padding:5px;'>-</td>
                <td style='text-align:center;padding:5px;'>-</td>
                <td style='text-align:center;padding:5px;'>-</td>
                <td style='text-align:center;padding:5px;'>-</td>
                <td style='text-align:center;padding:5px;'>-</td>
                <td style='text-align:center;padding:5px;'>-</td>
                {$overpayment_unknown}
                <td style='text-align:center;padding:5px;'>-</td>
HTML;
            
            if($lastInfo > $startDateTime_MonthlyReport){
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
                        $freePlays   = ($array_info[$day_]['freePlays']) ? $array_info[$day_]['freePlays'] : "0";
                        $plays       = $this->calcPlays($array_info[$day_]['plays'], $freePlays);
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
                        
                        $collection         =   $array_info[$day_]['collection'];                        

                        $currency = $array_info[$day_]['currency_symbol'];
                        $currency_postition =  $array_info[$day_]['currency_position'];

                        $totalPrints      += intval($prints);
                        $totalPlays       += intval($plays);
                        $totalFreePlays   += intval($freePlays);
                        $totalUpsells     += intval($upsells);
                        $totalCash        += intval($cash);
                        $totalCreditCard  += intval($creditCard);
                        $totalNet         += intval($net);
                        $totalmoney       += intval($money);
                        $totalOverpayment += intval($overpayment);

                        $cash = utils::putCurrency($cash, $currency, $currency_postition);
                        $creditCard = utils::putCurrency($creditCard, $currency, $currency_postition);
                        $net = utils::putCurrency($net, $currency, $currency_postition);
                        $money = utils::putCurrency($money, $currency, $currency_postition);
                        $overpayment = utils::putCurrency($overpayment, $currency, $currency_postition);

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
                            $hoursOperation_row = "<td style='text-align:center;'>{$hoursOperation_time}h</td>";
                            $overpayment_row = "<td style='text-align:center;padding:5px;'>{$overpayment}</td>";
                        }

                        $htmldata_ = <<<HTML
                            {$hoursOperation_row}
                            <td style='text-align:center;padding:5px;'>{$plays}</td>
                            <td style='text-align:center;padding:5px;'>{$freePlays}</td>
                            <td style='text-align:center;padding:5px;'>{$upsells}</td>
                            <td style='text-align:center;padding:5px;'>{$prints}</td>
                            <td style='text-align:center;padding:5px;'>{$cash}</td>
                            <td style='text-align:center;padding:5px;'>{$creditCard}</td>
                            <td style='text-align:center;padding:5px;'>{$net}</td>
                            <td style='text-align:center;padding:5px;'>{$money}</td>
                            {$overpayment_row}
                            <td style='text-align:center;padding:5px;'>{$stock}</td>
HTML;

                        $z++;
                    }
                }  
            }
            
            $html .= $htmldata_;
            $html .="</tr>";
            
            
            $i++;
            $startDateTime_MonthlyReport->modify("+1 month");
        }
        
        if($currencyChange == 0){
            $totalCash = utils::putCurrency($totalCash, $currency, $currency_postition);
            $totalCreditCard = utils::putCurrency($totalCreditCard, $currency, $currency_postition);
            $totalNet = utils::putCurrency( $totalNet, $currency, $currency_postition);
            $totalmoney = utils::putCurrency($totalmoney, $currency, $currency_postition);   
            $totalOverpayment = utils::putCurrency($totalOverpayment, $currency, $currency_postition); 
        }
        
        $totalTimeOperation = utils::printHours($totalHoursOperation, $totalMinutesOperation);
        $totalPrints = ($totalPrints)? $totalPrints : "0";
        $totalPlays = ($totalPlays)? $totalPlays : "0";
        $totalFreePlays = ($totalFreePlays)? $totalFreePlays : "0";
        $totalUpsells = ($totalUpsells)? $totalUpsells : "0";

        $totalHoursOperation_row = "";
        $totalOverpayment_row = "";
        if($minPBversion >= 21){
            $totalHoursOperation_row = "<td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>{$totalTimeOperation}h</td>";
            $totalOverpayment_row = "<td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>{$totalOverpayment}</td>";
        }
        
        $TotalStock_row = "";
        $TotalCollection_rows = "";
        if($idPb){
            $TotalStock_row .= "<td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;></td>";
            $TotalCollection_rows .= "<td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'></td><td class='no-color'></td>";
        }
        
        $html .= <<<HTML
            <tr class='RowTotal'>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;'>Total</td>
                {$totalHoursOperation_row}
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalPlays</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalFreePlays</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalUpsells</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalPrints</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalCash</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalCreditCard</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalNet</td>
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'>$totalmoney</td>
                {$totalOverpayment_row}
                {$TotalStock_row}
                {$TotalCollection_rows}
                <td style='border-top:#000 solid 1px;border-bottom:#000 solid 2px;font-weight:bold;text-align:center;'></td>
            </tr>
        </table>
        </br>
HTML;
        
        return $html;
    }
    
    public function ActivityBySession($idBooth, $idOwner, $startDateTime, $endDateTime, $lastConn){

        $html = "";
        $array_info = $this->RepdcModel->getActivityBySession($idBooth, $startDateTime, $endDateTime); 
        
        if($lastConn != "Never"){
            $lastConn = DateTime::createFromFormat('m/d/Y H:i:s', $lastConn);
        }
        
        if($this->checkPBVersion($array_info) < 21){
            $html = '';
        }
        else{
            $html .= "<p><strong>Activity</strong></p>";

            $_start_date = FALSE;
            $count_plays = 0;
            $count_errors = 0;
            $currency_symbol = FALSE;
            
            if($array_info){
                $i = 0;
                $z = 0;
                $array = array();
                foreach ($array_info as $activity){
                    $cash   = ($activity["Cash"])? $activity["Cash"] : "0";
                    $card   = ($activity["Card"])? $activity["Card"] : "0";
                    $net    = ($activity["Net"])? $activity["Net"] : "0";
                    $total  = intval($cash) + intval($card) + intval($net);
                    $money  = ($activity["money_"]) ? $activity["money_"] : "0";
                    $overPayment = $total - intval($money);
                    if($overPayment<0){
                         
                        $sumaOver = -$overPayment;
                        $overPayment = 0;
                        $creditCard = $creditCard + $sumaOver;
                    }

                    $extraPrints = ($activity["in4"]) ? $activity["in4"] : "0";
                    $playPrints  = ($activity["in8"]) ? $activity["in8"] : "0";
                    
                    if($activity["typeInfo"] == 10 || $activity["typeInfo"] == 20){
                        $stock =  ($activity["stock"])? $activity["stock"] : "0";
                    }

                    if($activity["typeInfo"] == 10){
                        $currency_position = $activity['currency_position'];
                        $currency_symbol   = $activity['currency_symbol'];

                        $count_prints += intval($extraPrints) + intval($playPrints);
                        $count_plays++;
                    }

                    if($activity["typeInfo"] == 40){
                        $count_errors++;
                    }

                    $count_cash  += intval($cash);
                    $count_card  += intval($card);
                    $count_net   += intval($net);
                    $count_total += intval($total);
                    $count_overPayment += intval($overPayment);
                    
                    if($activity["typeInfo"] == 50){
                        $_start_date = $activity["when"];
                    }

                    if($activity["typeInfo"] == 60){
                        $_end_date = $activity["when"];

                        if($_start_date == 0){
                            $_start_date = $startDateTime;
                        }

                        $hours_operation = $_start_date->diff($_end_date);
                        
                        $hours_operation_time = sprintf(
                            '%d:%02d',
                            ($hours_operation->d * 24) + $hours_operation->h,
                            $hours_operation->i,
                            $hours_operation->s
                        );

                        if($currency_symbol){
                            $count_cash  = utils::putCurrency($count_cash, $currency_symbol, $currency_position);
                            $count_card  = utils::putCurrency($count_card, $currency_symbol, $currency_position); 
                            $count_net   = utils::putCurrency($count_net, $currency_symbol, $currency_position);
                            $count_total = utils::putCurrency($count_total, $currency_symbol, $currency_position);
                            $count_overPayment = utils::putCurrency($count_overPayment, $currency_symbol, $currency_position);
                        }
                        
                        if($_start_date)$_start_date->format('m/d/Y H:i');
                        if($_end_date)$_end_date->format('m/d/Y H:i');
                        
                        $array[$i] = [
                                "StartDate"      => $_start_date,
                                "EndDate"        => $_end_date,
                                "HoursOperation" => $hours_operation_time,
                                "Errors"         => $count_errors,
                                "Plays"          => $count_plays,   
                                "Prints"         => $count_prints,
                                "Cash"           => $count_cash, 
                                "Card"           => $count_card, 
                                "Net"            => $count_net, 
                                "Total"          => $count_total,
                                "Overpayment"    => $count_overPayment,
                                "Stock"          => $stock
                        ];

                        $_start_date          = FALSE;
                        $_end_date            = 0;
                        $count_cash           = 0;
                        $count_card           = 0;
                        $count_net            = 0;
                        $count_total          = 0;
                        $hours_operation_time = 0;
                        $count_prints         = 0;
                        $count_plays          = 0;
                        $count_errors         = 0;
                        $count_overPayment    = 0;
                        $currency_symbol = FALSE;
                        $lastInfo = 0;

                        $i ++;
                    }
                    
                    $z++;
                    
                    if($z == count($array_info)){
                        if($_start_date){
                            if($lastConn > $endDateTime){$_end_date = $endDateTime;}
                            else{$_end_date = $lastConn;}
     
                            $hours_operation = $_start_date->diff($_end_date);
                            
                            $hours_operation_time = sprintf(
                                '%d:%02d',
                                ($hours_operation->d * 24) + $hours_operation->h,
                                $hours_operation->i,
                                $hours_operation->s
                            );

                            if($currency_symbol){
                                $count_cash  = utils::putCurrency($count_cash, $currency_symbol, $currency_position);
                                $count_card  = utils::putCurrency($count_card, $currency_symbol, $currency_position); 
                                $count_net   = utils::putCurrency($count_net, $currency_symbol, $currency_position);
                                $count_total = utils::putCurrency($count_total, $currency_symbol, $currency_position);
                                $count_overPayment = utils::putCurrency($count_overPayment, $currency_symbol, $currency_position);
                            }
                            
                            if($_start_date)$_start_date->format('m/d/Y H:i');
                            if($_end_date)$_end_date->format('m/d/Y H:i');
                            
                            $array[$i] = [
                                    "StartDate"      => $_start_date,
                                    "EndDate"        => $_end_date,
                                    "HoursOperation" => $hours_operation_time,
                                    "Errors"         => $count_errors,
                                    "Plays"          => $count_plays,   
                                    "Prints"         => $count_prints,
                                    "Cash"           => $count_cash, 
                                    "Card"           => $count_card, 
                                    "Net"            => $count_net, 
                                    "Total"          => $count_total,
                                    "Overpayment"    => $count_overPayment,
                                    "Stock"          => $stock
                            ];

                            $_start_date          = FALSE;
                            $_end_date            = 0;
                            $count_cash           = 0;
                            $count_card           = 0;
                            $count_net            = 0;
                            $count_total          = 0;
                            $hours_operation_time = 0;
                            $count_prints         = 0;
                            $count_plays          = 0;
                            $count_errors         = 0;
                            $count_overPayment    = 0;
                            $currency_symbol = FALSE;
                            $lastInfo = 0;

                            $i ++;
                        }
                            
                    }
                        
                }
            }
            
            $html .= "
            <table border='0' cellpadding='1' cellspacing='0' >
                <tr>
                    <td style='border-bottom:#000 solid 1px;font-weight:bold;' colspan='12'><strong>Activity report by session</strong></td>
                </tr>
                <tr>
                    <td style='border-bottom:#000 solid 1px;font-weight:bold;text-align:center;'>Date time ON </td>
                    <td style='border-bottom:#000 solid 1px;font-weight:bold;text-align:center;'>Date time OFF</td>
                    <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;text-align:center;'>Hours Operation</td>
                    <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;text-align:center;'>Errors</td>
                    <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;text-align:center;'>Plays</td>
                    <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;text-align:center;'>Prints</td>
                    <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;text-align:center;'>Cash</td>
                    <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;text-align:center;'>Card</td>
                    <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;text-align:center;'>Net</td>
                    <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;text-align:center;'>Total</td>
                    <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;text-align:center;'>Overpayment</td>
                    <td style='border-bottom:#000 solid 1px;font-weight:bold;padding:5px;text-align:center;'>Stock</td>
                </tr>
            ";

            foreach ($array as $session){            
                $html .= "
                <tr>
                    <td style='padding-right:10px;text-align:center;'>{$session["StartDate"]}</td>
                    <td style='padding:5px;text-align: center;'>{$session["EndDate"]}</td>
                    <td style='padding:5px;text-align: center;'>{$session["HoursOperation"]}h</td>
                    <td style='padding:5px;text-align: center;'>{$session["Errors"]}</td>
                    <td style='padding:5px;text-align: center;'>{$session["Plays"]}</td>
                    <td style='padding:5px;text-align: center;'>{$session["Prints"]}</td>
                    <td style='padding:5px;text-align: center;'>{$session["Cash"]}</td>
                    <td style='padding:5px;text-align: center;'>{$session["Card"]}</td>
                    <td style='padding:5px;text-align: center;'>{$session["Net"]}</td>
                    <td style='padding:5px;text-align: center;'>{$session["Total"]}</td>
                    <td style='padding:5px;text-align: center;'>{$session["Overpayment"]}</td>
                    <td style='padding:5px;text-align: center;'>{$session["Stock"]}</td>
                </tr>
                ";
            }

            $html .= "
                <tr>
                    <td style='border-top:#000 solid 1px;'>&nbsp</td>
                    <td style='border-top:#000 solid 1px;'>&nbsp</td>
                    <td style='border-top:#000 solid 1px;'>&nbsp</td>
                    <td style='border-top:#000 solid 1px;'>&nbsp</td>
                    <td style='border-top:#000 solid 1px;'>&nbsp</td>
                    <td style='border-top:#000 solid 1px;'>&nbsp</td>
                    <td style='border-top:#000 solid 1px;'>&nbsp</td>
                    <td style='border-top:#000 solid 1px;'>&nbsp</td>
                    <td style='border-top:#000 solid 1px;'>&nbsp</td>
                    <td style='border-top:#000 solid 1px;'>&nbsp</td>
                    <td style='border-top:#000 solid 1px;'>&nbsp</td>
                    <td style='border-top:#000 solid 1px;'>&nbsp</td>
                </tr>
                ";

            $html .= "<table>";
        }
        
        return $html;
    }
         
    private function calcPlays($plays, $freePlays) {
        
        if($freePlays !== FALSE){
            $plays = $plays - $freePlays;
        } 
        else {
            $plays = $plays;                        
        }
        
        return $plays;
    }
    
    private function checkPBVersion($array_info) {
        $version = FALSE;
        
        $min_version = FALSE;
        foreach($array_info as $single_info){
            if($single_info['typeInfo'] != 40){
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
        }
        
        if($min_version != NULL){
            $version = $min_version;
        }
        
        return $version;
    }
  
    public function getLastCon($idBooth) {
        $this->last_connection = $this->RepdcModel->getPbLastConnectionZone_appBooths($idBooth);
        if(!$this->last_connection){
            $this->last_connection = $this->RepdcModel->getPbLastConnection_appBooths($idBooth);
        }
        
        if($this->last_connection != NULL && $this->last_connection != ""){
            $this->last_connection = DateTime::createFromFormat('Y-m-d H:i:s', $this->last_connection);
            
            $this->last_connection_str = $this->last_connection->format('m/d/Y H:i');
            $this->last_connection = $this->last_connection->format('m/d/Y H:i:s');
        }
        
        return $this->last_connection;
    }
//    public function getLastCon($idBooth) {
//        $this->last_connection = $this->RepdcModel->getPbLastConnectionZone_appBooths($idBooth);
//        if(!$this->last_connection){
//            $this->last_connection = $this->RepdcModel->getPbLastConnection_appBooths($idBooth);
//        }
//        
//        if($this->last_connection != NULL && $this->last_connection != ""){
//            $this->last_connection = DateTime::createFromFormat('Y-m-d H:i:s', $this->last_connection);
//            
//            $this->last_connection_str = $this->last_connection->format('m/d/Y H:i');
//            $this->last_connection = $this->last_connection->format('m/d/Y H:i:s');
//        }
//        
//        return $this->last_connection;
//    }
    
    
  
}

