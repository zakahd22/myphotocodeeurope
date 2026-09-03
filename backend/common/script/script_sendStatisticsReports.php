<?php
require_once dirname(__FILE__) . "/../global.php";
require_once G_PATH . "sections/statisticsReports/controller/StatisticsReportsController.php";
require_once G_PATH . "common/Classes/MailViewRender.php";
require_once G_PATH . "common/Classes/scriptFormatOutput.php";

function sendStatisticsReportEventsEmail($email){
    $stdRC = new StatisticsReportsController;
    $STDMAIL = new MailViewRender;
    $STDMAIL->setEmail($email);
    
    scriptFormatOutput::CLI_echo(
        "Myphotocode Statistics Report - Event Count.",
        scriptFormatOutput::SUCCESS
    );
    scriptFormatOutput::CLI_show();
    $data = $stdRC->createEventsStatistics();
    $STDMAIL->setEmailName('Myphotocode Statistics Report - Event Count');
    if($STDMAIL->sendStatisticsReportEvents('Myphotocode Statistics Report - Event Count', $data)){
        scriptFormatOutput::CLI_echo(
            "Email Sended.",
            scriptFormatOutput::SUCCESS
        );
        scriptFormatOutput::CLI_show();
    }
    else{
        scriptFormatOutput::CLI_echo(
            "Not send email :(",
            scriptFormatOutput::FAILURE
        );
        scriptFormatOutput::CLI_show();
    }
}

function sendStatisticsReportPhotoUploadEmail($email){
    $stdRC = new StatisticsReportsController;
    $STDMAIL = new MailViewRender;
    $STDMAIL->setEmail($email);
    
    scriptFormatOutput::CLI_echo(
        "Myphotocode Statistics Report - Photo Upload.",
        scriptFormatOutput::SUCCESS
    );
    scriptFormatOutput::CLI_show();
    $data = $stdRC->createUpdatePhotosStatistics();
    $STDMAIL->setEmailName('Myphotocode Statistics Report - Photo Upload');
    if($STDMAIL->sendStatisticsReportUploadPhotos('Myphotocode Statistics Report - Photo Upload', $data)){
        scriptFormatOutput::CLI_echo(
            "Email Sended.",
            scriptFormatOutput::SUCCESS
        );
        scriptFormatOutput::CLI_show();
    }
    else{
        scriptFormatOutput::CLI_echo(
            "Not send email :(",
            scriptFormatOutput::FAILURE
        );
        scriptFormatOutput::CLI_show();
    }
}

function sendStatisticsReportOwnerLoginEmail($email){
    $stdRC = new StatisticsReportsController;
    $STDMAIL = new MailViewRender;
    $STDMAIL->setEmail($email);
    
    scriptFormatOutput::CLI_echo(
        "Myphotocode Statistics Report - Owner Login.",
        scriptFormatOutput::SUCCESS
    );
    $data = $stdRC->createOwnerLoginStatistics();
    $STDMAIL->setEmailName('Myphotocode Statistics Report - Owner Login');
    if($STDMAIL->sendStatisticsReportOwnersLogin('Myphotocode Statistics Report - Owner Login', $data)){
        scriptFormatOutput::CLI_echo(
            "Email Sended.",
            scriptFormatOutput::SUCCESS
        );
        scriptFormatOutput::CLI_show();
    }
    else{
        scriptFormatOutput::CLI_echo(
            "Not send email :(",
            scriptFormatOutput::FAILURE
        );
        scriptFormatOutput::CLI_show();
    }
}

function sendStatisticsReportPhotoFilesUploadEmail($email){
    $stdRC = new StatisticsReportsController;
    $STDMAIL = new MailViewRender;
    $STDMAIL->setEmail($email);
    
    scriptFormatOutput::CLI_echo(
        "Myphotocode Statistics Report - Photo Files Upload.",
        scriptFormatOutput::SUCCESS
    );
    scriptFormatOutput::CLI_show();
    $data = $stdRC->createPhotoFilesStatistics();
    $STDMAIL->setEmailName('Myphotocode Statistics Report - Photo Files Upload');
    if($STDMAIL->sendStatisticsReportUploadPhotoFiles('Myphotocode Statistics Report - Photo Files Upload', $data)){
        scriptFormatOutput::CLI_echo(
            "Email Sended.",
            scriptFormatOutput::SUCCESS
        );
        scriptFormatOutput::CLI_show();
    }
    else{
        scriptFormatOutput::CLI_echo(
            "Not send email :(",
            scriptFormatOutput::FAILURE
        );
        scriptFormatOutput::CLI_show();
    }
}

function sendAllStatisticsReport($email){
    $stdRC = new StatisticsReportsController;
    $STDMAIL = new MailViewRender;
    $STDMAIL->setEmail($email);
    
    scriptFormatOutput::CLI_echo(
        "Myphotocode Statistics Report - All Statistics",
        scriptFormatOutput::SUCCESS
    );
    scriptFormatOutput::CLI_show();
    
    $dataFiles        = $stdRC->createPhotoFilesStatistics();
    $dataOwner        = $stdRC->createOwnerLoginStatistics();
    $dataUpdatePhotos = $stdRC->createUpdatePhotosStatistics();
    $dataEvents       = $stdRC->createEventsStatistics();
    $dataSharePhotos  = $stdRC->createCloudShareStatistics();
    $STDMAIL->setEmailName('Myphotocode Statistics Report');
    
    if($STDMAIL->sendStatisticsAllReport(
        'Myphotocode Statistics Report - All Statistics',
        $dataFiles,
        $dataOwner,
        $dataUpdatePhotos,
        $dataEvents,
        $dataSharePhotos
    )){
        scriptFormatOutput::CLI_echo(
            "Email Sended.",
            scriptFormatOutput::SUCCESS
        );
        scriptFormatOutput::CLI_show();
    }
    else{
        scriptFormatOutput::CLI_echo(
            "Not send email :(",
            scriptFormatOutput::FAILURE
        );
        scriptFormatOutput::CLI_show();
    }
    
    
}

/**
 * How to use:
 * Call this script in shell with php.
 * And type after name script this Args to send automatically.
 * 
 * email:XXXX@XXXXX.com
 * type:XXXXX
 * 
 * EX: php5 common/script/script_sendStatisticsReports.php email:name@domain.com type:all
 * 
 * Types:
 *      all     - To send all Statistics
 *      events  - To send Events statistics
 *      photos  - To send Photos Upload Statistics
 *      login   - To send Owner Login Statistic
 */
$email = false;
$type = false;
for($i=0; $i<$argc; $i++){
    $var = explode(':',$argv[$i]);
    if($var[0] == "email"){
        $email = $var[1];
    }
    else if($var[0] == "type"){
        $type = $var[1];
    }
}

if($email){
    if($type == "all"){
        sendAllStatisticsReport($email);
    }
    else if($type == "events"){
        sendStatisticsReportEventsEmail($email);
    }
    else if($type == "photos"){
        sendStatisticsReportPhotoUploadEmail($email);
    }
    else if($type == "login"){
        sendStatisticsReportOwnerLoginEmail($email);
    }
    else if($type == "photoFiles"){
        sendStatisticsReportPhotoFilesUploadEmail($email);
    }
}
else{
    scriptFormatOutput::CLI_echo(
            "==========================================\n"
          . "--------- SEND STATISTICS REPORTS --------\n"
          . "==========================================\n"
          . "Type an email to send the statistics reports: "
    );
    scriptFormatOutput::CLI_show();

    $correctEmail = false;
    while(!$correctEmail){
        $email = scriptFormatOutput::CLI_promp();

        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $correctEmail = true;
            scriptFormatOutput::CLI_echo(
                "Email validated.",
                scriptFormatOutput::SUCCESS
            );
            scriptFormatOutput::CLI_show();      
        }
        else {
            scriptFormatOutput::CLI_echo(
                "Invalid email, try again.",
                scriptFormatOutput::FAILURE
            );
            scriptFormatOutput::CLI_show();
        }
    }

    scriptFormatOutput::CLI_echo(
            "What kind of statistic you want to send?\n"
          . "1 - Send Events Statistics \n"
          . "2 - Send Photo Upload Statistics \n"
          . "3 - Send Owner Login Statistics\n"
          . "4 - Send PhotoFiles Upload Statistics\n"
          . "5 - Send All Statistics \n"
    );
    scriptFormatOutput::CLI_show();

    $correctOption = false;
    while(!$correctOption){
        $option = scriptFormatOutput::CLI_promp();

        if($option == 1){
            sendStatisticsReportEventsEmail($email);
            $correctOption = true;
        }
        else if($option == 2){
            sendStatisticsReportPhotoUploadEmail($email);
            $correctOption = true;
        }
        else if($option == 3){
            sendStatisticsReportOwnerLoginEmail($email);
            $correctOption = true;
        }
        else if($option == 4){
            sendStatisticsReportPhotoFilesUploadEmail($email);
            $correctOption = true;
        }
        else if($option == 5){
            sendAllStatisticsReport($email);
            $correctOption = true;
        }
    }
    
}
scriptFormatOutput::CLI_echo(
        "\nScript Completed\n\n   º\( ͡° ͜ʖ ͡°)/º \n"
);
scriptFormatOutput::CLI_show();