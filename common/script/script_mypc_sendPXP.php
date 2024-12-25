<?php
require_once dirname(__FILE__) . "/../global.php";
require_once G_PATH . "common/Classes/baseController.php";
require_once G_PATH . "sections/payxprint/functions/reportManager.php";
require_once G_PATH . "common/Classes/scriptFormatOutput.php";

class sendPxp extends baseController{
    private $test = true;
    private $arrayDistributors = array();
    private $dateInit = NULL;
    private $dateFinish = NULL;
    
    public function __construct() {
        parent::__construct(false);
        $this->getDistributors();
        $this->getDates();
        
    }
    
    private function getDistributors() {
        $mypcDB = new baseController;
        $mypcDB->createModel('CLD_Login');
        $this->arrayDistributors = $mypcDB->CLD_LoginModel->getUsersType(3);
        if($this->test){
            scriptFormatOutput::CLI_echo(
                var_dump($this->arrayDistributors)
            );
            scriptFormatOutput::CLI_show();
        }
    }
    
    private function getDates() {
        $this->dateInit = date("Y/m/d", mktime(0, 0, 0, 1, 1, 2016));
        $this->dateFinish = date("Y/m/d", mktime(0, 0, 0, date("m")-1, 1, date("Y")));
        
        scriptFormatOutput::CLI_echo(
                "Date Init -> {$this->dateInit}\n".
                "Date Finish -> {$this->dateFinish}"
        );
        scriptFormatOutput::CLI_show();
    }
}

scriptFormatOutput::CLI_echo(
        "==========================================\n"
      . "------------- SEND REPORTS PxP -----------\n"
      . "==========================================\n"
);
scriptFormatOutput::CLI_show();
new sendPxp();