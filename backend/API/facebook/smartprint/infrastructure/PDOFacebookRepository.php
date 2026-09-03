<?php


namespace facebook\smartprint\infrastructure;

use facebook\smartprint\domain\FacebookUser;
use facebook\smartprint\domain\FacebookUserBuilder;
use facebook\smartprint\infrastructure\FBUserDTO;
use Exception;
use PDO;
use utils;

require_once $_SERVER['DOCUMENT_ROOT'] . "/API/facebook/smartprint/domain/FacebookUser.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/API/facebook/smartprint/domain/FacebookUserBuilder.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/API/facebook/smartprint/config/fb_config.php";

class PDOFacebookRepository {
    private $db;

    public function __construct() {
        $this->db = new PDO(
            'mysql:host='.DB_HOSTNAME.';port=3306x;dbname='.DB_DATABASE,
            DB_USER,
            DB_PASSWORD,
            array( PDO::ATTR_PERSISTENT => false)
        );
    }

    public function persist(FacebookUser $fbUser) {
        $sql = "INSERT INTO FacebookUsers (token,id_pb,num_photos,game_code) VALUES (:token, :idPB, :numPhotos, :gameCode)";
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
        $sql = "SELECT * FROM FacebookUsers WHERE token=:token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token
        ));
        $row = $stmt->fetch();
        if( $stmt->rowCount() ==0 ){
            throw new Exception("Token not found");
        }
        return (new FacebookUserBuilder())
            ->withIdBooth($row['id_pb'])
            ->withToken($row['token'])
            ->withAccessToken($row['fb_access_token'])
            ->withAllPhotosServed($row['all_photos_served'])
            ->withGameCode($row['game_code'])
            ->withLastDownloadedPhotos($row['last_downloaded_photos'])
            ->withLastPhoto($row['last_photo'])
            ->withNumPhotos($row['num_photos'])
            ->withTotalPhotos($row['total_photos'])
            ->build();
    }

    public function findByIdBooth($idBooth) {
        $sql = "SELECT * FROM FacebookUsers WHERE token=:pb_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':pb_id' => $idBooth
        ));
        $rows = $stmt->fetch();
        return (new FacebookUserBuilder())
            ->withIdBooth($rows[0]['pb_id'])
            ->withDongle($rows[0]['dongle'])
            ->withToken($rows[0]['token'])
            ->build();
    }

    public function findByTokenAndIdBooth($token, $idBooth){
        $fbUser = null;
        $sql = "SELECT * FROM FacebookUsers WHERE token=:token AND id_pb=:idBooth";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':idBooth' => $idBooth
        ));
        $row = $stmt->fetch();
        if( $stmt->rowCount() ==0 ){
            throw new Exception("Id or token not found");
        }
        return (new FacebookUserBuilder())
            ->withIdBooth($row['id_pb'])
            ->withToken($row['token'])
            ->withAccessToken($row['fb_access_token'])
            ->withAllPhotosServed($row['all_photos_served'])
            ->withGameCode($row['game_code'])
            ->withLastDownloadedPhotos($row['last_downloaded_photos'])
            ->withLastPhoto($row['last_photo'])
            ->withNumPhotos($row['num_photos'])
            ->withTotalPhotos($row['total_photos'])
            ->build();
    }

    public function updateAllPhotosServed($token, $value) {
        $sql = "UPDATE FacebookUsers SET all_photos_served = :value WHERE token=:token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':value' => $value
        ));
    }

    public function updateLastPhoto($token, $lastPhoto) {
        $sql = "UPDATE FacebookUsers SET last_photo = :lastPhoto WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':lastPhoto' => $lastPhoto
        ));
    }

    public function updateTotalPhotos($token, $totalPhotos) {
        $sql = "UPDATE FacebookUsers SET total_photos = :totalPhotos WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':totalPhotos' => $totalPhotos
        ));
    }

    public function updateDownloadedPhotosAndLastPhoto($token, $lastDownloadedPhotos, $lastPhoto)
    {
        $sql = "UPDATE FacebookUsers SET last_photo = :lastPhoto, last_downloaded_photos = :lastDownloadedPhotos WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':lastDownloadedPhotos' => $lastDownloadedPhotos,
            ':lastPhoto' => $lastPhoto
        ));
    }
    
    //per evaluacio proteccio de dades facebook deixem de guardar el nom 20211006
    public function updateAccessTokenAndName($token, $accessToken, $name){
        $sql = "UPDATE `FacebookUsers` SET fb_access_token = :accessToken WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':accessToken' => $accessToken
        ));
    }
    
//    public function updateAccessTokenAndName($token, $accessToken, $name){
//        $sql = "UPDATE `FacebookUsers` SET fb_access_token = :accessToken, fb_name = :name WHERE token = :token";
//        $stmt = $this->db->prepare($sql);
//        $stmt->execute(array(
//            ':token' => $token,
//            ':accessToken' => $accessToken,
//            ':name' => $name
//        ));
//    }

    public function deleteByTokenAndIdBooth($token, $idBooth){
        $sql = "DELETE FROM FacebookUsers WHERE token=:token AND id_pb=:idPB";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':token' => $token,
            ':idBooth' => $idBooth
        ));
        
        //Aquí eliminar els fitxers cada vegada que es crida end_session
    }

}


