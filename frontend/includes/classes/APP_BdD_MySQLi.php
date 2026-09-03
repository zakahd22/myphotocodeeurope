<?php

class BdD {

    private $conn;
    private $sql;
    private $rs;
    private $okRs;
    private $row; //nom?s a MySQL
    private $rowArray; //nom?s a MySQL
    public $okBase;
    public $errno;
    public $error;

//    public function __construct($dsn, $user, $password, $myDatabase) {
//        $this->okBase = false;
//        //echo "TRACE BdD,dsn; $dsn,user: $user ,password: $password <br/>";
//        $this->conn = mysql_connect($dsn, $user, $password);
//        //echo "<p>TRACEBdD_obre, laConn: $laConn</p>";
//        //echo "TRACE ?<br/>";
//
//        if (!$this->conn) {
//            $this->okBase = false;
//            $this->error = "Error Connecting to host";
//            //echo "TRACE ko<br/>";
//            return FALSE;
//        }
//        if (strlen($myDatabase)) {
//            //20100425????????? era true???       if (mysql_select_db($myDatabase, $this->conn)){
//            if (!mysql_select_db($myDatabase, $this->conn)) {//20100425?????????
//                $this->okBase = false;
//                $this->error = "Error Open DB";
//                //echo "TRACE ko<br/>";
//                return FALSE;
//            }
//        }
//        $this->okBase = true;
//        //echo "TRACE ok<br/>";
//        return TRUE;
//    }

    public function __construct() {
        
    }
    
    public function OpenBdD($dsn, $user, $password, $myDatabase) {
        
        $this->okBase = false;
        $this->conn = mysqli_connect($dsn, $user, $password, $myDatabase);
//        error_log(mysqli_connect);
        if (!$this->conn) {
            $this->okBase = false;
            $this->error = "Error Connecting to host. Error n. " . mysqli_connect_error();
            return FALSE;
        }
        //error_log($myDatabase);
        if (strlen($myDatabase)) {
            if (!mysqli_select_db($this->conn, $myDatabase)) {
                $this->okBase = false;
                $this->error = "Error Open DB";
                return FALSE;
            }
        }
        $this->okBase = true;
        return TRUE;
    }

    public function ExistTable($table) {
        $tmprs = mysqli_query($this->conn, "SELECT * FROM $table");
        if (!$tmprs) {
            return FALSE;
        }
        mysqli_free_result($tmprs);
        return TRUE;
    }

    /*
    public function OpenRs($sql){
        $this->rs=mysqli_query($this->conn, $sql);
        if (!$this->rs){
            $this->okBase = false;
            $this->error = "Error OpenRs($sql)";
            $this->errno = mysqli_errno($this->conn);
            $this->error = mysqli_error($this->conn);
            return FALSE;
        }
        $this->okBase = true;
        return TRUE;
    }
    */

    public function OpenRs($sql, $template=null, ...$params){
        if ($template) {
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, $template, ...$params);
            mysqli_stmt_execute($stmt);
            $this->rs = mysqli_stmt_get_result($stmt);
        } else {
            $this->rs = mysqli_query($this->conn, $sql);
        }
        
        if (!$this->rs){
            $this->okBase = false;
            $this->error = "Error OpenRs($sql)";
            $this->errno = mysqli_errno($this->conn);
            $this->error = mysqli_error($this->conn);
            return FALSE;
        }
        $this->okBase = true;
        return TRUE;
    }

    public function FetchRs($row = NULL) {
        if ($row) {
            if (mysqli_data_seek($this->rs, $row)) {
                return $this->row = mysqli_fetch_row($this->rs);
            } else
                return false;
        } else
            return $this->row = mysqli_fetch_row($this->rs);
    }

    public function SeekRs($row) {
        if ($row >= mysqli_num_rows($this->rs))
            return true;
        if (mysqli_data_seek($this->rs, $row)) {
            return true;
        } else
            return false;
    }

    public function GetRsRows() {
        return mysqli_num_rows($this->rs);
    }

    public function CloseRs() {
        try {
            mysqli_free_result($this->rs);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        return true;
    }

    public function GetField($field) {
        return $this->row[$field - 1];
    }

    public function GetFieldDate($field) {
        return $this->row[$field - 1];
    }

    public function GetFieldDateTime($field) {
        $tmp = $this->row[$field - 1];
        if ($tmp) {
            return new DateTime($tmp);
        } else
            return null;
    }

    public function FetchArray($row = NULL) {
        if ($row) {
            if (mysqli_data_seek($this->rs, $row)) {
                return $this->rowArray = mysqli_fetch_array($this->rs);
            } else
                return false;
        } else
            return $this->rowArray = mysqli_fetch_array($this->rs);
    }

    public function GetArrayField($colname) {
        return $this->rowArray[$colname];
    }

    public function GetArrayFieldDate($colname) {
        return $this->rowArray[$colname];
    }

    public function GetArrayFieldDateTime($colname) {
        $tmp = $this->rowArray[$colname];
        if ($tmp) {
            return new DateTime($tmp);
        } else
            return null;
    }

    
    /*
    public function Execute($sql) {
        if (mysqli_query($this->conn, $sql)) {
            return 1;
        } else {
            $this->errno = mysqli_errno($this->conn);
            $this->error = mysqli_error($this->conn);
            return 0;
        }
    }
    */
    
    
    public function Execute($sql, $template=null, ...$params) {
        if ($template) {
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, $template, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        } else {
            $result = mysqli_query($this->conn, $sql);
        }
        
        if ($result) {
            return 1;
        } else {
            $this->errno = mysqli_errno($this->conn);
            $this->error = mysqli_error($this->conn);
            return 0;
        }
    }
    

    /*
    function ExecuteInsert($sql) {
        if (mysqli_query($this->conn, $sql)) {
            return mysqli_insert_id($this->conn);
        } else {
            $this->errno = mysqli_errno($this->conn);
            $this->error = mysqli_error($this->conn);
            return 0;
        }
    }
    */
    
    
    function ExecuteInsert($sql, $template=null,  ...$params) {
        if ($template) {
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, $template, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        } else {
            $result = mysqli_query($this->conn, $sql);
        }
        if ($result) {
            return mysqli_insert_id($this->conn);
        } else {
            $this->errno = mysqli_errno($this->conn);
            $this->error = mysqli_error($this->conn);
            return 0;
        }
    }
    
    

    function myDateSerial($aaaammdd, $top = 0) {
        if (strlen($aaaammdd) != 8)
            return "myDateSerialError";
        if ($top)
            return "'" . substr($aaaammdd, 0, 4) . "-" . substr($aaaammdd, 4, 2) . "-" . substr($aaaammdd, 6, 2) . " 23:59:59'";
        else
            return "'" . substr($aaaammdd, 0, 4) . "-" . substr($aaaammdd, 4, 2) . "-" . substr($aaaammdd, 6, 2) . "'";
    }

    function myDateTimeSerial(DateTime $quan, $top = 0) {
        if (!$quan)
            return "error"; 
        if ($top)
            return "'" . $quan->format("Y-m-d") . " 23:59:59'";
        else
            return $quan->format("'Y-m-d H:i:s'");
    }

    function myDateTimeSerialFull($aaaammddhhmmss) {
        if (strlen($aaaammddhhmmss) != 14)
            return "myDateSerialError";
        return "'" . substr($aaaammddhhmmss, 0, 4) . "-" . substr($aaaammddhhmmss, 4, 2) . "-" . substr($aaaammddhhmmss, 6, 2) . " " . substr($aaaammddhhmmss, 8, 2) . ":" . substr($aaaammddhhmmss, 10, 2) . ":" . substr($aaaammddhhmmss, 12, 2) . "'";
    }
}