<?php
class BdD {
    private $conn;
    private $sql;
    private $rs;
    private $okRs;
    private $row;//nom?s a MySQL
    private $rowArray;//nom?s a MySQL
    public $okBase;
    public $errno;
    public $error;


function BdD(){


}
function OpenBdD($dsn , $user , $password, $myDatabase ){
    $this->okBase = false;
//echo "TRACE BdD,dsn; $dsn,user: $user ,password: $password <br/>";
    $this->conn=mysql_connect($dsn , $user , $password);
    //echo "<p>TRACEBdD_obre, laConn: $laConn</p>";
//echo "TRACE ?<br/>";

    if (!$this->conn){
        $this->okBase = false;
        $this->error = "Error Connecting to host";
//echo "TRACE ko<br/>";
        return FALSE;
    }
    if(strlen($myDatabase)){
//20100425????????? era true???       if (mysql_select_db($myDatabase, $this->conn)){
        if (!mysql_select_db($myDatabase, $this->conn)){//20100425?????????
            $this->okBase = false;
            $this->error = "Error Open DB";
    //echo "TRACE ko<br/>";
            return FALSE;
        }
    }
    $this->okBase = true;
//echo "TRACE ok<br/>";
    return TRUE;
}

function ExistTable($table){
//echo "ExistTable($table)<br/>";
    $tmprs=mysql_query("SELECT * FROM $table");
    if (!$tmprs){
        //echo "!tmprs(SELECT * FROM $table)<br/>";
        return FALSE;
    }
    mysql_free_result ($tmprs);
    return TRUE;
}

function OpenRs($sql){
//echo "OpenRs($sql)<br/>";
    $this->rs=mysql_query($sql);
    if (!$this->rs){
        $this->okBase = false;
        $this->error = "Error OpenRs($sql)";
        $this->errno = mysql_errno();
        $this->error = mysql_error();
        return FALSE;
    }
    $this->okBase = true;
    return TRUE;
}

function FetchRs($row = NULL){
    if($row){
        if(mysql_data_seek ($this->rs , $row )){
            return $this->row = mysql_fetch_row($this->rs);
        }
        else return false;
    }
    else
        return $this->row = mysql_fetch_row($this->rs);
}
//20110414 INICI  !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
function SeekRs($row){
    if($row >= mysql_num_rows ($this->rs)) return true;
    if(mysql_data_seek ($this->rs , $row )){
        return true;
    }
    else return false;
}
function GetRsRows(){
    return mysql_num_rows ($this->rs);
}
//20110414 FINAL  
function CloseRs(){//no caldr� que es cridi
    return mysql_free_result ($this->rs);
}

function GetField($field){
    return $this->row[$field-1];
}

//Nota: crec que no caldr? perqu? odbc_ de php sempre retorna aaaammdd que ?s el que volem
function GetFieldDate($field){
    return $this->row[$field-1];
}

function GetFieldDateTime($field){
    $tmp = $this->row[$field-1];
    if($tmp){
        return new DateTime($tmp);
    }
    else return null;
}
//$phpdate = strtotime( $mysqldate );
//$mysqldate = date( 'Y-m-d H:i:s', $phpdate );

//201308 INICI fetch_array
function FetchArray($row = NULL){
    if($row){
        if(mysql_data_seek ($this->rs , $row )){
            return $this->rowArray = mysql_fetch_array($this->rs);
        }
        else return false;
    }
    else
        return $this->rowArray = mysql_fetch_array($this->rs);
}
function GetArrayField($colname){
    return $this->rowArray[$colname];
}

//Nota: crec que no caldr? perqu? odbc_ de php sempre retorna aaaammdd que ?s el que volem
function GetArrayFieldDate($colname){
    return $this->rowArray[$colname];
}

function GetArrayFieldDateTime($colname){
    $tmp = $this->rowArray[$colname];
    if($tmp){
        return new DateTime($tmp);
    }
    else return null;
}

//201308 FINAL fetch_array


function Execute($sql){//la farem servir per a UPDATE i DELETE
    if(mysql_query($sql)){//a anat b�
        return 1;
    }
    else{
        $this->errno = mysql_errno();
        $this->error = mysql_error();
        return 0;
    }
}

function ExecuteInsert($sql){//a MySQL retornar� el id insertat
    if(mysql_query($sql)){//a anat b�
        return mysql_insert_id();
    }
    else{
        $this->errno = mysql_errno();
        $this->error = mysql_error();
        return 0;
    }
    
}



function myDateSerial($aaaammdd,$top = 0){
 // '0000-00-00 00:00:00'
    if(strlen($aaaammdd) != 8) return "myDateSerialError";
    if($top)
        return "'".substr($aaaammdd,0,4)."-".substr($aaaammdd,4,2)."-".substr($aaaammdd,6,2)." 23:59:59'";
    else
        return "'".substr($aaaammdd,0,4)."-".substr($aaaammdd,4,2)."-".substr($aaaammdd,6,2)."'";
}
//20110322 INICI afegim l'opci� top
//function myDateTimeSerial(DateTime $quan){
//   return $quan->format("Y-m-d");// H:i:s
// }
 
function myDateTimeSerial(DateTime $quan,$top = 0){
   if(!$quan) return "error";//20120914
   if($top)
        return "'".$quan->format("Y-m-d"). " 23:59:59'";
   else
//20120914        return $quan->format("'Y-m-d'");
        return $quan->format("'Y-m-d H:i'");//20120914
}
//20110322 FINAL
function myDateTimeSerialFull($aaaammddhhmmss){
    
//    return $aaaammddhhmmss;//TRACE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    
    if(strlen($aaaammddhhmmss) != 14) return "myDateSerialError";
    return "'".substr($aaaammddhhmmss,0,4)."-".substr($aaaammddhhmmss,4,2)."-".substr($aaaammddhhmmss,6,2)." ".substr($aaaammddhhmmss,8,2).":".substr($aaaammddhhmmss,10,2).":".substr($aaaammddhhmmss,12,2)."'"; 
        
}

}//end de la classe
?>
