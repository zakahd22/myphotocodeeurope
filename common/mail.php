
<?php

require_once G_PATH . 'common/mailer/PHPMailerAutoload.php';

//modificada a partir de la versíó motor

class mail {
    public $mailer;
//    private $host, $port, $smtp_user, $smtp_pass, $sendmail;
    
    private $template = "";
    private $fields = array();
    private $values = array();
    private $nFields = 0;
    
    public $retMsg = "";
    public $ok = true;
    public $ret= false;

    public function __construct() {
//        require 'config/config.php';
//        $this->host = $MAIL["host"];
//        $this->port = $MAIL["port"];
//        $this->smtp_user = $MAIL["smtp_user"];
//        $this->smtp_pass = $MAIL["smtp_pass"];
//        $this->sendmail = $MAIL["sendmail"];
        
        $this->mailer();
        $this->setBussines();
    }
    
    public function mailer() {
	$this->mailer = new PHPMailer(true);
//        if ($this->sendmail == 1) {
//            $this->mailer->isSendmail();    // para la kk de 1and1, sino usar isSMTP()
//        } else {
//            $this->mailer->isSMTP();
//       }
	$this->mailer->isSMTP();
	$this->mailer->SMTPSecure = 'ssl';
	$this->mailer->SMTPAuth = true;
	$this->mailer->Host = 'smtp.ionos.com';
	$this->mailer->Username = 'noreply@myphotocode.com';
	$this->mailer->Password = 'd1g1t4lc3ntr3&';
	$this->mailer->Port = '465';

	$this->mailer->SMTPDebug = 0;
	$this->mailer->Debugoutput = 'html';
        
        $this->mailer->IsHTML(true);
	$this->mailer->CharSet = 'UTF-8';
        
        $this->mailer->Body = "-";

	$this->mailer->setFrom('noreply@myphotocode.com', 'My PhotoCode');

	$this->mailer->addBCC('main@dc-image.com', 'MYPC-Main');
//        $this->mailer->addBCC('aleix@dc-image.com', 'MYPC-Aleix');
	//$this->mailer->addBCC('ferran@dc-image.com', 'MYPC-Ferran');
	//$this->mailer->addBCC('victor@dc-image.com', 'MYPC-Victor');
    }
    
    
    public function setBussines($mailUser = 'noreply') {
        require_once 'config/params.php';
//        $this->mailer->Host = $mail_{$mailUser}['host'];
//        $this->mailer->Port = $mail_{$mailUser}['port'];
//        $this->mailer->Username = $mail_{$mailUser}['smtp_user'];
//        $this->mailer->Password = $mail_{$mailUser}['smtp_pass'];
//        $this->mailer->isSendmail();
	$this->mailer->isSMTP();
	$this->mailer->SMTPSecure = 'ssl';
	$this->mailer->SMTPAuth = true;
	$this->mailer->Host = 'smtp.ionos.com';
	$this->mailer->Username = 'noreply@myphotocode.com';
	$this->mailer->Password = 'd1g1t4lc3ntr3&';
	$this->mailer->Port = '465';
		
        $this->mailer->setFrom("noreply@myphotocode.com", $mailUser);
        
        $this->mailer->AltBody = 'To view the message, please use an HTML compatible email viewer';
    }
    
    public function setFromName($nom) {
        $this->mailer->setFrom("noreply@myphotocode.com", $nom);        
    }
    
    public function addAdress($dst, $nom) {
        try{
            $this->mailer->addAddress($dst, $nom);
        }
        catch (Exception $e){
            $this->retMsg = $e->getMessage();
            $this->ok = false;
        }
    }
    
    public function addAdressCC($dst, $nom) {
        try{
            $this->mailer->addCC($dst, $nom);
        }
        catch (Exception $e){
            $this->retMsg = $e->getMessage();
            $this->ok = false;
        }
    }    
    
    public function addAdressBCC($dst, $nom) {
        try{
            $this->mailer->addBCC($dst, $nom);
        }
        catch (Exception $e){
            $this->retMsg = $e->getMessage();
            $this->ok = false;
        }
    }    
    
    public function setSubject($subject) {
        $this->mailer->Subject = ((G_TEST==1)?('(TEST)'. $subject):($subject));
    }    
    
    public function setBody($content) {
	$this->mailer->Body = $content;
    }    
    
    /**
     * Defineix la plantilla a enviar per el mail.
     * 
     * @param String $quina Path absolut al template(G_PATH).
     * @return boolean
     */
    public function setTemplate($quina) {
        if(file_exists($quina)){
            $this->template = $quina;
            $ret = true;
        }
        else{
            $this->retMsg = "Path plantilla incorrecte: {$quina}";
            $ret = false;
        }
        
        return $ret;
    }    
    
    public function addTemplateField($field, $value) {
        $this->fields[$this->nFields] = $field;
        $this->values[$this->nFields] = $value;
        $this->nFields++;
    }    
    
    public function applyTempplateFields() {
        $tpl = file_get_contents($this->template);
        if($tpl){
            $this->mailer->Body = str_replace($this->fields, $this->values, $tpl);
        }
    }    
    
    /**
     * 
     * @param String $file Absolute Path
     */
    public function addAttachment($file, $name = '') {
        $this->ret = false;
        
	try{
            $this->ret = $this->mailer->addAttachment($file, $name);        
        } 
        catch (Exception $e){
            $this->retMsg = $e->getMessage();
            $this->ret = false;
        }
        
        return $this->ret;
    }    
    
    /*
     * For test proposer
     * Return mail HTML
     */
    public function getMsgHTML(){
        return $this->mailer->Body;
    }    
    
    public function send() {
//        ob_clean();
        $this->ret = false;
        ob_start();
        
        try{
            $this->ret = $this->mailer->send();
        }
        catch (Exception $e){
            $this->retMsg = $e->getMessage();
            $this->ret = false;
        }
        
        
        $this->retMsg .= ob_get_contents();
        ob_end_clean();
        if($this->retMsg) $this->ret = false; //  $mail->Send() no és fiable
        
        return $this->ret;
    }    
    
//	if ($file) { $mail->mailer->addAttachment($file); }
//
//	if (!$mail->mailer->send()) {
     
    
    
    
}
