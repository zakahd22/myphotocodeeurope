<?php


namespace instagram\smartprint\infrastructure;

use PDO;
use instagram\smartprint\domain\AppBoothDongle;

require_once ___FILE__. "/../domain/AppBoothDongle.php";

class PDOAppBoothDongleRepository
{
    private $db;

    public function __construct() {
        $this->db = new PDO(
            'mysql:host='.DB_HOSTNAME.';port=3306x;dbname='.DB_DATABASE,
            DB_USER,
            DB_PASSWORD,
            array( PDO::ATTR_PERSISTENT => false)
        );
    }

    public function findByIdBooth($idBooth) {
        $fbUser = null;
        $sql = "SELECT idBooth,idDongle FROM App_boothDongle WHERE idBooth = :idBooth";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':idBooth' => $idBooth
        ));
	$row = $stmt->fetch();
	if($stmt->rowCount() > 0){
		return new AppBoothDongle(
        	    $row['idBooth'],
	            $row['idDongle']
        	);
	}
	return new AppBoothDongle("", "");
    }
}
