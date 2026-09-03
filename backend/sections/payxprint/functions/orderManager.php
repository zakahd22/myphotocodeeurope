<?php
require_once G_PATH . 'sections/payxprint/functions/orderModel.php';

class orderManager {
    private $orderModel;
    private $action;
    
    private $title;
    private $value;
    private $status;
    
    private $orderArray;
    
    public function __construct($distributor){        
        $this->distributor = $distributor;
        $this->createModel();
    }
    
    private function createModel(){
        $this->orderModel = new orderModel($this->distributor);
    }
    
    public function indexAction(){
        $this->clearVars();        
        $this->checkPost();      
        
        switch($this->action){
            case 'filterOwner':     
                $this->orderModel->setFilterOwner($this->value);
                $this->orderModel->setFilterStatus($this->status);
                $this->orderArray = $this->orderModel->getOrders();
                if(!empty($this->orderArray)){
                    echo $this->getOrdersTable();
                }
                else{
                    if($this->value != null) $text = "No matches for <b>Owner '{$this->value}'</b> <br />";
                    if($this->status != 0) $text .= "No matches for <b>Status {$this->status}</b>";
                    echo $text;
                }
                break;

            case 'filterString':     
                $this->orderModel->setFilterString($this->value);
                $this->orderModel->setFilterStatus($this->status);
                $this->orderArray = $this->orderModel->getOrders();
                if(!empty($this->orderArray)){
                    echo $this->getOrdersTable();
                }
                else{
                    $text = "No matches for <b>String '{$this->value}'</b> <br />";
                    if($this->status != 0) $text .= "No matches for <b>Status {$this->status}</b>";
                    echo $text;
                }
                break;
            
            default:
                $this->orderArray = $this->orderModel->getOrders();
                echo $this->getOrdersTable();
                break;
        }
    }
    
    private function clearVars(){
        $this->action = null;

        $this->title = null;
        $this->value = null;
        $this->status = null;
        
        $this->orderArray = array();
        $this->filters = array();
    }

    
    private function checkPost(){
        if(isset($_REQUEST['a'])) $this->action = $_REQUEST['a'];
        if(isset($_REQUEST['title'])) $this->title   = $_REQUEST['title'];
        if(isset($_REQUEST['value'])) $this->value = $_REQUEST['value'];
        if(isset($_REQUEST['s'])) $this->status   = $_REQUEST['s'];
    }
    
    private function checkFilters(){
        $this->title = "";
        $this->value = "";
        $and = "";
        $andStatus = "";
        $i = 0;

        if($this->title != "" && $this->value != ""){
            switch($this->title){
                case 'Owner':
                    $and = "AND Pay_print_order.idOwner IN (
                                SELECT id FROM rentals
                                WHERE name LIKE '%{$this->value}%'
                            )";
                    break;

                case 'String':
                    $and = "AND Pay_print_order.idDongle IN (
                                SELECT id FROM booths
                                WHERE rand_string LIKE '%{$this->value}%'
                            )";
                    break;

                default:
                    break;
            }

            $this->filters[$i] = array(
                'title' => $this->title,
                'value' => $this->value,
                'and' => $and
            );
            $i++;
        }
        
        if($this->status){
            switch($this->status){
                case 1:
                    $andStatus = "AND Pay_print_order.validatedDate IS NOT NULL";
                    break;

                case 2:
                    $andStatus = "AND Pay_print_order.validatedDate IS NULL";                
                    break;
            }

            $this->filters[$i] = array(
                'title' => 'status',
                'value' => $this->status,
                'and' => $andStatus
            );
        }
    }
    
    private function setOrdersHeader(){
        $html = <<<HTML
            <link href='sections/payxprint/resources/css/orders.css' rel='stylesheet' />
            <script src="sections/payxprint/resources/js/orders.js"></script>
HTML;
        
        return $html;
    }
    
    private function getOrdersTable(){
        if($this->orderArray != null){
            $ordersTable = "<div class='inContent'>";
            $ordersTable .= $this->getOrdersTableContent();
            $ordersTable .= "</div>";
        }
        else{
            $ordersTable = "You do not have any order yet!";
        }
        
        return $ordersTable;
    }
    
    private function getOrdersTableContent(){
        foreach($this->orderArray as $order){
            $html .= <<<HTML
                <ul class='regDongleUL' onclick="edit(68, {$order['orderNum']})">
                    <li  style='width:10%' title='Order Number'> {$order['orderNum']}</li>
                    <li  style='width:10%;' title='String'>{$order['dongleString']}</li>
                    <li  style='width:15%' title='Owner Name'>Owner: {$order['dongleOwner']}</li>
                    <li  style='width:15%' title='Quantity'>Qty: {$order['quantitat']}</li>
                    <li  style='width:40%' title='Balance'> {$order['validated']}</li>
                    <li  style='width:10%' title='Price'> {$order['preu']}</li>
                </ul>
HTML;
        }
            
        return $html;
    }
}
