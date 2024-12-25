<?php
require_once dirname(__FILE__) . "/../global.php";
require_once G_PATH . "common/mail.php";

class MailViewRender{
    private $email = "main@myphotocode.com";
    private $emailName = "My PhotoCode";
    
    public function setEmail($email){
        $this->email = $email;
    }
    public function setEmailName($name){
        $this->emailName = $name;
    }
    
    public function sendStatisticsReportEvents($subject, $data){
        $return = false;
        try {
            $mail = new mail();
            $mail->addAdress($this->email, $this->emailName);
            $mail->setSubject($subject);
            $mail->setTemplate(G_PATH . "common/resources/templates/html/en/StatisticReportsEventsCount.html");
            
//            $mail->addTemplateField("#StatisticLastYear#", $data['#StatisticLastYear#']);
//            $mail->addTemplateField("#StatisticThisYear#", $data['#StatisticThisYear#']);
//            $mail->addTemplateField("#StatisticLastMonth#", $data['#StatisticLastMonth#']);
//            $mail->addTemplateField("#StatisticThisMonth#", $data['#StatisticThisMonth#']);
//            $mail->addTemplateField("#StatisticLastWeek#", $data['#StatisticLastWeek#']);
            
            $mail->addTemplateField("#StatisticReportEvents#", $data);
            $mail->applyTempplateFields();
            if(G_TEST == 1){
                echo $mail->getMsgHTML();
//                $return = $mail->getMsgHTML();
            } else {
                if($mail->send()){
                    $return = true;
                } else{
                    utils::log('Not Send email "Statistics Report Events"', 'logReportsController', 'Statistics Report Events');
                }
            }
        } catch (Exception $e){
            utils::log('Internal Error '.$e, 'logReportsController', 'email');
        }
        return $return;
    }
    
    public function sendStatisticsReportOwnersLogin($subject, $data){
        $return = false;
        try {
            $mail = new mail();
            $mail->addAdress($this->email, $this->emailName);
            $mail->setSubject($subject);
            $mail->setTemplate(G_PATH . "common/resources/templates/html/en/StatisticReportsOwnerLogin.html");
            
//            $mail->addTemplateField("#StatisticLastYear#", $data['#StatisticLastYear#']);
//            $mail->addTemplateField("#StatisticThisYear#", $data['#StatisticThisYear#']);
//            $mail->addTemplateField("#StatisticLastMonth#", $data['#StatisticLastMonth#']);
//            $mail->addTemplateField("#StatisticThisMonth#", $data['#StatisticThisMonth#']);
//            $mail->addTemplateField("#StatisticLastWeek#", $data['#StatisticLastWeek#']);
            $mail->addTemplateField("#StatisticOwnerLogin#", $data);
            
            $mail->applyTempplateFields();
            if(G_TEST == 1){
                echo $mail->getMsgHTML();
//                $return = $mail->getMsgHTML();
            } else {
                if($mail->send()){
                    $return = true;
                } else{
                    utils::log('Not Send email "Statistics Report Owner Login"', 'logReportsController', 'Statistics Report Owner Login');
                }
            }
        } catch (Exception $e){
            utils::log('Internal Error '.$e, 'logReportsController', 'email');
        }
        return $return;
    }
    
    public function sendStatisticsReportUploadPhotos($subject, $data){
        $return = false;
        try {
            $mail = new mail();
            $mail->addAdress($this->email, $this->emailName);
            $mail->setSubject($subject);
            $mail->setTemplate(G_PATH . "common/resources/templates/html/en/StatisticReportsPhotosUploadCount.html");
            
            $mail->addTemplateField("#StatisticPhotoUpload#", $data);
            $mail->applyTempplateFields();
            if(G_TEST == 1){
                echo $mail->getMsgHTML();
//                $return = $mail->getMsgHTML();
            } else {
                if($mail->send()){
                    $return = true;
                } else{
                    utils::log('Not Send email "Statistics Report Photos Upload"', 'logReportsController', 'Statistics Report Photos Upload');
                }
            }
        } catch (Exception $e){
            utils::log('Internal Error '.$e, 'logReportsController', 'email');
        }
        return $return;
    }
    
    public function sendStatisticsReportUploadPhotoFiles($subject, $data){
        $return = false;
        try {
            $mail = new mail();
            $mail->addAdress($this->email, $this->emailName);
            $mail->setSubject($subject);
            $mail->setTemplate(G_PATH . "common/resources/templates/html/en/StatisticReportsPhotoFilesUploadCount.html");
            
            $mail->addTemplateField("#StatisticPhotoFilesUpload#", $data);
            $mail->applyTempplateFields();
            if(G_TEST == 1){
                echo $mail->getMsgHTML();
//                $return = $mail->getMsgHTML();
            } else {
                if($mail->send()){
                    $return = true;
                } else{
                    utils::log('Not Send email "Statistics Report PhotoFiles Upload"', 'logReportsController', 'Statistics Report PhotoFiles Upload');
                }
            }
        } catch (Exception $e){
            utils::log('Internal Error '.$e, 'logReportsController', 'email');
        }
        return $return;
    }
    
    public function sendStatisticsAllReport(
        $subject,
        $dataFiles,
        $dataOwner,
        $dataUpdatePhotos,
        $dataEvents,
        $dataSharePhotos
    ){
        $return = false;
        try {
            $mail = new mail();
            $mail->addAdress($this->email, $this->emailName);
            $mail->setSubject($subject);
            $mail->setTemplate(G_PATH . "common/resources/templates/html/en/AllStatisticReports.html");
            
            $mail->addTemplateField("#StatisticReportEvents#", $dataEvents);
            $mail->addTemplateField("#StatisticOwnerLogin#", $dataOwner);
            $mail->addTemplateField("#StatisticPhotoUpload#", $dataUpdatePhotos);
            $mail->addTemplateField("#StatisticPhotoFilesUpload#", $dataFiles);
            $mail->addTemplateField("#StatisticSharePhotos#", $dataSharePhotos);
            
            $mail->applyTempplateFields();
            if(G_TEST == 1){
                echo $mail->getMsgHTML();
//                $return = $mail->getMsgHTML();
            } else {
                if($mail->send()){
                    $return = true;
                } else{
                    utils::log('Not Send email "Statistics Report PhotoFiles Upload"', 'logReportsController', 'Statistics Report PhotoFiles Upload');
                }
            }
        } catch (Exception $e){
            utils::log('Internal Error '.$e, 'logReportsController', 'email');
        }
        return $return;
    }
    
    public function sendRepdcWeekly(
        $subject,
        $repdc_color,
        $reportTypeName,
        $startDate,
        $endDate,
        $booth_type,
        $serialnumber,
        $idBooth,
        $rand_string,
        $owner_name,
        $both_name,
        $location,
        $version,
        $week_number,
        $last_connection,
        $repdc_body
    ){
        $return = false;
        try {
            $mail = new mail();
            $mail->setFromName("DC Report Platform");
            $mail->addAdress($this->email, $this->emailName);
            $mail->setSubject($subject);
            $mail->setTemplate(G_PATH . "common/resources/templates/html/en/Repdc_weekly.html");
            
            $mail->addTemplateField("#repdc_color#", $repdc_color);
            $mail->addTemplateField("#reportTypeName#", $reportTypeName);
            $mail->addTemplateField("#startDate#", $startDate);
            $mail->addTemplateField("#endDate#", $endDate);
            $mail->addTemplateField("#booth_type#", $booth_type);
            $mail->addTemplateField("#serialnumber#", $serialnumber);
            $mail->addTemplateField("#idBooth#", $idBooth);
            $mail->addTemplateField("#rand_string#", $rand_string);
            $mail->addTemplateField("#owner_name#", $owner_name);
            $mail->addTemplateField("#both_name#", $both_name);
            $mail->addTemplateField("#location#", $location);
            $mail->addTemplateField("#version#", $version);
            
            $mail->addTemplateField("#week_number#", $week_number);
            $mail->addTemplateField("#last_connection#", $last_connection);
            
            $mail->addTemplateField("#repdc_body#", $repdc_body);
            
            $now = new DateTime();
            $mail->addTemplateField("#now#", $now->format('m/d/Y H:i'));
            
            $mail->applyTempplateFields();
            if(G_TEST == 1){
                echo $mail->getMsgHTML();
//                $return = $mail->getMsgHTML();
            } else {
                if($mail->send()){
                    $return = true;
                } else{
                    utils::log('Not Send email "DC PhotoBooth Report - Weekly" to ' . $this->email, 'logMailer');
                }
            }
        } catch (Exception $e){
            utils::log('Internal Error '.$e, 'logMailer', 'email');
        }
        return $return;
    }
    
    public function sendRepdcMonthly(
        $subject,
        $repdc_color,
        $reportTypeName,
        $startDate,
        $endDate,
        $booth_type,
        $serialnumber,
        $idBooth,
        $rand_string,
        $owner_name,
        $both_name,
        $location,
        $version,
        $month_number,
        $last_connection,
        $repdc_body
    ){
        $return = false;
        try {
            $mail = new mail();
            $mail->setFromName("DC Report Platform");
            $mail->addAdress($this->email, $this->emailName);
            $mail->setSubject($subject);
            $mail->setTemplate(G_PATH . "common/resources/templates/html/en/Repdc_monthly.html");
            
            $mail->addTemplateField("#repdc_color#", $repdc_color);
            $mail->addTemplateField("#reportTypeName#", $reportTypeName);
            $mail->addTemplateField("#startDate#", $startDate);
            $mail->addTemplateField("#endDate#", $endDate);
            $mail->addTemplateField("#booth_type#", $booth_type);
            $mail->addTemplateField("#serialnumber#", $serialnumber);
            $mail->addTemplateField("#idBooth#", $idBooth);
            $mail->addTemplateField("#rand_string#", $rand_string);
            $mail->addTemplateField("#owner_name#", $owner_name);
            $mail->addTemplateField("#both_name#", $both_name);
            $mail->addTemplateField("#location#", $location);
            $mail->addTemplateField("#version#", $version);
            
            $mail->addTemplateField("#month_number#", $month_number);
            $mail->addTemplateField("#last_connection#", $last_connection);
            
            $mail->addTemplateField("#repdc_body#", $repdc_body);
            
            $now = new DateTime();
            $mail->addTemplateField("#now#", $now->format('m/d/Y H:i:s'));
            
            $mail->applyTempplateFields();
            if(G_TEST == 1){
                echo $mail->getMsgHTML();
//                $return = $mail->getMsgHTML();
            } else {
                if($mail->send()){
                    $return = true;
                } else{
                    utils::log('Not Send email "DC PhotoBooth Report - Monthly"', 'logMailer');
                }
            }
        } catch (Exception $e){
            utils::log('Internal Error '.$e, 'logMailer', 'email');
        }
        return $return;
    }
    
    public function sendRepdcYear(
        $subject,
        $repdc_color,
        $reportTypeName,
        $startDate,
        $endDate,
        $booth_type,
        $serialnumber,
        $idBooth,
        $rand_string,
        $owner_name,
        $both_name,
        $location,
        $version,
        $year_number,
        $last_connection,
        $repdc_body
    ){
        $return = false;
        try {
            $mail = new mail();
            $mail->setFromName("DC Report Platform");
            $mail->addAdress($this->email, $this->emailName);
            $mail->setSubject($subject);
            $mail->setTemplate(G_PATH . "common/resources/templates/html/en/Repdc_year.html");
            
            $mail->addTemplateField("#repdc_color#", $repdc_color);
            $mail->addTemplateField("#reportTypeName#", $reportTypeName);
            $mail->addTemplateField("#startDate#", $startDate);
            $mail->addTemplateField("#endDate#", $endDate);
            $mail->addTemplateField("#booth_type#", $booth_type);
            $mail->addTemplateField("#serialnumber#", $serialnumber);
            $mail->addTemplateField("#idBooth#", $idBooth);
            $mail->addTemplateField("#rand_string#", $rand_string);
            $mail->addTemplateField("#owner_name#", $owner_name);
            $mail->addTemplateField("#both_name#", $both_name);
            $mail->addTemplateField("#location#", $location);
            $mail->addTemplateField("#version#", $version);
            
            $mail->addTemplateField("#year_number#", $year_number);
            $mail->addTemplateField("#last_connection#", $last_connection);
            
            $mail->addTemplateField("#repdc_body#", $repdc_body);
            
            $now = new DateTime();
            $mail->addTemplateField("#now#", $now->format('m/d/Y H:i:s'));
            
            $mail->applyTempplateFields();
            if(G_TEST == 1){
                echo $mail->getMsgHTML();
//                $return = $mail->getMsgHTML();
            } else {
                if($mail->send()){
                    $return = true;
                } else{
                    utils::log('Not Send email "DC PhotoBooth Report - Year"', 'logMailer', 'Statistics Report PhotoFiles Upload');
                }
            }
        } catch (Exception $e){
            utils::log('Internal Error '.$e, 'logMailer', 'email');
        }
        return $return;
    }
    
    public function sendRepdcCollection(
        $subject,
        $repdc_color,
        $reportTypeName,
        $startDate,
        $endDate,
        $booth_type,
        $serialnumber,
        $idBooth,
        $rand_string,
        $owner_name,
        $both_name,
        $location,
        $version,
        $repdc_body,
        $now        
    ){
        $return = false;
        try {
            $mail = new mail();
            $mail->setFromName("DC Report Platform");
            $mail->addAdress($this->email, $this->emailName);
            $mail->setSubject($subject);
            $mail->setTemplate(G_PATH . "common/resources/templates/html/en/Repdc_collection.html");
            
            $mail->addTemplateField("#repdc_color#", $repdc_color);
            $mail->addTemplateField("#reportTypeName#", $reportTypeName);
            $mail->addTemplateField("#startDate#", $startDate);
            $mail->addTemplateField("#endDate#", $endDate);
            $mail->addTemplateField("#booth_type#", $booth_type);
            $mail->addTemplateField("#serialnumber#", $serialnumber);
            $mail->addTemplateField("#idBooth#", $idBooth);
            $mail->addTemplateField("#rand_string#", $rand_string);
            $mail->addTemplateField("#repdc_num#", $repdc_num);
            $mail->addTemplateField("#cash_in#", $cash_in);
            $mail->addTemplateField("#plays_in#", $plays_in);
            $mail->addTemplateField("#paper_stock#", $paper_stock);
            $mail->addTemplateField("#owner_name#", $owner_name);
            $mail->addTemplateField("#both_name#", $both_name);
            $mail->addTemplateField("#location#", $location);
            $mail->addTemplateField("#version#", $version);
            
            $mail->addTemplateField("#repdc_body#", $repdc_body);
            
            $now = new DateTime();
            $mail->addTemplateField("#now#", $now->format('m/d/Y H:i:s'));
            
            $mail->applyTempplateFields();
            if(G_TEST == 1){
                echo $mail->getMsgHTML();
//                $return = $mail->getMsgHTML();
            } else {
                if($mail->send()){
                    $return = true;
                } else{
                    utils::log('Not Send email "DC PhotoBooth Report - Year"', 'logMailer', 'Statistics Report PhotoFiles Upload');
                }
            }
        } catch (Exception $e){
            utils::log('Internal Error '.$e, 'logMailer', 'email');
        }
        return $return;
    }
}