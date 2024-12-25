<?php
require_once G_PATH . 'sections/payxprint/functions/reportModel.php';

class reportManager{
    private $reportsModel;
    
    private $distributor;
    private $action;
    
    private $startDate;
    private $endDate;
    private $owner_id;
    private $owner_name;
    private $total_quantity;
    private $total_price;
    
    private $fileName;
    
    private $ownerArray;
    private $orderArray;
    
    public function __construct($distributor){        
        $this->distributor = $distributor;
        $this->createModel();
    }
    
    private function clearVars(){
        $this->action = null;

        $this->startDate = null;
        $this->endDate = null;
        $this->owner_id = null;
        $this->owner_name = null;
        $this->total_quantity = null;
        $this->total_price = null;

        $this->fileName = null;

        $this->ownerArray = null;
        $this->orderArray = null;
    }

    private function createModel(){
        $this->reportsModel = new reportModel($this->distributor);
    }
    
    public function indexAction(){
        $this->clearVars();        
        $this->checkAction();
        $this->checkPost();
        
        switch($this->action){
            case 'filterOwner':     
                echo $this->getOwnerList();
                break;
                
            case 'filterDate':
                break;

            case 'getOrders':
                $this->getOrders();
                echo $this->getOrdersTable();
                break;
            
            case 'createFile': 
                $this->getOrders();
                $result = $this->createFile();
                break;

            default:
                echo $this->defaultAction();
                break;
        }
    }
    
    private function checkAction(){
        $this->action = $_REQUEST['a'];
    }
  
    private function checkPost(){
        if(isset($_REQUEST['o'])) $this->owner_id   = $_REQUEST['o'];
        if(isset($_REQUEST['sd'])) $this->startDate = $_REQUEST['sd'];
        if(isset($_REQUEST['ed'])) $this->endDate   = $_REQUEST['ed'];
        if(isset($_REQUEST['fn'])) $this->fileName   = $_REQUEST['fn'];
    }

    private function defaultAction(){
        $html = <<<HTML
            <div class="filters">
                <form target="" action="sections/payxprint/profile/reports.php">
                    <div class = "left">
                        {$this->getDatePickerFilter()}               
                    </div>
                    <div class = "right">
                        {$this->getOwnerFilter()}
                    </div>
                    <div class="right_form">
                        <table>
                            <tr>
                                <td>
                                    <label for="checkExport">Export report?</label>
                                </td>
                                <td>
                                    <input id="checkExport" type="checkbox" val="1"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type='button' id="okDate" class='okB okButton' />
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
            </div>
            <div class="filterSeparator"></div>
            <div class="filteredContent"></div>
            {$this->setReportsHeader()}
HTML;

        return $html;                
    }
    
    private function getOwnerFilter(){
        $owner_options = $this->getOwnerList();
        
        $html = <<<HTML
            <div id="OwnerFilter">
                <span class="title_left"> <h1>Filter by Owner: </h1></span>
                <input id='ownerName' class="textInput" name='owner_name' type="text" value="" />
                <input id='ownerNameId' name="owner_name_id" type="hidden" value="" />
            </div>
HTML;
        
        return $html;
    }
    
    private function getDatePickerFilter(){
        $html = <<<HTML
            <div id="DatePickerFilter">
                <table>
                    <tr>
                        <td colspan="2">
                            <h1>Filter by Date: </h1>
                        </td>                
                    </tr>
                    <tr>
                        <td>
                            From:
                        </td>
                        <td>
                            <input id="startDate" class="textInput" type="text" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            To:
                        </td>
                        <td>
                            <input id="endDate" class="textInput" type="text" />
                        </td>
                    </tr>
                </table>
            </div>
HTML;
        
        return $html;        
    }
    
    private function setReportsHeader(){
        $html = <<<HTML
            <link href='sections/payxprint/resources/css/reports.css' rel='stylesheet' />
            <script src="sections/payxprint/resources/js/reports.js"></script>
HTML;
        
        return $html;
    }
        
    private function getOwnerList(){
        $this->ownerArray = $this->reportsModel->getOwner();
        
        return json_encode($this->ownerArray);
    }
    
    private function getOrders(){
        $this->orderArray = $this->reportsModel->getOrders($this->owner_id, $this->startDate, $this->endDate);
    }
    
    private function getOrdersTable(){
        $ordersTable = "<table class='tableStocks'>";
        $ordersTable .= $this->getOrdersTableHeader();
        
        if($this->orderArray != null){
            $ordersTable .= $this->getOrdersTableContent();
            $ordersTable .= $this->getOrdersTableTotals();
        }
        else{
            $ordersTable .= "<tr><td colspan='6'>No Orders</td></tr>";
        }
        
        $ordersTable .= "</table>";
//        $ordersTable .= "<div class='importCSV'><img class='downloadXLSImg' src='images/web/pxp_downloadXLS.png'></div>";
        $ordersTable .= "<iframe id='secretIFrame' src='' style='display:none; visibility:hidden;'></iframe>";

        return $ordersTable;
    }
    
    private function getReportTitle(){
        $ownerHeader = (($this->owner_id != "")? "of the Owner {$this->owner_id} ": "");
        $DateHeader  = (($this->startDate !="")? "from {$this->startDate} ": "");
        $DateHeader .= (($this->endDate != "")? "to {$this->endDate}": "");
        
        return "Reports {$ownerHeader}{$DateHeader}";
    }

    private function getOrdersTableHeader(){
        $html = <<<HTML
            <tr>
                <td colspan='6' class='black'>
                    <span class="reportsHeader">{$this->getReportTitle()}</span>
                </td>
            </tr>
            <tr>
                <td class='black' title='validatedDate'>Validated Date</td>
                <td class='black' title='orderNumber'>Order Number</td>
                <td class='black' title='idOwner'>Owner Id</td>
                <td class='black' title='ownerName'>Owner Name</td>
                <td class='black' title='quantity'>Prints</td>
                <td class='black' title='price'>Price</td>
            </tr>
HTML;
        
        return $html;
    }
    
    private function getOrdersTableContent(){
        foreach($this->orderArray as $order){
            $this->owner_name = $this->reportsModel->getOwnerName($order['idOwner']);
            
            $html .= <<<HTML
                <tr>
                    <td title='validatedDate'>{$order['validatedDate']}</td>
                    <td title='orderNumber'>{$order['idOrder']}</td>
                    <td title='idOwner'>{$order['idOwner']}</td>
                    <td title='ownerName'>{$this->owner_name}</td>
                    <td title='prints'>{$order['quantity']}</td>
                    <td title='price'>{$order['preu']}</td>
                </tr>
HTML;
            
            $this->total_price    += $order['preu'];
            $this->total_quantity += $order['quantity'];
        }
            
        return $html;
    }
    
    private function getOrdersTableTotals(){
        $html = "<tr>";
        $html .= "<td class='blank_td' colspan='3'></td>";
        $html .= "<td style='background-color:#669933; color: white;' title='total'>TOTAL</td>";
        $html .= "<td style='background-color:#669933; color: white;' title='total_prints'>{$this->total_quantity}</td>";
        $html .= "<td style='background-color:#669933; color: white;' title='total_price'>{$this->total_price}</td>";
        $html .= "</tr>";
            
        return $html;
    }
    
    private function getFileName(){
        $date = new DateTime();
        return $date->getTimestamp() . '-' . $this->distributor . '_report.xls';
    }
    
    private function createFile(){
        $status = true;
                        
        $this->fileName = $this->getFileName();
        
        @header("Last-Modified: " . @gmdate("D, d M Y H:i:s"));
        @header("Content-type: text/x-csv");
        
        // If the file is NOT requested via AJAX(never), force-download
        if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            header("Content-Disposition: attachment; filename={$this->fileName}");
        }
        
//        $tab = chr(9); //No ho detecta bé a windows!!!
        $tab = "\t";
        
        // Open the output stream
        $output = fopen("php://output", 'w');

        //Build file Header
        $now = utils::get_date_std('m/d/Y H:i');
        $DateHeader  = (($this->startDate !="")? "From {$this->startDate} ": "");
        $DateHeader .= (($this->endDate != "")? "to {$this->endDate}": ""); //"to {$now}");
        
        fputcsv($output, array("Title", "Order list"), $tab);
        fputcsv($output, array("Period", $DateHeader), $tab);
        if($this->owner_id != null) fputcsv($output, array("Owner", $this->reportsModel->getOwnerName($this->owner_id)), $tab);
        else fputcsv($output, array("Owner"), $tab);
        fputcsv($output, array("Date generation", $now), $tab);
        fputcsv($output, array(), $tab);
                
        //start writting the table content
        fputcsv($output, array("Validated Date", "Order Number", "Owner Id", "Owner Name", "Prints", "Price"), $tab);
        foreach($this->orderArray as $order){
            $owner_name = $this->reportsModel->getOwnerName($order['idOwner']);

            $status = fputcsv($output, array($order['validatedDate'], $order['idOrder'], $order['idOwner'], $owner_name, $order['quantity'], $order['preu']), $tab);

            if(!$status){
                utils::log("ERROR when generating {$this->getReportTitle()} of {$this->distributor}", "logCSV", "createFile");
                break;
            }
        }
        
        fclose($output);
    }
}