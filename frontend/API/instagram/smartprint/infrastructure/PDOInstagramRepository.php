<?php


namespace instagram\smartprint\infrastructure;

use instagram\smartprint\domain\InstagramUser;
use instagram\smartprint\domain\InstagramUserBuilder;
use instagram\smartprint\infrastructure\IGUserDTO;
use Exception;
use PDO;
use utils;

// require_once $_SERVER['DOCUMENT_ROOT'] . "/myphotocode/API/instagram/smartprint/domain/InstagramUser.php"; //maquina local
// require_once $_SERVER['DOCUMENT_ROOT'] . "/myphotocode/API/instagram/smartprint/domain/InstagramUserBuilder.php"; //maquina local
//require_once $_SERVER['DOCUMENT_ROOT'] . "/common/conexio_mysqli.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/common/conexio.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/API/instagram/smartprint/domain/InstagramUser.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/API/instagram/smartprint/domain/InstagramUserBuilder.php";

//TODO: hauria d'estar amb LOCAL_PATH pero no podem perque esta definti al propi ig_config.php
//no funciona-->require_once $_SERVER['DOCUMENT_ROOT'] . LOCAL_PATH . '/API/instagram/smartprint/config/ig_config.php';
//require_once $_SERVER['DOCUMENT_ROOT'] . "/myphotocode/API/instagram/smartprint/config/ig_config.php"; //maquina local
require_once $_SERVER['DOCUMENT_ROOT'] . "/API/instagram/smartprint/config/ig_config.php";
 
class PDOInstagramRepository {
    private $db;

    public function __construct() {
        $this->db = new PDO(
            'mysql:host='.DB_HOSTNAME.';port=3306x;dbname='.DB_DATABASE,
            DB_USER,
            DB_PASSWORD,
            array( PDO::ATTR_PERSISTENT => false)
        );
    }
    
    public function findLikeWord($word='', $type='', $numTo=50){
        $ipdat = @json_decode(file_get_contents( 
        "http://www.geoplugin.net/json.gp?ip=" . $ip));     
        $pais = $ipdat->geoplugin_countryCode;
        
        $allRows = array();
        if($type==''){
            $sqlPais = "SELECT * FROM InstagramSuggestions WHERE word LIKE CONCAT( '%', :word, '%') and pais= :pais GROUP BY `word` , `type` ORDER BY numfollowers, numPrint, numCount DESC LIMIT :numTo";
            $stmtPais = $this->db->prepare($sqlPais);
            $stmtPais->bindValue(':numTo', intval($numTo), PDO::PARAM_INT);
            $stmtPais->bindValue(':word',$word,PDO::PARAM_STR);
            $stmtPais->bindValue(':pais',$pais,PDO::PARAM_STR);            
            $stmtPais->execute();
        
           
            $sql = "SELECT * FROM InstagramSuggestions WHERE word LIKE CONCAT( '%', :word, '%') and pais!= :pais GROUP BY `word` , `type` ORDER BY numfollowers, numPrint, numCount DESC LIMIT :numTo";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':numTo', intval($numTo), PDO::PARAM_INT);
            $stmt->bindValue(':word',$word,PDO::PARAM_STR);
            $stmt->bindValue(':pais',$pais,PDO::PARAM_STR);            
            $stmt->execute();
           


        }else{

            $sql = "SELECT * FROM InstagramSuggestions WHERE word LIKE CONCAT( '%', :word, '%') and type=:type and pais!= :pais GROUP BY `word` , `type` ORDER BY numfollowers, numPrint, numCount DESC LIMIT :numTo";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':numTo', intval($numTo), PDO::PARAM_INT);
            $stmt->bindValue(':word',$word,PDO::PARAM_STR);
            $stmt->bindValue(':pais',$pais,PDO::PARAM_STR);   
            $stmt->bindValue(':type',$type,PDO::PARAM_STR);              
            $stmt->execute();
           
         

            

            $sqlPais = "SELECT * FROM InstagramSuggestions WHERE word LIKE  CONCAT( '%', :word, '%') and type=:type and pais= :pais GROUP BY `word` , `type` ORDER BY numfollowers, numPrint, numCount DESC LIMIT :numTo";
            $stmtPais = $this->db->prepare($sqlPais);
            $stmtPais->bindValue(':numTo', intval($numTo), PDO::PARAM_INT);
            $stmtPais->bindValue(':word',$word,PDO::PARAM_STR);
            $stmtPais->bindValue(':pais',$pais,PDO::PARAM_STR);
            $stmtPais->bindValue(':type',$type,PDO::PARAM_STR);
            $stmtPais->execute();
        
                         
            


        }

        $rowPais = $stmtPais->fetchall(PDO::FETCH_ASSOC);
        if( $stmtPais->rowCount() !=0 ){            
            $allRows = array_merge($allRows, $rowPais);           
        }    

        $row = $stmt->fetchall(PDO::FETCH_ASSOC);
        if( $stmt->rowCount() !=0 && $stmtPais->rowCount() <= $numTo ){
            $allRows = array_merge($allRows, $row);
            
        }
        
       
        

        if( count( $allRows) ==0 ){
            $row = 0;
        }
        
       
        return $allRows;
    }

    public function findOtherIdeas($type, $numTo=50){
        $ipdat = @json_decode(file_get_contents( 
        "http://www.geoplugin.net/json.gp?ip=" . $_SERVER['REMOTE_ADDR']));     
        $pais = $ipdat->geoplugin_countryCode;
        //$pais = getCountryByIp($_SERVER['REMOTE_ADDR']);  //peta
//        $pais = 'ES';
        $allRows = array();
        
        $sqlPais = "SELECT * FROM InstagramSuggestions WHERE pais=:pais AND type=:type GROUP BY `word` , `type` ORDER BY numfollowers, numPrint, numCount DESC LIMIT :numTo";
//        print "SELECT * FROM InstagramSuggestions WHERE pais='$pais' AND type='$type' ORDER BY numfollowers, numPrint, numCount DESC LIMIT $numTo";
        $stmtPais = $this->db->prepare($sqlPais);
        $stmtPais->bindValue(':numTo', intval($numTo), PDO::PARAM_INT);            
        $stmtPais->bindValue(':pais',$pais,PDO::PARAM_STR);   
        $stmtPais->bindValue(':type',$type,PDO::PARAM_STR);
        $stmtPais->execute();


        $sql = "SELECT * FROM InstagramSuggestions WHERE pais!=:pais AND type=:type  GROUP BY `word` , `type` ORDER BY numfollowers, numPrint, numCount DESC LIMIT :numTo";
//        print "<br>SELECT * FROM InstagramSuggestions WHERE pais!='$pais' AND type='$type' ORDER BY numfollowers, numPrint, numCount DESC LIMIT $numTo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':numTo', intval($numTo), PDO::PARAM_INT);            
        $stmt->bindValue(':pais',$pais,PDO::PARAM_STR);  
        $stmt->bindValue(':type',$type,PDO::PARAM_STR);
        $stmt->execute();
              


       
        $rowPais = $stmtPais->fetchall(PDO::FETCH_ASSOC);
//        print "pais<pre>";
//        print_r($rowPais);
        if( $stmtPais->rowCount() !=0 ){            
            $allRows = array_merge($allRows, $rowPais);           
        }    

        $row = $stmt->fetchall(PDO::FETCH_ASSOC);
        if( $stmt->rowCount() !=0 && $stmtPais->rowCount() <= $numTo){
            $allRows = array_merge($allRows, $row);
            
        }
        
        $allRows = array_slice($allRows, 0, $numTo); //retornem nomes n valors
// print "no pais";       
//        print_r($row);
//        print "no pais";       
//        print_r($allRows);
//        exit;
       
        

        if( count( $allRows) ==0 ){
            $row = 0;
        }
        
       
        return $allRows;
    }
    
    
    public function findAllSuggestion($type='', $numTo=100000){
       
        $stringTypeSQL = ""; 
        if($type!=''){
            $stringTypeSQL = " WHERE `type`=:type ";
        }

        $sql = "SELECT * FROM InstagramSuggestions ".$stringTypeSQL." GROUP BY `word` , `type` ORDER BY `type`, numfollowers, numPrint, numCount DESC LIMIT :numTo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':numTo', intval($numTo), PDO::PARAM_INT);
        if($type!=''){
            $stmt->bindValue(':type',$type,PDO::PARAM_STR);
        }
        $stmt->execute();  
        $rowCount = $stmt->rowCount();    
        $row = $stmt->fetchall(PDO::FETCH_ASSOC);
        
      
       
        
           
//        $arraySuggestions = array();
//        $arraySuggestions['suggestions'] = $row;
//        $arraySuggestions['numHashtag'] = $rowCountHash;
//        $arraySuggestions['numUsername'] = $rowCount - $rowCountHash;
//        return $arraySuggestions;
        
        return $row;
    }


    public function findByWord($word='', $type='', $pais=''){
       
        $sql = "SELECT * FROM InstagramSuggestions WHERE word=:word and type=:type and pais=:pais ORDER BY numfollowers, numPrint, numCount DESC";
        $stmt = $this->db->prepare($sql);
     
        $stmt->execute(array(
            ':word' => $word,
            ':type' => $type,
            ':pais' => $pais
        ));

        $row = $stmt->fetch();
        if( $stmt->rowCount() ==0 ){
            $row = 0;
        }        
        return $row;
    }
    
    public function findUsernameByWord($word=''){
       
        $sql = "SELECT * FROM InstagramSuggestions WHERE word=:word AND type='username' LIMIT 0, 1";
        $stmt = $this->db->prepare($sql);
     
        $stmt->execute(array(
            ':word' => $word
        ));

        $row = $stmt->fetch();
        if( $stmt->rowCount() ==0 ){
            $row = 0;
        }        
        return $row;
    }
    //el cridem a get_photos per fer update de suggestions guardades desde apartat instagram de myphotocode 
    //o desde save_suggestion (on no fem crida a scrapingbee per estalviar, per tant no guarda followers, is verified ni fbid)                                         
    public function updateSuggestion($word='', $type='', $numFollowers=0, $isVerified=0, $fbid=0) {
        

        $sql = "UPDATE InstagramSuggestions SET numFollowers= :numFollowers, isVerified=:isVerified, fbid=:fbid WHERE word=:word and type=:type";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':word' => $word,
            ':type' => $type,
            ':numFollowers' => $numFollowers,           
            ':isVerified' => $isVerified,
            ':fbid' => $fbid
        ));
    }
    
    public function updateSuggestionSenseFollowers($word='', $print=0, $type='', $pais='') {
        if($print==1){
            $sum = 0;
        }else{
            $sum = 1;
        }

        $sql = "UPDATE InstagramSuggestions SET numCount = numCount + :sum, numPrint = :print WHERE word=:word and type=:type and pais=:pais";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':word' => $word,
            ':type' => $type,
            ':sum' => $sum,
            ':print' => $print,
            ':pais' => $pais
        ));
    }


    public function insertSuggestion($word='', $print=0, $type='', $pais='', $numFollowers=0, $isVerified=0, $fbid=0) {
        if(!isset($numFollowers) || $numFollowers==''){
            $numFollowers=0;
        }
        if(!isset($isVerified) || $isVerified==''){
            $isVerified=0;
        }
                
                
                
        if($print==1){
            $sum = 0;
        }else{
            $sum = 1;
        }
        //print "inseert";
        $sql = "INSERT INTO InstagramSuggestions (word, type, numCount, numPrint, pais, numFollowers, isVerified, fbid ) VALUES (:word, :type, :sum, :print, :pais, :followers, :isVerified, :fbid ) ";  
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':word' => $word,
            ':type' => $type,
            ':sum' => $sum,
            ':print' => $print,
            ':pais' => $pais,
            ':followers' => $numFollowers,
            ':isVerified' => $isVerified,
            ':fbid' => $fbid
        ));
        if($stmt->errorCode() != 0) {
            //print_r($stmt->errorInfo()) ;
            throw new Exception($stmt->errorCode());
        }
    }
    
    
    

    public function insertSuggestionSenseFollowers($word='', $print=0, $type='', $pais='', $isVerified=0, $fbid=0) {
        if($print==1){
            $sum = 0;
        }else{
            $sum = 1;
        }
//        print "inseert:   "."INSERT INTO InstagramSuggestions (word, type, numCount, numPrint, pais, isVerified, fbid ) VALUES ('$word', '$type', '$sum', '$print', '$pais', $isVerified, $fbid )";
        
//        $CLD_CON->Execute("INSERT INTO InstagramSuggestions (word, type, numCount, numPrint, pais, isVerified, fbid ) VALUES ('$word', '$type', '$sum', '$print', '$pais', $isVerified, $fbid )");
//        print "codi instert. ".$insert;exit;
        $sql = "INSERT INTO InstagramSuggestions (word, type, numCount, numPrint, pais, isVerified, fbid ) VALUES (:word, :type, :sum, :print, :pais, :isVerified, :fbid  ) ";  
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':word' => $word,
            ':type' => $type,
            ':sum' => $sum,
            ':print' => $print,
            ':pais' => $pais,
            ':isVerified' => $isVerified,
            ':fbid' => $fbid
        ));
        if($stmt->errorCode() != 0) {
            //print_r($stmt->errorInfo()) ;
            throw new Exception($stmt->errorCode());
        }
         if($stmt->errorCode() != 0) {
            //print_r($stmt->errorInfo()) ;
            throw new Exception($stmt->errorCode());
        }
    }
    
    
    public function insertReport($id='', $mt='') {
        $sql = "INSERT INTO InstagramReportedPhotos (id, mt) VALUES (:id, :mt)";  
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':id' => $id,
            ':mt' => $mt
        ));
        if($stmt->errorCode() != 0) {
            //print_r($stmt->errorInfo()) ;
            throw new Exception($stmt->errorCode());
        }
        if($mt=="reportverified" || $mt=="reportVerified"){
           $reported = 2; //ha enviat reportVerified l'admin  
        }else{
           $reported = 1; //ho reporta un mindundi 
        }
        $sql = "UPDATE InstagramPhotoViewed SET reportVerified=:reported WHERE id=:id";  
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':id' => $id,
            ':reported' => $reported
        ));
        if($stmt->errorCode() != 0) {
            //print_r($stmt->errorInfo()) ;
            throw new Exception($stmt->errorCode());
        }
            
       
            
    }

    

    public function persist(InstagramUser $fbUser) {
        $sql = "INSERT INTO InstagramUsers (token,id_pb,num_photos,game_code) VALUES (:token, :idPB, :numPhotos, :gameCode)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $fbUser->getToken(),
            ':idPB' => $fbUser->getIdBooth(),
            ':numPhotos' => $fbUser->getNumPhotos(),
            ':gameCode' => $fbUser->getGameCode()
        ));
        if($stmt->errorCode() != 0) {
            throw new Exception($stmt->errorCode());
        }
    }

    public function findByToken($token) {
        $sql = "SELECT * FROM InstagramUsers WHERE token=:token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token
        ));
        $row = $stmt->fetch(); 

        if( $stmt->rowCount() ==0 ){
            throw new Exception("Token not found");
        }
        return (new InstagramUserBuilder())
            ->withIdBooth($row['id_pb'])
            ->withToken($row['token'])
            ->withAccessToken($row['fb_access_token'])
            ->withAllPhotosServed($row['all_photos_served'])
            ->withGameCode($row['game_code'])
            ->withLastDownloadedPhotos($row['last_downloaded_photos'])
            ->withLastPhoto($row['last_photo'])
            ->withNumPhotos($row['num_photos'])
            ->withTotalPhotos($row['total_photos'])
            ->withType($row['type'])
            ->build();
    }

    public function deleteByTokenAndIdBooth($token, $idBooth){
        
        $sql = "DELETE FROM InstagramUsers WHERE token=:token AND id_pb=:idBooth";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':idBooth' => $idBooth
        ));
    }
   

    public function getPhotoByIdCountryType($id, $pais, $type){
     
        $sql = "SELECT * FROM InstagramPhotoViewed WHERE id=:id AND pais=:pais AND type=:type";
        
        $stmt = $this->db->prepare($sql);
        // $stmt->bindParam(':id',$id,PDO::PARAM_STR);
        // $stmt->bindParam(':pais',$pais,PDO::PARAM_STR);
        // $stmt->bindParam(':type',$type,PDO::PARAM_STR);
        $stmt->execute(array(
            ':id' => $id,
            ':pais' => $pais,
            ':type' => $type
        ));
       
        $row = $stmt->fetch();      
// print "<pre>";
// print_r($row);
// print "rowcountccc:".$stmt->rowCount();

        if( $stmt->rowCount() ==0){
            $row = 0;
        }else{
            $row = 1;
        }    
        if($stmt->errorCode() != 0) {
            
            print_r($stmt->errorInfo()) ;
            throw new Exception($stmt->errorCode());
        }
//         print "id:".$id.$pais.$type;
// print_r($row);
        return $row;
    }

    public function getPhotoById($id){
     
        $sql = "SELECT * FROM InstagramPhotoViewed WHERE id=:id";        
        $stmt = $this->db->prepare($sql);        
        $stmt->execute(array(
            ':id' => $id            
        ));
       
        $row = $stmt->fetch();  

        if( $stmt->rowCount() ==0){
            $row = 0;
        }else{
            $row = 1;
        }    
        if($stmt->errorCode() != 0) {
            
            print_r($stmt->errorInfo()) ;
            throw new Exception($stmt->errorCode());
        }
        return $row;
    }

    public function insertPhotoViewed($id=0, $word='', $path='', $type='', $pais='', $numLikes=0, $text='', $fbid=0) {

        $sql = "INSERT INTO InstagramPhotoViewed (id, word, type, numPrint, numCount, pais, numLikes, photoText, fbid) VALUES (:id, :word, :type, :print, :sum, :pais, :numLikes, :photoText, :fbid) ";  
        $stmt = $this->db->prepare($sql);
        //$stmt->bindParam(':path',$path,PDO::PARAM_STR);
        //$photoText = '';
        $stmt->execute(array(
            ':id' => $id,
            ':word' => $word,
            ':type' => $type,
            //':path' => $path,            
            ':print' => 0,
            ':sum' => 0,
            ':pais' => $pais,
            ':numLikes' => $numLikes,
            ':photoText' => $text,
            ':fbid' => $fbid
        ));
        if($stmt->errorCode() != 0) {
            // print "id:".$id;
            // print_r($stmt->errorInfo()) ;
            throw new Exception($stmt->errorCode());
        }
    }

    public function updatePhotoViewed($id=0, $word='', $path='', $type='', $pais='', $numLikes=0, $numPrint=0, $numCount=0, $text='') {
       //print $word.$type.$id.$pais;


        $sql = "UPDATE InstagramPhotoViewed SET numPrint=numPrint + :print, numCount=numCount + :sum, numLikes=:numLikes, photoText=:photoText WHERE word=:word AND id=:id AND type=:type AND pais=:pais";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':id' => $id,
            ':word' => $word,
            ':type' => $type,
            ':sum' => $numCount,
            ':print' => $numPrint,
            ':pais' => $pais,
            ':numLikes' => $numLikes,
            ':photoText' => $text
        ));
        if($stmt->errorCode() != 0) {
            // print "<pre>";
            // print_r($stmt->errorInfo()) ;
            throw new Exception($stmt->errorCode());
        }
    } 

    public function savePhotoViewed($id, $word, $type, $pais, $numPrint, $numCount){
       //print $word.$type.$id.$pais;
// print $id_photo.$word.$type.$pais.$numPrint.$numCount;

        $sql = "UPDATE InstagramPhotoViewed SET numPrint=numPrint + :print, numCount=numCount + :sum WHERE word=:word AND id=:id AND type=:type AND pais=:pais";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':id' => $id,
            ':word' => $word,
            ':type' => $type,
            ':sum' => $numCount,
            ':print' => $numPrint,
            ':pais' => $pais
        ));
        if($stmt->errorCode() != 0) {
            // print "<pre>";
            // print_r($stmt->errorInfo()) ;
            throw new Exception($stmt->errorCode());
        }
    } 


    public function updateIGPhotoDownload($id=0) {       
        
        $sql = "UPDATE InstagramPhotoViewed SET downloaded=1 WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':id' => $id            
        ));
        
        if($stmt->errorCode() != 0) {
            // print "<pre>";
            // print_r($stmt->errorInfo()) ;
            throw new Exception($stmt->errorCode());
        }
    } 

       

    public function getCountryByIp($ip){
        //$ip = $_SERVER['REMOTE_ADDR']; 
        $numCarIp = mb_strlen ( $ip );
        //echo   $numCarIp;
        if($numCarIp <4){
          
          $ip='52.25.109.230';
        }    
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip)); 
        
        $pais = $ipdat->geoplugin_countryCode;
        return $pais;

    }

    public function findByTokenAndIdBooth($token, $idBooth){
        $igUser = null;
        $sql = "SELECT * FROM InstagramUsers WHERE token=:token AND id_pb=:idBooth";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':idBooth' => $idBooth
        ));
        $row = $stmt->fetch();
        if( $stmt->rowCount() ==0 ){
            throw new Exception("Id or token not found");
        }
        return (new InstagramUserBuilder())
            ->withIdBooth($row['id_pb'])
            ->withToken($row['token'])
            ->withAccessToken($row['fb_access_token'])
            ->withAllPhotosServed($row['all_photos_served'])
            ->withGameCode($row['game_code'])
            ->withLastDownloadedPhotos($row['last_downloaded_photos'])
            ->withLastPhoto($row['last_photo'])
            ->withNumPhotos($row['num_photos'])
            ->withTotalPhotos($row['total_photos'])
            ->withType($row['type'])
            ->build();
    }


    
      public function getSavedInstaPhotosNumber($word='', $type='', $pais='', $other){
       

        if($pais==''){      
        
            $sql = "SELECT * FROM InstagramPhotoViewed WHERE word=:word AND type=:type AND downloaded=1 AND reportVerified < 2";
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindValue(':word',$word,PDO::PARAM_STR);

            $stmt->bindValue(':type',$type,PDO::PARAM_STR);
        }else{  
            
            $sql = "SELECT * FROM InstagramPhotoViewed WHERE word=:word AND pais=:pais AND type=:type AND downloaded=1 AND reportVerified < 2";
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindValue(':word',$word,PDO::PARAM_STR);
            $stmt->bindValue(':pais',$pais,PDO::PARAM_STR);
            $stmt->bindValue(':type',$type,PDO::PARAM_STR);
        }  
         
            
            
           
           
             $stmt->execute();
            
            $row = $stmt->fetchAll();      
    // print "<pre>".$word.$type.$pais;
    //  print_r($sql);
    // print "rowcountvvv:".$stmt->rowCount();

           
            if($stmt->errorCode() != 0) {
                
                print_r($stmt->errorInfo()) ;
                throw new Exception($stmt->errorCode());
            }
    //         print "id:".$id.$pais.$type;
    // print_r($row);
            return $stmt->rowCount();

    }



      public function getSavedInstaPhotos($word='', $type='', $pais='', $numFrom=0, $numTo=0, $other=0){
       

        if(!$other){
            if($pais===''){
                //Eloi: Vam treure el filtre de type que no se perque però no funcionava. Però sembla que era perque no hi havia realment res a BD. si tot funciona es pot treure aquest commentari
                $sql = "SELECT * FROM InstagramPhotoViewed WHERE word=:word AND type=:type AND downloaded=1 AND reportVerified < 2 ORDER BY downloadDate DESC LIMIT :numFrom, :numTo";
                //print "SELECT * FROM InstagramPhotoViewed WHERE word=$word AND type=$type AND downloaded=1 AND reportVerified < 2 LIMIT $numFrom, $numTo";
//                $sql = "SELECT * FROM InstagramPhotoViewed WHERE word=:word  AND downloaded=1 LIMIT :numFrom, :numTo";
                $stmt = $this->db->prepare($sql);        
                $stmt->bindValue(':word',$word,PDO::PARAM_STR);
                $stmt->bindValue(':type',$type,PDO::PARAM_STR);        
                $stmt->bindValue(':numFrom', intval($numFrom), PDO::PARAM_INT);
                $stmt->bindValue(':numTo', intval($numTo), PDO::PARAM_INT);           
            }else{
                //Eloi: Vam treure el filtre de type que no se perque però no funcionava. Però sembla que era perque no hi havia realment res a BD. si tot funciona es pot treure aquest commentari
                $sql = "SELECT * FROM InstagramPhotoViewed WHERE word=:word AND type=:type AND pais=:pais AND downloaded=1 AND reportVerified < 2 ORDER BY downloadDate DESC LIMIT :numFrom, :numTo";
             //print   "SELECT * FROM InstagramPhotoViewed WHERE word=$word AND type=$type AND pais=$pais AND downloaded=1 AND reportVerified < 2 LIMIT $numFrom, $numTo";
//                $sql = "SELECT * FROM InstagramPhotoViewed WHERE word=:word  AND pais=:pais  AND downloaded=1 LIMIT :numFrom, :numTo";
                 $stmt = $this->db->prepare($sql);   
                $stmt->bindValue(':word',$word,PDO::PARAM_STR);
                $stmt->bindValue(':type',$type,PDO::PARAM_STR);  
                $stmt->bindValue(':pais',$pais,PDO::PARAM_STR);        
                $stmt->bindValue(':numFrom', intval($numFrom), PDO::PARAM_INT);
                $stmt->bindValue(':numTo', intval($numTo), PDO::PARAM_INT);   
            }

        }else{
            
            if($pais===''){
                //Eloi: Vam treure el filtre de type que no se perque però no funcionava. Però sembla que era perque no hi havia realment res a BD. si tot funciona es pot treure aquest commentari
               // $sql = "SELECT * FROM InstagramPhotoViewed WHERE word!=:word AND type=:type AND downloaded=1 AND reportVerified < 2 ORDER BY downloadDate DESC LIMIT :numFrom, :numTo";
               //les others no tornen per data, si no tornem sempre d'una sola paraula
                $sql = "SELECT * FROM InstagramPhotoViewed WHERE word!=:word AND type=:type AND downloaded=1 AND reportVerified < 2 ORDER BY numPrint DESC, numLikes DESC, numCount DESC LIMIT :numFrom, :numTo";                

                 $stmt = $this->db->prepare($sql);   
                $stmt->bindValue(':word',$word,PDO::PARAM_STR);
                $stmt->bindValue(':type',$type,PDO::PARAM_STR);        
                $stmt->bindValue(':numFrom', intval($numFrom), PDO::PARAM_INT);
                $stmt->bindValue(':numTo', intval($numTo), PDO::PARAM_INT);   
            }else{
                //Eloi: Vam treure el filtre de type que no se perque però no funcionava. Però sembla que era perque no hi havia realment res a BD. si tot funciona es pot treure aquest commentari
                //$sql = "SELECT * FROM InstagramPhotoViewed WHERE word!=:word AND pais=:pais AND type=:type AND downloaded=1 AND reportVerified < 2 ORDER BY downloadDate DESC LIMIT :numFrom, :numTo";
                 //les others no tornen per data, si no tornem sempre d'una sola paraula
                $sql = "SELECT * FROM InstagramPhotoViewed WHERE word!=:word AND pais=:pais AND type=:type AND downloaded=1 AND reportVerified < 2 ORDER BY numPrint DESC, numLikes DESC, numCount DESC LIMIT :numFrom, :numTo";

                 $stmt = $this->db->prepare($sql);   
                $stmt->bindValue(':word',$word,PDO::PARAM_STR);
                $stmt->bindValue(':type',$type,PDO::PARAM_STR);
                $stmt->bindValue(':pais',$pais,PDO::PARAM_STR);
                $stmt->bindValue(':numFrom', intval($numFrom), PDO::PARAM_INT);
                $stmt->bindValue(':numTo', intval($numTo), PDO::PARAM_INT);   
            }
        }

        
 
       
       
        $stmt->execute();

       
 
        
        $row = $stmt->fetchAll();      
//print_r($stmt->errorInfo()) ;
       
        if($stmt->errorCode() != 0) {
            
            print_r($stmt->errorInfo()) ;
            throw new Exception($stmt->errorCode());
        }
//        print "----------------------------------------";
//        print_r($row);exit;
        return $row;

    }

    
    public function getSavedInstaPhotosAll($word='', $type='', $pais){   
               //deixem parametre pais per si volguessim filtrar en un futur
                $sql = "SELECT * FROM InstagramPhotoViewed WHERE word=:word AND type=:type AND downloaded=1 AND reportVerified < 2 ORDER BY downloadDate DESC";               
                $stmt = $this->db->prepare($sql);        
                $stmt->bindValue(':word',$word,PDO::PARAM_STR);
                $stmt->bindValue(':type',$type,PDO::PARAM_STR);       
               
       
        $stmt->execute();  
        $rowCount = $stmt->rowCount();
        $row = $stmt->fetchAll();    
        if($stmt->errorCode() != 0) {            
            print_r($stmt->errorInfo()) ;
            throw new Exception($stmt->errorCode());
        }
        $arrayPhotos = array();
        $arrayPhotos['rowCount'] = $rowCount;
        $arrayPhotos['row'] = $row;

        return $arrayPhotos;

    }

    
    
    public function updateInstagramUserType($token, $value) {
        $sql = "UPDATE InstagramUsers SET type = :value WHERE token=:token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':value' => $value
        ));
    }

    public function updateAllPhotosServed($token, $value) {
        $sql = "UPDATE InstagramUsers SET all_photos_served = :value WHERE token=:token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':value' => $value
        ));
    }

    public function updateLastPhoto($token, $lastPhoto) {
        $sql = "UPDATE InstagramUsers SET last_photo = :lastPhoto WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':lastPhoto' => $lastPhoto
        ));
    }

    public function updateTotalPhotos($token, $totalPhotos) {
        //print $token.$totalPhotos;
        $sql = "UPDATE InstagramUsers SET total_photos = :totalPhotos WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':totalPhotos' => $totalPhotos
        ));
    }

    public function updateDownloadedPhotosAndLastPhoto($token, $lastDownloadedPhotos, $lastPhoto)
    {
        $sql = "UPDATE InstagramUsers SET last_photo = :lastPhoto, last_downloaded_photos = :lastDownloadedPhotos WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':lastDownloadedPhotos' => $lastDownloadedPhotos,
            ':lastPhoto' => $lastPhoto
        ));
    }
//suma a les que ja tenim
     public function updateDownloadedPhotos($token, $lastPhotosServed)
    {
        $sql = "UPDATE InstagramUsers SET last_downloaded_photos = last_downloaded_photos +:lastPhotosServed WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':lastPhotosServed' => $lastPhotosServed
        ));
    }
 //fa update del valor que volguem. L'utilitzem per reiniciar a 0   
    public function updateLastDownloadedPhotos($token, $lastPhotosServed)
    {
        $sql = "UPDATE InstagramUsers SET last_downloaded_photos = :lastPhotosServed WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':lastPhotosServed' => $lastPhotosServed
        ));
    }





}


