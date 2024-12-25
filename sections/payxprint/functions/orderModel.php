<?php
require_once G_PATH . "includes/classes/APP_common.php";
require_once G_PATH . "includes/classes/APP_BdD_MySQL.php";

class orderModel {
    private $CLD_CON;
    private $distributor;
    
    private $filter = "";
    
    public function __construct($distributor) {
        require_once G_PATH . 'common/conexio.php';
        $this->CLD_CON = $CLD_CON;
        $this->distributor = $distributor;
    }
    
    public function setFilterOwner($value){
        $this->filter .= "
            AND Pay_print_order.idOwner IN (
                SELECT id FROM rentals
                WHERE name LIKE '%{$value}%'
            )
        ";
    }

    public function setFilterString($value){
        $this->filter .= "
            AND Pay_print_order.idDongle IN (
                SELECT id FROM booths
                WHERE rand_string LIKE '%{$value}%'
            )
        ";
    }
    
    public function setFilterStatus($status){
        if($status){
            switch($status){
                case 1:
                    $this->filter .= "AND Pay_print_order.validatedDate IS NOT NULL";
                    break;

                case 2:
                    $this->filter .= "AND Pay_print_order.validatedDate IS NULL";                
                    break;
            }
        }
    }

    public function getOrders(){
        $sql = "SELECT Pay_print_order.idOrder as orderNum, Pay_print_order.idDongle as idDongle, booths.rand_string as rand_string, rentals.name as owner_name, Pay_print_order.quantitat as quantitat, Pay_print_order.validatedDate as validatedDate, Pay_print_order.preu as preu
                FROM Pay_print_order
                LEFT JOIN booths
                ON booths.id = Pay_print_order.idDongle
                LEFT JOIN rentals
                ON rentals.id = Pay_print_order.idOwner
                WHERE Pay_print_order.CLD_Distributor = {$this->distributor}
                {$this->filter}
        ";
                
        $this->CLD_CON->OpenRs($sql);

        if($this->CLD_CON->GetRsRows() > 0){
            $i = 0;
            $result = array();
            while ($this->CLD_CON->FetchArray()) {
                $result[$i]['orderNum']  = $this->CLD_CON->GetArrayField("orderNum");
//                $result[$i]['']  = $this->CLD_CON->GetArrayField("idDongle");
                $result[$i]['dongleString']  = $this->CLD_CON->GetArrayField("rand_string");
                $result[$i]['dongleOwner']  = $this->CLD_CON->GetArrayField("owner_name");
                $result[$i]['quantitat']  = $this->CLD_CON->GetArrayField("quantitat");
                $result[$i]['validated']  = $this->CLD_CON->GetArrayField("validatedDate");
                $result[$i]['preu']  = $this->CLD_CON->GetArrayField("preu");
                $i++;
            }
        }
        
        return $result;
    }
}
