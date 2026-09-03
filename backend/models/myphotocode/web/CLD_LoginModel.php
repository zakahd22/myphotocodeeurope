<?php
require_once G_PATH . "models/baseModel.php";

class CLD_LoginModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }

    public function getUsersType($userType){
        $this->setFilter('userType', '=', $userType);
        return $this->select('CLD_Login', 'users');
    }

    public function getLoginWhereUserIdUserType($id_user, $userType){
        $this->setFilter('id_user', '=', $id_user);
        $this->setFilter('userType', '=', $userType, "AND");
        return $this->select('CLD_Login');
    }
    
    public function getLoginWhereUsername($username){
        $this->setFilter('username', '=', $username);
        return $this->select('CLD_Login');
    }

    public function updateLogin($id_user, $userType, $updates){
        $this->setFilter("id_user", "=", $id_user);
        $this->setFilter("userType", "=", $userType, "AND");
        return $this->update('CLD_Login', $updates);
    }
    
    public function updateLoginWhereUsername($username, $updates){
        $this->setFilter("username", "=", $username);
        return $this->update('CLD_Login', $updates);
    }
    
    public function insertCLD_Login(){
        return $this->insert('CLD_Login');
    }
    
    /**
     * get more than one photo
     * @param type $photoCode all the code or a single part of it 
     * @return Array Containing an array with the entities photos
     */
    public function getAllFromLogin($ownerID){
        $fields .= "";
        $this->setFilter("CLD_Login.id_user", "=",  $ownerID);
        $this->setFilter("CLD_Login.userType", "=", "4", "AND");
        $this->entity->loadEntity('CLD_Login');
        $fields .= $this->entity->getEntityFields();  
        $fields .= ', ';  
        $this->entity->loadEntity('rentals');
        $fields .= $this->entity->getEntityFields();
        
        $sql = "
            SELECT {$fields} 
            FROM CLD_Login
            LEFT JOIN rentals
            ON rentals.id = CLD_Login.id_user
        ";
        
        $query = $this->my_query($sql);
        
        $result = $this->requestQueryResults($query, 'all', array('CLD_Login','rentals'));
        
        return $result;
    }
}