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

require_once $_SERVER['DOCUMENT_ROOT'] . "/API/instagram/smartprint/domain/InstagramUser.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/API/instagram/smartprint/domain/InstagramUserBuilder.php";

//TODO: hauria d'estar amb LOCAL_PATH pero no podem perque esta definti al propi ig_config.php
//no funciona-->require_once $_SERVER['DOCUMENT_ROOT'] . LOCAL_PATH . '/API/instagram/smartprint/config/ig_config.php';
//require_once $_SERVER['DOCUMENT_ROOT'] . "/myphotocode/API/instagram/smartprint/config/ig_config.php"; //maquina local
require_once $_SERVER['DOCUMENT_ROOT'] . "/API/instagram/smartprint/config/ig_config.php";

try {
    echo "hola hola";
    $igRepository = new PDOInstagramRepository();    
    
    echo "hola hola";
       $result = getSavedInstaPhotos("aerosmith", "username", "", "4", "20", 0);
echo "hola hola";





    
    
   
    print_r($result) ;
} catch( Exception $e){    
    echo "KO#{$e->getMessage()}";
}

function getSavedInstaPhotos($word='',$type='', $pais, $numFrom, $numTo, $other){
    //TODO: podem fer una consulta que si li pasem $tag, retorna totes les del tag, si li passem $type filtra per type i si li passem un num limita les que retorna.
    global $igRepository;

    return $igRepository->getSavedInstaPhotos($word, $type, $pais, $numFrom, $numTo, $other);
}


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
    
  

      public function getSavedInstaPhotos($word='', $type='', $pais='', $numFrom=0, $numTo=0, $other=0){
       

        if(!$other){
            if($pais==''){
                //Eloi: Vam treure el filtre de type que no se perque però no funcionava. Però sembla que era perque no hi havia realment res a BD. si tot funciona es pot treure aquest commentari
              $sql = "SELECT * FROM InstagramPhotoViewed WHERE  word=:word AND type=:type AND downloaded=1  LIMIT :numFrom, :numTo";
//                $sql = "SELECT * FROM InstagramPhotoViewed WHERE word=:word  AND downloaded=1 LIMIT :numFrom, :numTo";
                $stmt = $this->db->prepare($sql);       
               

                $stmt->bindValue(':word',$word,PDO::PARAM_STR);
               $stmt->bindValue(':type',$type,PDO::PARAM_STR);      
                $stmt->bindValue(':numFrom', intval($numFrom+1), PDO::PARAM_INT);
                $stmt->bindValue(':numTo', intval($numTo), PDO::PARAM_INT);           
            }else{
                //Eloi DEBUG: no retona res. treiem el filtre de type que no se perque però no funciona. deixa-ho com estava en acabat
                // $sql = "SELECT * FROM InstagramPhotoViewed WHERE word=:word AND type=:type AND pais=:pais  AND downloaded=1 LIMIT :numFrom, :numTo";
                
                $sql = "SELECT * FROM InstagramPhotoViewed WHERE word=:word  AND pais=:pais  AND downloaded=1 LIMIT :numFrom, :numTo";
                 $stmt = $this->db->prepare($sql);   
                $stmt->bindValue(':word',$word,PDO::PARAM_STR);
                $stmt->bindValue(':pais',$pais,PDO::PARAM_STR);        
                $stmt->bindValue(':numFrom', intval($numFrom+1), PDO::PARAM_INT);
                $stmt->bindValue(':numTo', intval($numTo), PDO::PARAM_INT);   
            }

        }else{
            if($pais==''){
                //Eloi DEBUG: no retona res. treiem el filtre de type que no se perque però no funciona. deixa-ho com estava en acabat
                //$sql = "SELECT * FROM InstagramPhotoViewed WHERE word!=:word AND type=:type AND downloaded=1 ORDER BY numPrint, numLikes, numCount LIMIT :numFrom, :numTo";
                $sql = "SELECT * FROM InstagramPhotoViewed WHERE word!=:word  AND downloaded=1 ORDER BY numPrint, numLikes, numCount LIMIT :numFrom, :numTo";
                 $stmt = $this->db->prepare($sql);   
                $stmt->bindValue(':word',$word,PDO::PARAM_STR);
                       
                $stmt->bindValue(':numFrom', intval($numFrom+1), PDO::PARAM_INT);
                $stmt->bindValue(':numTo', intval($numTo), PDO::PARAM_INT);   
            }else{
                //Eloi DEBUG: no retona res. treiem el filtre de type que no se perque però no funciona. deixa-ho com estava en acabat
                //$sql = "SELECT * FROM InstagramPhotoViewed WHERE word!=:word AND pais=:pais AND type=:type AND downloaded=1 ORDER BY numPrint, numLikes, numCount LIMIT :numFrom, :numTo";
                $sql = "SELECT * FROM InstagramPhotoViewed WHERE word!=:word AND pais=:pais AND downloaded=1 ORDER BY numPrint, numLikes, numCount LIMIT :numFrom, :numTo";
                 $stmt = $this->db->prepare($sql);   
                $stmt->bindValue(':word',$word,PDO::PARAM_STR);
                
                $stmt->bindValue(':numFrom', intval($numFrom+1), PDO::PARAM_INT);
                $stmt->bindValue(':numTo', intval($numTo), PDO::PARAM_INT);   
            }
        }

        
 //Eloi DEBUG: potser li has de donar format amb un str() o alguna cosa aixi... ens veiem demà ;)
                // $stmt->bindValue(':type',$type,PDO::PARAM_STR);
                //   $stmt->bindValue(':type',$type,PDO::PARAM_STR);
                //$stmt->bindValue(':type','username',PDO::PARAM_STR);
       
       
        $stmt->execute();

        //Eloi DEBUG: esborra l'execute de sota
        // $stmt->execute(array(
        //     ':word' => $word,
        //     ':pais' => $pais,
        //     ':numFrom' => $numFrom,
        //     ':numTo' => $numTo

        // ));
        // ,
        //     ':pais' => $pais,
        //     ':type' => $type
 
        
        $row = $stmt->fetchAll();      
//print_r($stmt->errorInfo()) ;
       
        if($stmt->errorCode() != 0) {
            
            print_r($stmt->errorInfo()) ;
            throw new Exception($stmt->errorCode());
        }
        return $row;

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

     public function updateDownloadedPhotos($token, $lastPhotosServed)
    {
        $sql = "UPDATE InstagramUsers SET last_downloaded_photos = last_downloaded_photos +:lastPhotosServed WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':lastPhotosServed' => $lastPhotosServed
        ));
    }





}


