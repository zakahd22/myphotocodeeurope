<?php

class baseModel extends EntityUtility{
    private $id_db_array = array('myphotocode_web', 'myphotocode_trashed', 'myphotocode_statistics', 'apns_owner', 'apns_user');
    
    private $db;
    private $connect;
    
    public $insert_id;
    public $errno;
    public $error;
    
    public $table = "";
    public $where = "";
    public $limit = "";
    public $order = "";
    public $group = "";
    
    public $sql_string = "";
    
    public function __construct($id_db = 'myphotocode_web') {
        require_once G_PATH . 'common/connect.php';
        
        if(in_array($id_db, $this->id_db_array)){
            $this->connect = new connect($id_db);
        } else {
            throw new Exception("Wrong Database id");
        }
//        $this->db = $this->connect->connection();
    }
    
    public function getConnect() {
        return $this->connect;
    }
    
    public function createDBbackup() {
        $result = false;
        $result = $this->connect->createBackup();
        
        return $result;
    }
    
    public function my_query($sql){
        
        $sql .= " {$this->where}";
        $sql .= " {$this->group}";
        $sql .= " {$this->order}";
        $sql .= " {$this->limit}";
              
        $db = $this->connect->connection();
        $this->sql_string = $sql;
        $result = $db->query($sql);
        $this->clear_sql_operators();
        
        $this->insert_id = $db->insert_id;
        
        
        if(!$result){
            $this->errno = $db->errno;            
            $this->error = $db->error;
            
            utils::log("Error {$this->errno}:", 'logModel');
            utils::log("{$this->error}", 'logModel');
            utils::log($sql, 'logModel');
        }
        
        $this->connect->close_connection($db);
        
        return $result;
    }
    
    public function my_query_debug($sql){
        
        $sql .= " {$this->where}";
        $sql .= " {$this->group}";
        $sql .= " {$this->order}";
        $sql .= " {$this->limit}";
          
        $db = $this->connect->connection();
        $this->sql_string = $sql;
        $result = $db->query($sql);
        $this->clear_sql_operators();
        
        $this->insert_id = $db->insert_id;
        
        
        if(!$result){
            
            $this->errno = $db->errno;            
            $this->error = $db->error;
            
             
            
            
            print $this->errno;
            utils::log("Error {$this->errno}:", 'logModel');
            utils::log("{$this->error}", 'logModel');
            utils::log($sql, 'logModel');
        }
         
        
        $this->connect->close_connection($db);
        
        return $result;
    }
    
    public function get_insert_id(){
        return $this->insert_id;
    }

    public function my_fetch_array($query) {
        $result = false;
        try{
            if($query){
                $result = $query->fetch_array();
            }
        }
        catch (Exception $e){
            utils::log("$e", 'logModel');
        }
        
        return $result;
    }

    public function my_insert_id() {
        return $this->insert_id;
    }

    public function db() {
        return $this->connect->open_connection();
    }
    
    //===================== SQL GENERATOR Functions ==================
     /**
     * Function to do a select
     * 
     * @param String $table The table you want to select
     * @param String $dataSet The dataSet you want to select by default all
     * @return mixed False if query failed.<br />
     * 0, if no results.<br />
     * 1, if single result.<br />
     * Array of entity objects if multiple results.<br />
     */
    public function select($table, $dataSet = 'all'){
        $this->entity->loadEntity($table);
        
        $sql = "
            SELECT {$this->entity->getEntityFields($dataSet)}
            FROM `{$table}`
        ";
            
        $query = $this->my_query($sql);
          
        if($query){
            $result = $this->requestQueryResults($query, $dataSet);
        }
    
        return ($query? $result : $query);
    }
public function select_debug($table, $dataSet = 'all'){
        $this->entity->loadEntity($table);
        
        $sql = "
            SELECT {$this->entity->getEntityFields($dataSet)}
            FROM `{$table}`
        ";
            
        $query = $this->my_query_debug($sql);
          
        if($query){
            $result = $this->requestQueryResults($query, $dataSet);
        }
    
        return ($query? $result : $query);
    }

    
    /**
     * Function to do an update
     * 
     * @param String $table The table to insert into
     * @param Array $values Associative array containing the values
     * @param String $dataSet [optional] A String containing the dataset
     * 
     * @return boolean True if Success, false otherwise
     */
    public function update($table, $values, $dataSet = 'all'){
                
        $this->entity->loadEntity($table);

        $sql = "UPDATE `{$table}` SET ";
        
        $aDataSets = $this->entity->getEntityDataSet($dataSet);
        $num = count($values);
        
        for($value = current($values); ($value !== FALSE || is_null($value)); $value = current($values)){
            if($value === NULL) $sql .= "`" . key($values) . "` = NULL";
            else $sql .= "`" . key($values) . "` = {$this->prepare_string($value)} ";
            if($i < $num - 1){
                $sql .= ", ";
            }
            $i++;
            next($values);
        }       
        $query = $this->my_query($sql);
        
        return $query;
    }
    
    
    public function update_debug($table, $values, $dataSet = 'all'){
                
        $this->entity->loadEntity($table);

        $sql = "UPDATE `{$table}` SET ";
        
        $aDataSets = $this->entity->getEntityDataSet($dataSet);
        $num = count($values);
        
        for($value = current($values); ($value !== FALSE || is_null($value)); $value = current($values)){
            if($value === NULL) $sql .= "`" . key($values) . "` = NULL";
            else $sql .= "`" . key($values) . "` = {$this->prepare_string($value)} ";
            if($i < $num - 1){
                $sql .= ", ";
            }
            $i++;
            next($values);
        }
        
        $query = $this->my_query_debug($sql);
        
        return $query;
    }
    
    /**
     * Function to do a delete
     * 
     * @param String $table The table to delete
     *
     * @return boolean True if Success, false otherwise
     */
    public function delete($table){
        $this->entity->loadEntity($table);

        $sql = "DELETE FROM `{$table}` ";

        $query = $this->my_query($sql);
        
        return $query;
    }
    
    
    /**
     * Function to do an insert, catch the actual values in the entity associated with the table
     * 
     * @param String $table The table to delete into
     * @param String $dataSet [optional] A String containing the dataset
     * 
     * @return boolean 
     *      True if Success and not return ID.
     *      If ID, returns it.
     *      False otherwise
    */
    public function insert($table, $dataSet = 'all'){
        $this->entity->changeEntity($table);

        $contents = $this->entity->getAllValues($dataSet);
        $fields = array();
        $values = array();
        
        for($value = current($contents); ($value !== FALSE || is_null($value)); $value = current($contents)){
            if(!is_null($value)){
                array_push($fields, key($contents));
                array_push($values, $value);
            }
            next($contents);
        }
        
        $count = count($fields);
        
        $sql = "INSERT INTO `{$table}` (";
        $i = 0;
        foreach ($fields as $field){
            $sql .= "`{$field}`";
            if($i < $count-1){
                $sql .= ", ";
            }
            $i++;
        }
        $sql .= ") VALUES (";
        $i = 0;
        foreach ($values as $value){
            $sql .= "{$this->prepare_string($value)}";
            if($i < $count-1){
                $sql .= ", ";
            }
            $i++;
        }
        $sql .= ")";
        
        $query = $this->my_query($sql);
        
        return ($query? (($this->my_insert_id()==0)? true : $this->my_insert_id()) : $query);
    }
    
    /**
     * Function to set the GROUP BY clausule in the queries
     * 
     * @param String $field String representation of the field you want to group by   
     */
    public function setGroup($field) {
        $this->group = "GROUP BY {$field}";
    }
    
    /**
     * Function to set the LIMIT clausule in the queries
     * 
     * @param String $limit String representation of the limit 
     */
    public function setLimit($limit) {
        $this->limit = "LIMIT {$limit}";
    }
    
    /**
     * Function to set the LIMIT clausule in the queries
     * 
     * @param String $limit String representation of the limit 
     */
    public function setLimitAndNumber($limit, $number) {
        $this->limit = "LIMIT {$limit}, {$number}";
    }
    
    /**
     * Function to set the ORDER BY clausule in the queries
     * 
     * @param String $field String representation of the value you want to order by
     * @param String $order ASC or DESC
     */
    public function setOrder($field, $order = "ASC") {
        if($field == 'RAND()'){
            $this->order = "ORDER BY {$field} {$order}";
        } else {
            $this->order = "ORDER BY `{$field}` {$order}";
        }
    }
    
    /**
     * Function to set the Where clausule in the models. Also used to set the IS NULL and IS NOT NULL clausules
     * 
     * @param String $field The field to set the filter
     * @param String $operation The operation to use
     * @param String $value The value you want to compare If value is NULL don't prepare string
     * @param String $concat By default is OR, could be also AND 
     */
    public function setFilter($field, $operation, $value, $concat = 'OR') {
        if($this->where == ""){
            $this->where .= " WHERE";
        }
        else{
            $this->where .= " {$concat}";
        }
        
        if($value == "NULL") $this->where .= " {$field} {$operation} {$value}";
        else $this->where .= " {$field} {$operation} {$this->prepare_string($value)}";
    }
    
    /**
     * Function to set the between Where clausule in the models
     * 
     * @param String $field The field to set the filter
     * @param String $firstValue The operation to use
     * @param String $operation The operation that you want to do ('AND' or 'OR')
     * @param String $secondValue The value you want to compare
     * @param String $concat By default is AND, could be also OR 
     */
    public function setBetweenFilter($field, $firstValue, $operation, $secondValue, $concat = 'AND', $noPreparedField = false) {
        if($this->where == ""){
            $this->where .= " WHERE";
        }
        else{
            $this->where .= " {$concat} ";
        }
        
        $this->where .= ($this->table==""? " " : " ".($this->prepare_field($this->table) . "."));
        
        $this->where .= ($noPreparedField==true?"'".$field."'" : "{$this->prepare_field($field)}");
        
        $this->where .= " BETWEEN {$this->prepare_string($firstValue)} {$operation} {$this->prepare_string($secondValue)}";
    }
    
    public function setTable($table){
        $this->table = $table;
    }
    
    
    /**
     * Function to set the IN Where clausule in the models
     * 
     * @param String $field The field to set the filter
     * @param Array  $array The values to set the filter
     * @param String $concat By default is OR, could be also AND 
     */
    public function setInFilter($field, $array, $concat = 'AND') {
        if($this->where == ""){
            $this->where .= " WHERE";
        }
        else{
            $this->where .= " {$concat}";
        }
        
        $this->where .= " {$field} IN ({$this->buildInValuesFromArray($array)})";
    }
    
    /**
     * Function to set the NOT IN Where clausule in the models
     * 
     * @param String $field The field to set the filter
     * @param Array  $array The values to set the filter
     * @param String $concat By default is OR, could be also AND 
     */
    public function setNotInFilter($field, $array, $concat = 'AND') {
        if($this->where == ""){
            $this->where .= " WHERE";
        }
        else{
            $this->where .= " {$concat}";
        }
        
        $this->where .= " {$field} NOT IN ({$this->buildInValuesFromArray($array)})";
    }
    
    /**
     * Function to concat SQL directly to the WHERE clausule
     * 
     * @param String $sql SQL to concat
     * @param String $concat By default is OR, could be also AND 
     */
    public function concatWhere($sql, $concat = 'AND') {
        if($this->where == ""){
            $this->where .= " WHERE";
        }
        else{
            $this->where .= " {$concat}";
        }
        
        $this->where .= " " . $sql;
    }
    
    //===================== SQL UTILS Functions ==================
    
    /**
     * Function to save the query results in an entity or an array of them, important if using only a single entity it has to be defined from before.
     * 
     * @param type $query
     * @param Mixed $dataSets String containing a single datatSet or an array containing the dataSets ordered by the $multipleEntities array if null it would be considered as an array containig all in all it's positions
     * @param Array $multipleEntities Containing the name of the (multiple) Entities to fill
     * @return type
     */
    public function requestQueryResults($query, $dataSets = "all", $multipleEntities = false){
        $customizedDataSet = false;
        $dataSets_array = array();
        $events_array = array();
        $result = 0;
        $totalEntities = 1;
        
        if($multipleEntities !== false){
            if(is_array($dataSets)){ 
                $customizedDataSet = true;
                $dataSets_array = $dataSets;
            }
            
            foreach($multipleEntities as $entity){
                $events_array[$entity] = array();
                if(!$customizedDataSet) array_push($dataSets_array, 'all');
            }
        }
        $test = 0;
        while($rowS = $this->my_fetch_array($query)){
            $test++;
            //for multiple entities case
            $numEntities = 0;
            if($multipleEntities !== false) $totalEntities = count($multipleEntities);
            $i = 0;
            while($numEntities < $totalEntities){
                if($multipleEntities !== false){ 
                    $entity_name = $multipleEntities[$numEntities];
                    $dataSet = $dataSets_array[$numEntities];
                }
                else{ 
                    $entity_name = $this->entity->getCurrentEntity();
                    $dataSet = $dataSets;
                }
                
                $this->entity->loadEntity($entity_name);
                $aDataSets = $this->entity->getEntityDataSet($dataSet);
                $num = count($aDataSets);
                $x = 0;
                while($x < $num){
                    $this->entity->setValue($aDataSets[$x], $rowS[$i]);
                    $i++;
                    $x++;
                }

                if($multipleEntities !== false) array_push($events_array[$multipleEntities[$numEntities]], $this->entity->getAllValues($dataSet));
                else array_push($events_array, $this->entity->getAllValues($dataSet));
                $numEntities++;
            }
        }
        $result = $events_array;
        
        return $result;
    }
    
    
    private function buildInValuesFromArray($array) {
        for ($i = 0; $i < count($array); $i++) {
            $values .= $this->prepare_string($array[$i]) . "," ;
        }
        
        return substr( $values , 0 , -1);
    }
    
    public function prepare_string($string){
        $string = utf8_decode($string);
        $string = addslashes($string);
        $string = "'" . $string . "'";
        
        return $string;
    }
    
    public function prepare_field($string){
        $string = utf8_decode($string);
        $string = addslashes($string);
        $string = "`" . $string . "`";
        
        return $string;
    }
    
    public function clear_sql_operators(){
        $this->where = "";
        $this->group = "";
        $this->order = "";
        $this->limit = "";
    }
    
    /**
     * Function to get the last SQL executed by the model
     * @return String Containin¡g the same SQL sentence executed
     */
    public function get_sql_string(){
        return $this->sql_string;
    }
}
