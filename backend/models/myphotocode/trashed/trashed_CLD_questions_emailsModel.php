<?php
require_once dirname(__FILE__) . "/trashedModel.php";

class trashed_CLD_questions_emailsModel extends trashedModel{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getAllQuestionsEmailOneEvent($id){
        $this->setFilter("event", "=", $id);
        $this->setFilter("email", "IS NOT", "NULL", "AND");
        $this->setGroup("email");
        return $this->select('trashed_CLD_questions_emails');
    }
    
}
