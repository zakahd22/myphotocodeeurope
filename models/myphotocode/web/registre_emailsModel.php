<?php
require_once G_PATH . "models/baseModel.php";

class registre_emailsModel extends baseModel{


    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the event from the id
     */
    public function getRegistreEmail(){
        return $this->select('registre_emails');
    }
    
    public function getRegistreEmails($limit = False){
        $this->setGroup('email');
        $this->setOrder('fecha', 'DESC');
        if($limit !== False){$this->setLimit($limit);}
        return $this->select('registre_emails');
    }
    
    public function getRegistreEmailsOwner($photo_codes, $limit = false){
        $this->setInFilter('event_id', $photo_codes);
        $this->setGroup('email');
        $this->setOrder('fecha', 'DESC');
        if($limit !== false){$this->setLimit($limit);}
        return $this->select('registre_emails');
    }
    
    public function getRegistreEmailEvent($id_event){
        $this->setFilter("event_id", "=", $id_event);
        $this->setGroup('email');
        return $this->select('registre_emails');
    }
    
    public function getRegistreEmailAll($id){
        $this->setFilter("photos.event_id", "=", $id);
        $this->setFilter("registre_emails.email", "IS NOT", "NULL", "AND");
        $this->setGroup("registre_emails.email");
        
        $fields .= "";
        $this->entity->loadEntity('registre_emails');
        $fields .= $this->entity->getEntityFields();
        
        $sql = "
            SELECT {$fields} 
            FROM registre_emails
            RIGHT JOIN photos
            ON registre_emails.code = photos.code
        ";
        
        $query = $this->my_query($sql);
        
        $result = $this->requestQueryResults($query, 'all');
        
        return $result;
    }
    
    public function insertRegistreEmail($dataset = "all"){
        $this->entity->changeEntity('registre_emails');
        $this->insert('registre_emails', $dataset);
    }
}
    