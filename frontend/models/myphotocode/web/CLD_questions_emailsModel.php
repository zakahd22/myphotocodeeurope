<?php
require_once G_PATH . "models/baseModel.php";

class CLD_questions_emailsModel extends baseModel{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getQuestionsEmail($id){
        $this->setFilter("event", "=", $id);
        $this->setFilter("email", "IS NOT", "NULL", "AND");
        $this->setGroup("email");
        return $this->select('CLD_questions_emails');
    }
    
    public function getQuestionsEmailIN($array){
        $this->setInFilter('event', $array);
        return $this->select('CLD_questions_emails');
    }

    public function getQuestionsEmails($events = FALSE, $LIMIT = false){
        $fields .= "";
        
        if($events !== FALSE){$this->setInFilter('CLD_questions_emails.event', $events);}
        $this->entity->loadEntity('CLD_questions_emails');
        $fields .= $this->entity->getEntityFields();  
        $fields .= ', ';  
        $this->entity->loadEntity('events');
        $fields .= $this->entity->getEntityFields();
        
        $sql = "
            SELECT {$fields} 
            FROM CLD_questions_emails
            LEFT JOIN events
            ON CLD_questions_emails.event = events.id
        ";
        
        if($LIMIT){
            $this->setLimit($LIMIT);
        }
            
        $query = $this->my_query($sql);
        
        $result = $this->requestQueryResults($query, 'all', array('CLD_questions_emails','events'));
        
        return $result;
    }     
    
}
