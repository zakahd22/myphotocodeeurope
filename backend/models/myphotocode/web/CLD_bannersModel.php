<?php
require_once G_PATH . "models/baseModel.php";

class CLD_bannersModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the Banners
     */
    public function get_CLD_banners(){
        return $this->select('CLD_banners');
    }
    
    /*
     * Get info Banners for Secction Event/Cloud
     */
    public function getEventCloudBanner($owner, $date2){
        /*
         * "SELECT b.banner , b.banner_url "
            . "FROM CLD_banners b "
            . "RIGHT JOIN CLD_timesBanners bt "
            . "ON bt.id_banner= b.id "
            . "WHERE b.rental_id=$owner "
            . "AND (
         *          ('$date2' BETWEEN start_date AND end_date AND end_date IS NOT NULL) "
            .       "OR 
         *          ('$date2' BETWEEN start_date AND '3000-01-01' AND end_date IS NULL)
         *         )"
         * 
         * 
         * 
         * "SELECT b.banner , b.banner_url "
            . "FROM CLD_banners b "
            . "RIGHT JOIN CLD_timesBanners bt "
            . "ON bt.id_banner= b.id "
            . "WHERE b.rental_id=$owner "
         *     AND end_date IS NOT NULL
            . "AND '$date2' BETWEEN start_date AND end_date "
            .  OR '$date2' BETWEEN start_date AND '3000-01-01'
         */
        $fields .= "";
        $this->entity->loadEntity('CLD_banners');
        $fields .= $this->entity->getEntityFields("CloudBanner");
        
        $sql = "
            SELECT {$fields} 
            FROM CLD_banners
            RIGHT JOIN CLD_timesBanners
            ON CLD_timesBanners.id_banner = CLD_banners.id
        ";
            
        $this->setFilter("CLD_banners.rental_id", "=", $owner);
        $this->setFilter("CLD_timesBanners.end_date", "IS NOT", "NULL", "AND");
        $this->setBetweenFilter($date2, "CLD_timesBanners.start_date", "AND", "CLD_timesBanners.end_date", "AND", true);
        $this->setBetweenFilter($date2, "CLD_timesBanners.start_date", "AND", "3000-01-01", "OR", true);
        
        
        $query = $this->my_query($sql);
        
        $result = $this->requestQueryResults($query, 'CloudBanner');
        
        return $result;
    }
    
}


