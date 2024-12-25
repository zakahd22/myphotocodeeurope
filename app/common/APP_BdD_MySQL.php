<?php
class BdD {
    private $conn;
    private $sql;
    private $rs;
    private $okRs;
    private $row;//nom?s a MySQL
    private $rowArray;
    public $okBase;
    public $errno;
    public $error;


    function __construct(){ 


    }
    function OpenBdD($dsn , $user , $password, $myDatabase ){

    $this->conn = mysqli_connect($dsn , $user , $password,$myDatabase);
    // Check connection
    if (mysqli_connect_errno())
    {
        $this->okBase = false;
        $this->error = "Error Open DB" . mysqli_connect_error();

    //  echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }    

    else{
        $this->okBase = true;
    }


        return TRUE;
    }

    function SetTimeOut($secs){
        mysqli_options($this->conn,MYSQLI_OPT_CONNECT_TIMEOUT,$secs);

    }

    function ExistTable($table){
    //echo "ExistTable($table)<br/>";
        $tmprs=mysqli_query($this->conn,"SELECT * FROM $table");
        if (!$tmprs){
            //echo "!tmprs(SELECT * FROM $table)<br/>";
            return FALSE;
        }
        mysqli_free_result($tmprs);
        return TRUE;
    }

    function OpenRs($sql){
    //echo "OpenRs($sql)<br/>";
        $this->rs=mysqli_query($this->conn,$sql);
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

    function FetchRs($row = NULL){
        if($row){
            if(mysqli_data_seek ($this->rs , $row )){

                return $this->row = mysqli_fetch_row($this->rs); 
            }
            else return false;
        }
        else
            return $this->row = mysqli_fetch_row($this->rs); 
    }
    function SeekRs($row){
        if($row >= mysqli_num_rows ($this->rs)) return true;

        if(mysqli_data_seek ($this->rs , $row )){
            return true;
        }
        else return false;
    }
    function GetRsRows(){
        return mysqli_num_rows ($this->rs);
    }
    function CloseRs(){//no caldr� que es cridi
        return mysqli_free_result ($this->rs);

    }

    function GetField($field){
        return $this->row[$field-1];
    }

    function GetFieldDate($field){
        $tmp = $this->row[$field-1];
        if($tmp){
            $laData =new DateTime($tmp);
            return $laData->format("Ymd");
        }
        else return "";


    }

    function GetFieldDateTime($field){
        $tmp = $this->row[$field-1];
        if($tmp){
            return new DateTime($tmp);
        }
        else return null;
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


    function GetFieldLength($field){

        $finfo = mysqli_fetch_field_direct($this->rs, $field-1);
        $length = $finfo->length;

        return $length;

    }


    function Execute($sql){//la farem servir per a UPDATE i DELETE

        if(mysqli_query($this->conn,$sql)){

            return 1;
        }
        else{
            $this->errno = mysqli_errno($this->conn);

            $this->error = mysqli_error($this->conn);


            return 0;
        }
    }

    function ExecuteInsert($sql){//a MySQL retornar� el id insertat
        if(mysqli_query($this->conn,$sql)){
            return mysqli_insert_id($this->conn);
        }
        else{
            $this->errno = mysqli_errno($this->conn);
            $this->error = mysqli_error($this->conn);
            return 0;
        }
    }
    //202009 INICI
    function ExecuteAffected($sql) {//a MySQL retornarà el nombre de registres modificats
        if(mysqli_query($this->conn,$sql)){//a anat bé
            return mysqli_affected_rows ($this->conn);
        } else {
            $this->errno = mysqli_errno($this->conn);
            $this->error = mysqli_error($this->conn);
            return 0;
        }
    }
    //202009 FINAL



    function myDateSerial($aaaammdd,$top = 0){
     // '0000-00-00 00:00:00'
        if(strlen($aaaammdd) != 8) return "myDateSerialError";
        if($top)
            return "'".substr($aaaammdd,0,4)."-".substr($aaaammdd,4,2)."-".substr($aaaammdd,6,2)." 23:59:59'";
        else
            return "'".substr($aaaammdd,0,4)."-".substr($aaaammdd,4,2)."-".substr($aaaammdd,6,2)."'";
    }

    //única funció aprofitada de QR
    function myDateSerialFromDateTime(DateTime $quan,$top = 0){
       if(!$quan) return "error";
       if($top)
            return "'".$quan->format("Y-m-d"). " 23:59:59'";
       else
            return $quan->format("'Y-m-d'");
    }

    function myDateSerialTop($aaaammdd){
        return $this->myDateSerial($aaaammdd,1);
    }

    function myDateTimeSerialBottom(DateTime $quan){
        if(!$quan) return "error";
        return "'".$quan->format("Y-m-d")."'";
    }
    function myDateTimeSerialTop(DateTime $quan){
        if(!$quan) return "error";
        return "'".$quan->format("Y-m-d"). " 23:59:59'";
    }


    function myDateSerialFormated($strDate, $idioma = "en",$top = 0){
        $ret = "'";// ha de ser Y-m-d
        //array explode ( string $delimiter , string $string [, int $limit ] )
        switch($idioma){
            case "en":// m-d-Y
                $arrDate = explode ( "-" , $strDate);
                if(count($arrDate) != 3) return "myDateSerialError2";
                $ret.= $arrDate[2]."-".$arrDate[0]."-".$arrDate[1];
            break;
            case "de":// d.m.Y
                $arrDate = explode ( "." , $strDate);
                if(count($arrDate) != 3) return "myDateSerialError2";
                $ret.= $arrDate[2]."-".$arrDate[1]."-".$arrDate[0];
            break;
            default:// d/m/Y
                 $arrDate = explode ( "/" , $strDate);
                if(count($arrDate) != 3) return "myDateSerialError2";
                $ret.= $arrDate[2]."-".$arrDate[1]."-".$arrDate[0];



       }
        if($top)
            $ret.=" 23:59:59'";
        else
            $ret.="'";

        return $ret;
    }


    function myDateTimeSerial(DateTime $quan,$top = 0){
       if(!$quan) return "error";//20120914
       if($top)
            return "'".$quan->format("Y-m-d"). " 23:59:59'";
       else
            return $quan->format("'Y-m-d H:i'");
    }
    function myDateTimeSerialFull($aaaammddhhmmss){


        if(strlen($aaaammddhhmmss) != 14) return "myDateSerialError";
        return "'".substr($aaaammddhhmmss,0,4)."-".substr($aaaammddhhmmss,4,2)."-".substr($aaaammddhhmmss,6,2)." ".substr($aaaammddhhmmss,8,2).":".substr($aaaammddhhmmss,10,2).":".substr($aaaammddhhmmss,12,2)."'"; 

    }


    function myTopSelect1($top){
        return " TOP $top";//MS SQL
        return "";
    }

    function myTopSelect2($top){
        return "";//MS SQL
        return " LIMIT 0,$top ";

    }

}//end de la classe
?>
