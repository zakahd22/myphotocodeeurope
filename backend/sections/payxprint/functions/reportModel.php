<?php
require_once G_PATH . "includes/classes/APP_common.php";
require_once G_PATH . "includes/classes/APP_BdD_MySQL.php";

class reportModel {
    private $CLD_CON;
    private $distributor;
    
    public function __construct($distributor) {
        require_once G_PATH . 'common/conexio.php';
        $this->CLD_CON = $CLD_CON;
        $this->distributor = $distributor;
    }
    
    public function getOwner(){
        $result = false;
        $sql = "
            SELECT rentals.id AS id, rentals.name AS name
            FROM rentals
            WHERE rentals.CLD_DistributorId = {$this->distributor}
            OR id IN (
                SELECT booths.rental_id 
                FROM booths
                WHERE booths.CLD_Distributor = {$this->distributor}
            )
            ORDER BY rentals.name
        ";

        $this->CLD_CON->OpenRs($sql);

        if($this->CLD_CON->GetRsRows() > 0){
            $i = 0;
            $result = array();
            while ($this->CLD_CON->FetchArray()) {
                $result[$i]['value']  = $this->CLD_CON->GetArrayField("id");
                $result[$i]['label']  = $this->CLD_CON->GetArrayField("name");
                $i++;
            }
        }
        
        return $result;
    }
    
    public function getOrders($owner_id = null, $startDate = null, $endDate = null){
        $result = false;
        $and = "";
        if($owner_id != null) $and .= "AND Pay_print_order.idOwner = {$owner_id} ";
        
        if($startDate != null){
            $startDate = utils::datetime_to_date_std($startDate, 'm/d/Y', 'Y/m/d');
            $and .= "AND DATE(Pay_print_order.validatedDate) >= '{$startDate}' ";
        }
        
        if($endDate != null){
            $endDate = utils::datetime_to_date_std($endDate, 'm/d/Y', 'Y/m/d');
            $and .= "AND DATE(Pay_print_order.validatedDate) <= '{$endDate}' ";
        }
        
        $sql = "
            SELECT Pay_print_order.idOrder AS idOrder, Pay_print_order.idDongle AS idDongle, Pay_print_order.idOwner AS idOwner, Pay_print_order.quantitat AS quantitat, 
            Pay_print_order.preu AS preu, Pay_print_order.proposedDate AS proposedDate, Pay_print_order.validatedDate AS validatedDate
            FROM Pay_print_order
            WHERE Pay_print_order.validatedDate IS NOT NULL
            AND Pay_print_order.reportedDate IS NULL
            AND Pay_print_order.CLD_Distributor = {$this->distributor}
            {$and}
            ORDER BY(Pay_print_order.validatedDate)
        ";

        $this->CLD_CON->OpenRs($sql);

        if($this->CLD_CON->GetRsRows() > 0){
            $i = 0;
            $result = array();
            while ($this->CLD_CON->FetchArray()) {
                $result[$i]['idOrder']      = $this->CLD_CON->GetArrayField("idOrder");
                $result[$i]['idDongle']     = $this->CLD_CON->GetArrayField("idDongle");
                $result[$i]['idOwner']      = $this->CLD_CON->GetArrayField("idOwner");
                $result[$i]['quantity']     = $this->CLD_CON->GetArrayField("quantitat");
                $result[$i]['preu']         = $this->CLD_CON->GetArrayField("preu");
                $result[$i]['proposedDate'] = $this->CLD_CON->GetArrayField("proposedDate");
                $result[$i]['validatedDate']= utils::datetime_to_date_std($this->CLD_CON->GetArrayField("validatedDate"), 'Y-m-d H:i:s', 'm/d/Y');
                $i++;
            }
        }
        
        return $result;
    }
    
    public function getOwnerName($owner_id){
        $owner_name = false;
        $sql = "
            SELECT rentals.name as name
            FROM rentals
            WHERE rentals.id = {$owner_id}
        ";

        $this->CLD_CON->OpenRs($sql);

        if($this->CLD_CON->GetRsRows() > 0){
            if($this->CLD_CON->FetchArray()){
                $owner_name = $this->CLD_CON->GetArrayField("name");
            }
        }
        
        return $owner_name;
    }
}
