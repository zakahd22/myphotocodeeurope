<?php
class connect {
    private $id_db;
    private $host, $user, $pass, $database, $charset;
    
    public function __construct($id_db = 'myphotocode_web') {
        require 'config/config.php';
        
        $this->id_db = $id_db;
        
        $this->host = ${"DB_$id_db"}["host"];
        $this->user = ${"DB_$id_db"}["user"];
        $this->pass = ${"DB_$id_db"}["pass"];
        $this->database = ${"DB_$id_db"}["database"];
        $this->charset = ${"DB_$id_db"}["charset"];
    }
    
    public function connection() {
        $db = new mysqli($this->host, $this->user, $this->pass, $this->database);
        if ($db->connect_error) {
            die('Error de conexión: ' . $db->connect_error);
        } else {
            $db->query("SET NAMES '" . $this->charset . "'");
            return $db;
        }
    }
    
    public function close_connection($db) {
        $closed = mysqli_close($db);
                
        return $closed;
    }
    
    public function createBackup() {
        $date = date('YmdHis');
        
        $file_name = "{$this->id_db}_{$date}.sql";
        $file_rel_path = "app/myphotocodeManager/tmp/{$file_name}";
        $file_abs_path = G_PATH . "app/myphotocodeManager/tmp/{$this->id_db}_{$date}.sql";
        
        exec("mysqldump -u {$this->user} -p{$this->pass} -h {$this->host} {$this->database} > {$file_abs_path}");
        
        return $file_rel_path;
    }
}
