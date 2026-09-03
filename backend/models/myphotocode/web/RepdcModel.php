<?php
require_once G_PATH . "models/baseModel.php";

/**
 * Abstract model, used in reports DC (weekly, monthly and year)
 */
class RepdcModel extends baseModel{
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the boothDongle from the ?
     */
    public function getSinglePBInfo($idPB, $owner){
        $result = array();
        $this->clear_sql_operators();
        
        $sql="
           SELECT b.`idBooth` AS idBooth, b.`name` AS both_name, b.`location` AS location, b.`version` AS version, r.`name` AS owner_name, r.`APP_email` AS alert_email, t.`name` AS booth_type, b.`serialnumber` AS serialnumber
            FROM App_booths b
            INNER JOIN rentals r
                ON b.`owner` = r.`id`
            INNER JOIN `CLD_boothTypes` t
                ON b.`type` = t.`char`
            WHERE b.`idBooth` = {$idPB}
            AND b.`owner` = {$owner}
        ";

        $query = $this->my_query($sql);
        
        $pb = $this->my_fetch_array($query);
//            $result['idBooth'];
//            $result['both_name'];
//            $result['location'];
//            $result['version'];
//            $result['owner_name'];
//            $result['alert_email'];
//            $result['booth_type'];
//            $result['serialnumber'];          

        if($pb){
            $pb['rand_string'] = NULL;
            $result = $pb;

            $sql="
                SELECT  booths.`rand_string` AS rand_string
                FROM `booths` 
                INNER JOIN App_boothDongle 
                ON booths.id = App_boothDongle.idDongle 
                WHERE App_boothDongle.idBooth = {$idPB}
                ORDER BY `datetimeS` DESC 
                LIMIT 0,1;
            ";

            $query = $this->my_query($sql);

            $dongle = $this->my_fetch_array($query);
            //Necessari per a que no peti si no hi ha dongle
            if($dongle){
                $result['rand_string'] = $dongle['rand_string'];
            }

        }
        
        return $result;
    }
    
    public function checkForCurrencies($idBooth, $startDate, $endDate){
        $array_currencies = array();
        $array_currenciesName = array();
        $array_currenciesPosition = array();
        $array_currenciesSymbol = array();
         
         $sql="
            SELECT DISTINCT App_info.currency AS currency, App_currencies.name AS name, App_currencies.position AS position, App_currencies.symbol AS symbol
            FROM App_info 
            INNER JOIN App_currencies 
            ON App_info.currency = App_currencies.code
            WHERE `typeInfo`=10 
            AND money IS NOT NULL 
            AND (
                `when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND `when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            AND idBooth = {$idBooth}
            ORDER BY App_info.currency;
        ";
        
        $query = $this->my_query($sql);
        
        $nCurrencies = 0;
        while($info = $this->my_fetch_array($query)){
            $array_currencies[$nCurrencies]         = $info['currency'];
            $array_currenciesName[$nCurrencies]     = utf8_encode($info['name']);
            $array_currenciesPosition[$nCurrencies] = $info['position'];
            $array_currenciesSymbol[$nCurrencies]   = $info['symbol'];
            $nCurrencies++;
        }

        return array($nCurrencies, $array_currencies, $array_currenciesName, $array_currenciesPosition, $array_currenciesSymbol);
    }
    
    public function checkForFreePlays($idBooth, $startDate, $endDate){
        $hasFreeplays = FALSE;
         
         $sql="
            SELECT DISTINCT App_info.currency, App_currencies.name, App_currencies.position, App_currencies.symbol FROM App_info 
            INNER JOIN App_currencies
            ON App_info.currency = App_currencies.code
            WHERE `typeInfo`=10 
            AND money IS NULL 
            AND (
                `when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND `when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            AND idBooth={$idBooth}
        ";
        
        $query = $this->my_query($sql);
        
        if($freePlays = $this->my_fetch_array($query)){
            $hasFreeplays = TRUE;
        }

        return $hasFreeplays;
    }
    
    public function getFirstApp_info($idPb){
        $FirstDate = FALSE;
        
        $this->setFilter('idBooth', '=', $idPb);
        $this->setOrder('when', 'ASC');
        $this->setLimit(1);
        
        $result = $this->select('App_info');

        if(!empty($result)){
            $FirstDate = DateTime::createFromFormat('Y-m-d H:i:s', $result[0]['when']);
        }
        
        return $FirstDate;
    }
    public function getFirstOwnerPbsLastConnection($idOwner){
        $OlderDate = FALSE;
        
        $sql = "
            SELECT App_info.`when`
            FROM `App_info`
            INNER JOIN `App_booths` ON App_info.`idBooth` = App_booths.`idBooth`
            WHERE App_booths.`owner` = {$idOwner}
            ORDER BY App_info.`when` ASC
            LIMIT 1
        ";
        
        $query = $this->my_query($sql);
        
        if ($result = $this->my_fetch_array($query)) {
            $OlderDate = DateTime::createFromFormat('Y-m-d H:i:s', $result['when']);
        }
    
        return $OlderDate;
    }
    
    public function getPbLastConnection($idBooth){
        $OlderDate = FALSE;
        
        $this->setFilter('idBooth', '=', $idBooth);
        $this->setOrder('when', 'DESC');
        $this->setLimit(1);

        
        $result = $this->select('App_info');

        if(!empty($result)){
            $OlderDate = DateTime::createFromFormat('Y-m-d H:i:s', $result[0]['when']);
        }        
        
        return $OlderDate;
    }
    public function getPbLastConn($idBooth){
        $OlderDate = FALSE;
        
        $this->setFilter('idBooth', '=', $idBooth);      
       
        $result = $this->select('App_booths');
        
        if(!empty($result)){
            $OlderDate = DateTime::createFromFormat('Y-m-d H:i:s', $result[0]['lastConn']);
        }        
        
        return $OlderDate;
    }
    
    public function getPbLastConnectionLocal($idBooth){
        $OlderDate = FALSE;
        
        $this->setFilter('idBooth', '=', $idBooth);      
       
        $result = $this->select('App_booths');
        
        if(!empty($result)){
            $OlderDate = DateTime::createFromFormat('Y-m-d H:i:s', $result[0]['lastConnLocal']);
        }        
        
        return $OlderDate;
    }
    
    public function getPbLastConnection_appBooths($idBooth){
        $OlderDate = FALSE;
        
        $this->setFilter('idBooth', '=', $idBooth);

        
        $result = $this->select('App_booths');

        if(!empty($result)){
            $OlderDate = $result[0]['lastConn'];
        }        
        
        return $OlderDate;
    }
    
    public function getPbLastConnectionZone_appBooths($idBooth){
        $OlderDate = FALSE;
        
        $this->setFilter('idBooth', '=', $idBooth);

        
        $result = $this->select('App_booths');

        if(!empty($result)){
            $OlderDate = $result[0]['lastConnZone'];
        }        
        
        return $OlderDate;
    }
    
    public function getPbLastConnectionLocal_appBooths($idBooth){
        $OlderDate = FALSE;
        
        $this->setFilter('idBooth', '=', $idBooth);

        
        $result = $this->select('App_booths');

        if(!empty($result)){
            $OlderDate = $result[0]['lastConnLocal'];
        }        
        
        return $OlderDate;
    }
    
    public function getTotalOwnerPbsLastConnection($idOwner){
        $OlderDate = FALSE;
        
        $sql = "
            SELECT App_info.`when`
            FROM `App_info`
            INNER JOIN `App_booths` ON App_info.`idBooth` = App_booths.`idBooth`
            WHERE App_booths.`owner` = {$idOwner}
            ORDER BY App_info.`when` DESC
            LIMIT 1
        ";
        
        $query = $this->my_query($sql);
        if ($result = $this->my_fetch_array($query)) {
            $OlderDate = DateTime::createFromFormat('Y-m-d H:i:s', $result['when']);
        }
    
        return $OlderDate;
    }
    
    
    public function getYearSummaryReportByPb($idPb, $owner, $startDate, $endDate){
        $result = FALSE;
        $filterByidPB = '';
        
        if($idPb != FALSE){
            $filterByidPB = "AND App_info.`idBooth` = {$idPb}";
        }
        
        $sql = "
            SELECT 
                MONTH(App_info.`when`) AS month,
                YEAR(App_info.`when`) AS year, 
                MAX(App_info.`when`) AS when_datetime, 
                COUNT(App_info.`idInfo`) AS plays, 
                DATE(App_info.`when`) AS when_, 
                COALESCE(SUM(CASE WHEN `money` IS NULL AND `i1` != 1031 AND `i1` != 1013 THEN 1 ELSE 0 END),0) AS `freePlays`,
                App_info.`idBooth`, 
                App_info.`idDongle`, 
                App_info.`typeInfo`, 
                COALESCE(SUM(App_info.`money`), 0) AS money_, 
                COALESCE(SUM(App_info.`money2`), 0) AS extraMoney,
                App_info.`currency`, 
                IF(
                    App_info.`stock` > 0,
                    App_info.`stock`,
                    0
                  ) AS stock,
                
                COALESCE(SUM(CASE WHEN `i1` = 1031 THEN 1 ELSE 0 END),0) AS pas,
                COALESCE(SUM(CASE WHEN `i1` = 1013 THEN 1 ELSE 0 END),0) AS SmartPrint,
                App_info.`i1`, 
                App_info.`i2`,
                COALESCE(SUM(App_info.`i3`),0) AS i3, 
                COALESCE(SUM(App_info.`i4`),0) AS i4, 
                App_info.`i5`, 
                App_info.`str1`,
                App_info.`str2`, 
                MIN(App_info.`PBnew`) AS PBnew,
                App_info.`in1`, 
                App_info.`in2`, 
                App_info.`in3`, 
                (COALESCE(SUM(App_info.`in4`), 0) + COALESCE(SUM(App_info.`in8`),0)) AS prints,
                COALESCE(SUM(App_info.`in4`), 0) AS in4,
                App_info.`pbs_time`, 
                App_info.`db_time`,
                App_currencies.`position` AS currency_position,
                App_currencies.`symbol` AS currency_symbol
                
            FROM (SELECT * FROM `App_info` ORDER BY App_info.`when` DESC) App_info
            LEFT JOIN App_currencies
            ON App_currencies.`code` = App_info.`currency`
            WHERE App_info.`typeInfo` = 10 AND i3!=65535
            AND (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            {$filterByidPB}
            AND App_info.`idBooth` IN (
                SELECT App_booths.`idBooth` 
                FROM `App_booths` 
                WHERE App_booths.`owner` = {$owner}
            )
            GROUP BY `year`, `month`
            ORDER BY App_info.`when`
        ";             
        
        $query = $this->my_query($sql);
        
        if($query){
            $i = 0;
            $result = array();
            while($info = $this->my_fetch_array($query)){
                
                $date = DateTime::createFromFormat('Y-m-d', $info['when_']);
                $date = $date->format('Y-m');
                        
                $result[$date]['month']             = $info['month'];
                $result[$date]['year']              = $info['year'];
                $result[$date]['plays']             = $info['plays'];
                $result[$date]['freePlays']         = $info['freePlays'];
                $result[$date]['prints']            = $info['prints'];
                $result[$date]['when_']             = $date;
                $result[$date]['when_datetime']     = DateTime::createFromFormat('Y-m-d H:i:s', $info['when_datetime']);
                $result[$date]['idBooth']           = $info['idBooth'];
                $result[$date]['idDongle']          = $info['idDongle'];
                $result[$date]['typeInfo']          = $info['typeInfo'];
                $result[$date]['typeInfo']          = $info['typeInfo'];
                $result[$date]['money_']            = $info['money_'];
                $result[$date]['currency']          = $info['currency'];
                $result[$date]['stock']             = $info['stock'];
                $result[$date]['pas']               = $info['pas'];
                $result[$date]['SmartPrint']        = $info['SmartPrint'];
                $result[$date]['i1']                = $info['i1'];
                $result[$date]['i2']                = $info['i2'];
                $result[$date]['i3']                = $info['i3'];
                $result[$date]['i4']                = $info['i4'];
                $result[$date]['i5']                = $info['i5'];
                $result[$date]['str1']              = $info['str1'];
                $result[$date]['str2']              = $info['str2'];
                $result[$date]['PBnew']             = $info['PBnew'];
                $result[$date]['in1']               = $info['in1'];
                $result[$date]['in2']               = $info['in2'];
                $result[$date]['in3']               = $info['in3'];
                $result[$date]['in4']               = $info['in4'];
                $result[$date]['in5']               = $info['in5'];
                $result[$date]['in6']               = $info['in6'];
                $result[$date]['in7']               = $info['in7'];
                $result[$date]['in8']               = $info['in8'];
                $result[$date]['pbs_time']          = $info['pbs_time'];
                $result[$date]['db_time']           = $info['db_time'];
                $result[$date]['currency_position'] = $info['currency_position'];
                $result[$date]['currency_symbol']   = $info['currency_symbol'];
                $result[$date]['collection']        = FALSE;
                
                $i++;
            }
        }
        //20220113 En versió v3.1.5 hi ha un bug que envia overflow al no convertir bé float a integer
        //excloem els valors 65535 que és el maxim valor de un unsigned smallint --> AND i3!=65535
        $sql = "
            SELECT MONTH(App_info.`when`) AS month, YEAR(App_info.`when`) AS year, DATE(App_info.`when`) AS when_,  SUM(i3) AS i3_60
            FROM App_info
            WHERE App_info.`typeInfo` = 60 AND i3!=65535
            AND (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            $filterByidPB
            AND App_info.`idBooth` IN (
                SELECT App_booths.`idBooth` 
                FROM `App_booths` 
                WHERE App_booths.`owner` = {$owner}
            )
            GROUP BY `year`, `month`
            ORDER BY App_info.`when`
        ";
        
        $query0 = $this->my_query($sql);

        if($query0){
            while($info = $this->my_fetch_array($query0)){
                $date = DateTime::createFromFormat('Y-m-d', $info['when_']);
                $date = $date->format('Y-m');
                
                $result[$date]['i3_60'] = $info['i3_60'];
            }
        }
        
         //Afegim i4_60 que enviem a partir de la versió 3.1.7 de Britta (Newport de moment en parche de 3.1.6)
         $sql = "
            SELECT MONTH(App_info.`when`) AS month, YEAR(App_info.`when`) AS year, DATE(App_info.`when`) AS when_,  SUM(i4) AS i4_60
            FROM App_info
            WHERE App_info.`typeInfo` = 60 AND i4!=65535
            AND (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            $filterByidPB
            AND App_info.`idBooth` IN (
                SELECT App_booths.`idBooth` 
                FROM `App_booths` 
                WHERE App_booths.`owner` = {$owner}
            )
            GROUP BY `year`, `month`
            ORDER BY App_info.`when`
        ";
        
        $query0 = $this->my_query($sql);

        if($query0){
            while($info = $this->my_fetch_array($query0)){
                $date = DateTime::createFromFormat('Y-m-d', $info['when_']);
                $date = $date->format('Y-m');
                
                $result[$date]['i4_60'] = $info['i4_60'];
            }
        }
        
        $sql = "
            SELECT `when` AS collection_time, DATE(App_info.`when`) AS when_, i1
            FROM App_info
            WHERE App_info.`typeInfo` = 20
            AND (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            $filterByidPB
            AND App_info.`idBooth` IN (
                SELECT App_booths.`idBooth` 
                FROM `App_booths` 
                WHERE App_booths.`owner` = {$owner}
            )
            ORDER BY App_info.`when`
        ";
                
        $query1 = $this->my_query($sql);
        if($query1){
            while($info = $this->my_fetch_array($query1)){
                $date = DateTime::createFromFormat('Y-m-d', $info['when_']);
                $date = $date->format('Y-m');
                
                if(!is_array($result[$date]['collectionNum'])) {
                    $result[$date]['collectionNum'] = array();
                }
                if($result === FALSE || !array_key_exists('stock', $result[$date])){
                    //En el cas que no hi hagi partides pero si collection, volem mostrar el stock, ja que es conegut
                    $result[$date]['stock'] = $info['stock'];
                }
                
                $result[$date]['collection'] = TRUE;
                $result[$date]['collection_time'] = DateTime::createFromFormat('Y-m-d H:i:s', $info['collection_time']);
                array_push($result[$date]['collectionNum'], $info['i1']);
            }
        }
        
        
        /*****
         * 
         * Stock final del dia, no inicial
         */
        $sqlStock = "
            SELECT DATE(App_info.`when`) AS when_, stock
            FROM App_info
            WHERE App_info.`typeInfo` = 10
            AND (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            $filterByidPB
            AND App_info.`idBooth` IN (
                SELECT App_booths.`idBooth` 
                FROM `App_booths` 
                WHERE App_booths.`owner` = {$owner}
            )
            
            ORDER BY App_info.`when` ASC
        ";
                
        $queryStock = $this->my_query($sqlStock);
        if($queryStock){
            while($info = $this->my_fetch_array($queryStock)){
                $date = DateTime::createFromFormat('Y-m-d', $info['when_']);
                $date = $date->format('Y-m');   
                $result[$date]['stock'] = $info['stock'];                
                
            }
        }
        
        
        /*
         *Cost Twilio
         * */ 
        if($idPb){
            $AndPBOwner = "AND idBooth = {$idPb}";
        }else{
            $AndPBOwner = "AND owner = {$owner}";
        }
        $sql2 = "
         
            SELECT cost, method , DATE(gestor.`last`) AS when_
            FROM gestor
            WHERE gestor.`last` >= '{$startDate->format('Y-m-d H:i:s')}' AND gestor.`last` <= '{$endDate->format('Y-m-d H:i:s')}'
            AND `state`=6 
            AND (`method` = 1 OR `method` = 3)
            AND `cost`!=0
            ".$AndPBOwner;
        
        $query2 = $this->my_query($sql2);
        $rows2 = 0;
        if($query2){
            while($info = $this->my_fetch_array($query2)){
                $date = DateTime::createFromFormat('Y-m-d', $info['when_']);
                $date = $date->format('Y-m');
                
                if(!isset($result[$date]['costTwilio'])){
                    $result[$date]['costTwilio'] = $info['cost'];
                    if($info['method']==3){
                        $result[$date]['costWhats'] = $info['cost'];
                        $result[$date]['qtyWhats'] = 1;
                    }
                    if($info['method']==1){
                        $result[$date]['costSMS'] = $info['cost'];
                        $result[$date]['qtySMS'] = 1;
                    }
                }else{
                    $result[$date]['costTwilio'] += $info['cost'];
                    if($info['method']==3){
                        $result[$date]['costWhats'] += $info['cost'];
                        $result[$date]['qtyWhats'] += 1;
                    }
                    if($info['method']==1){
                        $result[$date]['costSMS'] += $info['cost'];
                        $result[$date]['qtySMS'] += 1;
                    }
                }
//                if($anteriorWhen != $date){
//                    array_push($result[$date]['costTwilio'], $info['cost']);
//                }
                
                $anteriorWhen = $date;
                $rows2++;
            }
            //afegim la última fila si hi ha rows
//            if($rows2){
//               array_push($result[$date]['costTwilio'], $info['cost']); 
//            }
            
        }

        return $result;
    }
       
    public function getSummaryReportByPb($idPb, $owner, $startDate, $endDate){
        $result = FALSE;
        $filterByidPB = '';
        
        if($idPb != FALSE){
            $filterByidPB = "AND App_info.`idBooth` = {$idPb}";
        }

        $sql = "
            SELECT 
                COUNT(App_info.`idInfo`) AS plays, 
                DATE(App_info.`when`) AS when_, 
                MAX(App_info.`when`) AS when_datetime, 
                COALESCE(SUM(CASE WHEN `money` IS NULL AND `i1` != 1031 AND `i1` != 1013 THEN 1 ELSE 0 END),0) AS `freePlays`,
                App_info.`idBooth`, 
                App_info.`idDongle`, 
                App_info.`typeInfo`, 
                COALESCE(SUM(App_info.`money`), 0) AS money_, 
                COALESCE(SUM(App_info.`money2`), 0) AS extraMoney,
                App_info.`currency`, 
                IF(
                    App_info.`stock` > 0,
                    App_info.`stock`,
                    0
                  ) AS stock,
                COALESCE(SUM(CASE WHEN `i1` = 1031 THEN 1 ELSE 0 END),0) AS pas,
                COALESCE(SUM(CASE WHEN `i1` = 1013 THEN 1 ELSE 0 END),0) AS SmartPrint,
                App_info.`i1`, 
                App_info.`i2`,
                COALESCE(SUM(App_info.`i3`),0) AS i3, 
                COALESCE(SUM(App_info.`i4`),0) AS i4, 
                App_info.`i5`, 
                App_info.`str1`,
                App_info.`str2`, 
                MIN(App_info.`PBnew`) AS PBnew, 
                App_info.`in1`, 
                App_info.`in2`, 
                App_info.`in3`, 
                (COALESCE(SUM(App_info.`in4`), 0) + COALESCE(SUM(App_info.`in8`),0)) AS prints,
                COALESCE(SUM(App_info.`in4`), 0) AS in4,
                App_info.`pbs_time`, 
                App_info.`db_time`,
                App_currencies.`position` AS currency_position,
                App_currencies.`symbol` AS currency_symbol
                
            FROM (SELECT * FROM `App_info` ORDER BY App_info.`when` DESC) App_info
            LEFT JOIN App_currencies
            ON App_currencies.`code` = App_info.`currency`
            WHERE App_info.`typeInfo` = 10 AND i3!=65535
            AND (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            {$filterByidPB}
            AND App_info.`idBooth` IN (
                SELECT App_booths.`idBooth` 
                FROM `App_booths` 
                WHERE App_booths.`owner` = {$owner}
            )
            GROUP BY DATE(App_info.`when`)
            ORDER BY App_info.`when`
        "; 
                
        utils::log($sql, "logAlex");                
        $query = $this->my_query($sql);

        if($query){
            $result = array();
            while($info = $this->my_fetch_array($query)){
                $result[$info['when_']]['plays'] = $info['plays'];
                $result[$info['when_']]['freePlays'] = $info['freePlays'];
                $result[$info['when_']]['prints'] = $info['prints'];
                $result[$info['when_']]['when_'] = $info['when_'];
                $result[$info['when_']]['when_datetime'] = DateTime::createFromFormat('Y-m-d H:i:s', $info['when_datetime']);
                $result[$info['when_']]['idBooth'] = $info['idBooth'];
                $result[$info['when_']]['idDongle'] = $info['idDongle'];
                $result[$info['when_']]['typeInfo'] = $info['typeInfo'];
                $result[$info['when_']]['typeInfo'] = $info['typeInfo'];
                $result[$info['when_']]['money_'] = $info['money_'];
                $result[$info['when_']]['currency'] = $info['currency'];
                $result[$info['when_']]['stock'] = $info['stock'];
                $result[$info['when_']]['pas'] = $info['pas'];
                $result[$info['when_']]['SmartPrint'] = $info['SmartPrint'];
                $result[$info['when_']]['i1'] = $info['i1'];
                $result[$info['when_']]['i2'] = $info['i2'];
                $result[$info['when_']]['i3'] = $info['i3'];
                $result[$info['when_']]['i4'] = $info['i4'];
                $result[$info['when_']]['i5'] = $info['i5'];
                $result[$info['when_']]['str1'] = $info['str1'];
                $result[$info['when_']]['str2'] = $info['str2'];
                $result[$info['when_']]['PBnew'] = $info['PBnew'];
                $result[$info['when_']]['in1'] = $info['in1'];
                $result[$info['when_']]['in2'] = $info['in2'];
                $result[$info['when_']]['in3'] = $info['in3'];
                $result[$info['when_']]['in4'] = $info['in4'];
                $result[$info['when_']]['in5'] = $info['in5'];
                $result[$info['when_']]['in6'] = $info['in6'];
                $result[$info['when_']]['in7'] = $info['in7'];
                $result[$info['when_']]['in8'] = $info['in8'];
                $result[$info['when_']]['pbs_time'] = $info['pbs_time'];
                $result[$info['when_']]['db_time'] = $info['db_time'];
                $result[$info['when_']]['currency_position'] = $info['currency_position'];
                $result[$info['when_']]['currency_symbol'] = $info['currency_symbol'];
                $result[$info['when_']]['collection'] = FALSE;
            }
        }
        //20220113 En versió v3.1.5 hi ha un bug que envia overflow al no convertir bé float a integer
        //excloem els valors 65535 que és el maxim valor de un unsigned smallint --> AND i3!=65535
        $sql = "
            SELECT DATE(App_info.`when`) AS when_, SUM(i3) AS i3_60 
            FROM App_info
            WHERE App_info.`typeInfo` = 60 AND i3!=65535
            AND (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
                {$filterByidPB}
            AND App_info.`idBooth` IN (
                SELECT App_booths.`idBooth` 
                FROM `App_booths` 
                WHERE App_booths.`owner` = {$owner}
            )
            GROUP BY DATE(App_info.`when`)
            ORDER BY App_info.`when`
        ";
        
        $query0 = $this->my_query($sql);

        if($query0){
            while($info = $this->my_fetch_array($query0)){
                $result[$info['when_']]['i3_60'] = $info['i3_60'];
            }
        }
        
        
         //Afegim i4_60 que enviem a partir de la versió 3.1.7 de Britta (Newport de moment en parche de 3.1.6)
        $sql = "
            SELECT DATE(App_info.`when`) AS when_, SUM(i4) AS i4_60 
            FROM App_info
            WHERE App_info.`typeInfo` = 60 AND i4!=65535
            AND (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
                {$filterByidPB}
            AND App_info.`idBooth` IN (
                SELECT App_booths.`idBooth` 
                FROM `App_booths` 
                WHERE App_booths.`owner` = {$owner}
            )
            GROUP BY DATE(App_info.`when`)
            ORDER BY App_info.`when`
        ";
        
        $query0 = $this->my_query($sql);

        if($query0){
            while($info = $this->my_fetch_array($query0)){
                $result[$info['when_']]['i4_60'] = $info['i4_60'];
            }
        }
        
        $sql = "
            SELECT `when` AS collection_time, DATE(App_info.`when`) AS when_, i1, stock
            FROM App_info
            WHERE App_info.`typeInfo` = 20
            AND (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            $filterByidPB
            AND App_info.`idBooth` IN (
                SELECT App_booths.`idBooth` 
                FROM `App_booths` 
                WHERE App_booths.`owner` = {$owner}
            )
            ORDER BY App_info.`when`
        ";
        
        $query1 = $this->my_query($sql);
        if($query1){
            while($info = $this->my_fetch_array($query1)){
                if(!is_array($result[$info['when_']]['collectionNum'])) {
                    $result[$info['when_']]['collectionNum'] = array();
                }
                if($result === FALSE || !array_key_exists('stock', $result[$info['when_']])){
                    //En el cas que no hi hagi partides pero si collection, volem mostrar el stock, ja que es conegut
                    $result[$info['when_']]['stock'] = $info['stock'];
                }
                
                $result[$info['when_']]['collection'] = TRUE;
                $result[$info['when_']]['collection_time'] = DateTime::createFromFormat('Y-m-d H:i:s', $info['collection_time']);
                array_push($result[$info['when_']]['collectionNum'], $info['i1']);
            }
        }
        
        
        
        /*****
         * 
         * Stock final del dia, no inicial
         */
        $sqlStock = "
            SELECT DATE(App_info.`when`) AS when_, stock
            FROM App_info
            WHERE App_info.`typeInfo` = 10
            AND (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            $filterByidPB
            AND App_info.`idBooth` IN (
                SELECT App_booths.`idBooth` 
                FROM `App_booths` 
                WHERE App_booths.`owner` = {$owner}
            )
            ORDER BY App_info.`when` ASC
        ";
                
        $queryStock = $this->my_query($sqlStock);
        if($queryStock){
            while($info = $this->my_fetch_array($queryStock)){
   
                $result[$info['when_']]['stock'] = $info['stock'];
                
            }
        }
        
        
        
        /*
         *Cost Twilio
         * */ 
        if($idPb){
            $AndPBOwner = "AND idBooth = {$idPb}";
        }else{
            $AndPBOwner = "AND owner = {$owner}";
        }
        $sql2 = "
         
            SELECT cost, method , DATE(gestor.`last`) AS when_
            FROM gestor
            WHERE gestor.`last` >= '{$startDate->format('Y-m-d H:i:s')}' AND gestor.`last` <= '{$endDate->format('Y-m-d H:i:s')}'
            AND `state`=6 
            AND (`method` = 1 OR `method` = 3)
            AND `cost`!=0
            ".$AndPBOwner;
        
        $query2 = $this->my_query($sql2);
        $rows2 = 0;
        if($query2){
            while($info = $this->my_fetch_array($query2)){
                
                if(!isset($result[$info['when_']]['costTwilio'])){
                    $result[$info['when_']]['costTwilio'] = $info['cost'];
                    if($info['method']==3){
                        $result[$info['when_']]['costWhats'] = $info['cost'];
                        $result[$info['when_']]['qtyWhats'] = 1;
                    }
                    if($info['method']==1){
                        $result[$info['when_']]['costSMS'] = $info['cost'];
                        $result[$info['when_']]['qtySMS'] = 1;
                    }
                }else{
                    $result[$info['when_']]['costTwilio'] += $info['cost'];
                    if($info['method']==3){
                        $result[$info['when_']]['costWhats'] += $info['cost'];
                        $result[$info['when_']]['qtyWhats'] += 1;
                    }
                    if($info['method']==1){
                        $result[$info['when_']]['costSMS'] += $info['cost'];
                        $result[$info['when_']]['qtySMS'] += 1;
                    }
                }
//                if($anteriorWhen != $info['when_']){
//                    array_push($result[$info['when_']]['costTwilio'], $info['cost']);
//                }
                
                $anteriorWhen = $info['when_'];
                $rows2++;
            }
            //afegim la última fila si hi ha rows
//            if($rows2){
//               array_push($result[$info['when_']]['costTwilio'], $info['cost']); 
//            }
            
        }
          
                
        return $result;
    }
    
    public function getVersionArrayByPb($idPb, $owner, $startDate, $endDate){
        $result = FALSE;
        $filterByidPB = '';
        
        if($idPb != FALSE){
            $filterByidPB = "AND App_info.`idBooth` = {$idPb}";
        }

        $sql = "
            SELECT 
               
                MIN(App_info.`PBnew`) AS PBnew,
		typeInfo
                
            FROM (SELECT * FROM `App_info` ORDER BY App_info.`when` DESC) App_info
            
            WHERE (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            {$filterByidPB}
            AND App_info.`idBooth` IN (
                SELECT App_booths.`idBooth` 
                FROM `App_booths` 
                WHERE App_booths.`owner` = {$owner}
            )
            GROUP BY DATE(App_info.`when`)
            ORDER BY App_info.`when`
        "; 
                
//         print  $sql;exit;         
        $query = $this->my_query($sql);

        if($query){
            $result = array();
            while($info = $this->my_fetch_array($query)){
               
                $result[$info['when_']]['typeInfo'] = $info['typeInfo'];               
                $result[$info['when_']]['PBnew'] = $info['PBnew'];
               
            }
        }
        //Si no hi ha registres app_info mirem la ultima versió comunicada desde la data d'inici
        if(empty($result)){
            $sql = "
                    SELECT 

                        PBnew,
                        typeInfo

                    FROM (SELECT * FROM `App_info` ORDER BY App_info.`when` DESC) App_info

                    WHERE (
                        App_info.`when` <= '{$startDate->format('Y-m-d H:i:s')}'
                        
                    )
                    {$filterByidPB}
                    AND App_info.`idBooth` IN (
                        SELECT App_booths.`idBooth` 
                        FROM `App_booths` 
                        WHERE App_booths.`owner` = {$owner}
                    )
                    
                   
                    LIMIT 1
                "; 

               
                $query = $this->my_query($sql);

                if($query){
                    $result = array();
                    while($info = $this->my_fetch_array($query)){

                        $result[$info['when_']]['typeInfo'] = $info['typeInfo'];               
                        $result[$info['when_']]['PBnew'] = $info['PBnew'];

                    }
                }

        }
        
                
        return $result;
    }
    
    public function getLastStockByPb($idPb, $startDate) {
        $stock = null; 
        $filterByidPB = '';
    
        if ($idPb !== false) {
            $filterByidPB = "AND App_info.`idBooth` = {$idPb}";
        }
    
        $sql = "
            SELECT stock
            FROM App_info
            WHERE App_info.`when` <= '{$startDate->format('Y-m-d H:i:s')}'
            AND stock IS NOT NULL
            {$filterByidPB}
            ORDER BY App_info.`when` DESC
            LIMIT 1
        ";
    
        $query = $this->my_query($sql);
    
        if ($query) {
            if ($info = $this->my_fetch_array($query)) { 
                $stock = $info['stock'];
            }
        } else {
           
            error_log("Error en la consulta: {$sql}"); 
        }
    
        return $stock; 
    }
        
    
    public function getAuditsStatus($startDate, $endDate, $owner, $idPb = FALSE){
        $result = FALSE;

        $filterByidPB = '';
        if($idPb != FALSE){
            $filterByidPB = "AND App_info.`idBooth` = {$idPb}";
        }
        
        $sql = "
            SELECT count(App_info.`idInfo`) AS num, DATE(App_info.`when`) AS when_
            FROM `App_info`
            WHERE (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            {$filterByidPB}
            AND App_info.`idBooth` IN (
                SELECT App_booths.`idBooth` 
                FROM `App_booths` 
                WHERE App_booths.`owner` = {$owner}
            )
            GROUP BY DATE(App_info.`when`)
            ORDER BY App_info.`when` DESC
            LIMIT 1
        ";   
//                print $sql;exit;
        $query = $this->my_query($sql);
        if($query){
            $i = 0;
            $result = array();
            while($status_ = $this->my_fetch_array($query)){
                $result[$i]['num'] = $status_['num'];
                $result[$i]['when_'] = $status_['when_'];
            }
        }
        
        return $result;
    }
    
    public function getAuditLastConection($owner, $idPb = FALSE){
        $array = FALSE;
        
        $filterByidPB = '';
        if($idPb != FALSE){
            $filterByidPB = "AND App_info.`idBooth` = {$idPb}";
        }
        
        $sql = "
            SELECT DATE( `App_info`.`when` ) AS when_,  DATE(`App_booths`.`lastConn`) AS lastConn
            FROM `App_info`
            INNER JOIN  `App_booths` 
            ON `App_info`.`idBooth` =  `App_booths`.`idBooth`
            WHERE App_info.`idBooth` IN (SELECT App_booths.`idBooth` FROM `App_booths` WHERE App_booths.`owner` = {$owner})

            $filterByidPB
    
            GROUP BY DATE(`App_info`.`when`)
            ORDER BY `App_info`.`when` DESC
            LIMIT 1
        ";       

        $query = $this->my_query($sql);
        
        if($query){
            $i = 0;
            $array = array();
            while($status_ = $this->my_fetch_array($query)){
                $array[$i]['when_'] = $status_['when_'];
                $array[$i]['lastConn'] = $status_['lastConn'];
            }
        }
        
        if($array[0]['when_'] > $array[0]['lastConn']){
            $result = $array[0]['when_'];
        }
        else{
            $result = $array[0]['lastConn'];
        }
        
        return $result;
    }
    
    public function getInfoSessionsByMonth($idPb, $owner){
        $result = FALSE;
                
        if($idPb != FALSE){
            $filterByidPB = "App_info.`idBooth` = {$idPb}";
        }
        
        $sql = "
            SELECT                 
                DATE(App_info.`when`) AS when_,                  
                App_info.`typeInfo` AS typeInfo,                 
                App_info.`pbs_time`, 
                App_info.`db_time`,
                App_currencies.`position` AS currency_position,
                App_currencies.`symbol` AS currency_symbol
            FROM `App_info`
            LEFT JOIN App_currencies
            ON App_currencies.`code` = App_info.`currency`
            WHERE 
            {$filterByidPB}
            AND App_info.`idBooth` IN (
                SELECT App_booths.`idBooth` 
                FROM `App_booths` 
                WHERE App_booths.`owner` = {$owner}
            )
            GROUP BY DATE(App_info.`when`)
            ORDER BY App_info.`when`
        ";
        
        $query = $this->my_query($sql);
        if($query){
            $result = array();
            $i = 0;
            while($status_ = $this->my_fetch_array($query)){
                $result[$i]['when_'] = $status_['when_'];
                $result[$i]['typeInfo'] = $status_['typeInfo'];
                $i++;
            }
        }
//        print_r($result);

        return $result;
    }
    
    public function getInfoSessionsByDay($idPb, $owner){
        $result = FALSE;
        if($idPb != FALSE){
            $filterByidPB = "App_info.`idBooth` = {$idPb}";
        }
        $sql = "
            SELECT 
                App_info.`when` AS when_,                 
                App_info.`typeInfo` AS typeInfo,                 
                App_info.`pbs_time`, 
                App_info.`db_time`,
                App_currencies.`position` AS currency_position,
                App_currencies.`symbol` AS currency_symbol
            FROM `App_info`
            LEFT JOIN App_currencies
            ON App_currencies.`code` = App_info.`currency`
            WHERE 
            {$filterByidPB}
            AND App_info.`idBooth` IN (
                SELECT App_booths.`idBooth` 
                FROM `App_booths` 
                WHERE App_booths.`owner` = {$owner}
            )
            ORDER BY App_info.`when`
        ";
        $query = $this->my_query($sql);
        if($query){
            $result = array();
            $i = 0;
            while($status_ = $this->my_fetch_array($query)){
                $result[$i]['pbs_time'] = $status_['when_'];
                $result[$i]['typeInfo'] = $status_['typeInfo'];
                $i++;
            }
        }

        return $result;
    }
    
    public function getOwnerPb($owner){
        $this->setFilter('owner', '=', $owner);
        $this->setLimit(1);
        $result = $this->select('App_booths');
        
//        $result = $result[0]['idBooth'];
        return $result;
    }
    
    public function getReapDcActibity($idBooth, $startDateTime, $endDateTime){
        $result = FALSE;
       
        $startDateTime = $startDateTime->format('Y-m-d H:i:s');
        $endDateTime   = $endDateTime->format('Y-m-d H:i:s');
        
        $sql = "
        SELECT App_info.`when`, 
        App_info.`typeInfo`, 
        App_info.`i1`, 
        App_info.`stock`, 
        App_products.`descr` AS Product, 
        COALESCE(SUM(App_info.`i3`),0) AS Cash,
        COALESCE(SUM(App_info.`i4`),0) AS Card, 
        COALESCE(SUM(App_info.`i5`),0) AS Net,
        COALESCE(SUM(App_info.`money`), 0) AS money_, 
        COALESCE(SUM(App_info.`money2`), 0) AS extraMoney,
        MIN(App_info.`PBnew`) AS PBnew, 
        App_currencies.`position` AS currency_position,
        App_currencies.`symbol` AS currency_symbol
        FROM App_info 
        LEFT JOIN App_products ON App_info.i1 = App_products.id  
        LEFT JOIN App_currencies ON App_currencies.`code` = App_info.`currency`
        WHERE (`when` >= '{$startDateTime}' AND `when` <= '{$endDateTime}')
        AND idBooth= {$idBooth}
        GROUP BY App_info.`when`";
        
        $query = $this->my_query($sql);
        
        if($query){
            $result = array();
            $i = 0;
            while($info = $this->my_fetch_array($query)){
                $result[$i]['when'] = DateTime::createFromFormat('Y-m-d H:i:s', $info['when']);
                $result[$i]['typeInfo'] = $info['typeInfo'];
                $result[$i]['i1'] = $info['i1'];
                $result[$i]['Product'] = $info['Product'];
                $result[$i]['stock'] = $info['stock'];
                $result[$i]['Cash'] = $info['Cash'];
                $result[$i]['Card'] = $info['Card'];
                $result[$i]['Net'] = $info['Net'];
                $result[$i]['money_'] = $info['money_'];
                $result[$i]['extraMoney'] = $info['extraMoney'];
                $result[$i]['PBnew'] = $info['PBnew'];
                $result[$i]['currency_position'] = $info['currency_position'];
                $result[$i]['currency_symbol'] = $info['currency_symbol'];
                $i++;
            }
        }
        else{
            return "Error Database Repdc_activity, error code: 0002 $sql";
        }
        
        return $result;
    }
    
    public function moneyByPayment($idBooth, $startDateTime, $endDateTime, $currency){
        $result = FALSE;

        $startDateTime = $startDateTime->format('Y-m-d H:i:s');
        $endDateTime = $endDateTime->format('Y-m-d H:i:s');
        
        $sql = "SELECT SUM(`i3`) AS Cash, sum(`i4`) AS Card, sum(`i5`) AS Net,  SUM(`money`) AS Money 
                FROM `App_info`  
                WHERE currency = '{$currency}'
                AND `typeInfo` = 10 
                AND (`when` >= '{$startDateTime}' 
                AND  `when` <= '{$endDateTime}') 
                AND idBooth = {$idBooth}
        ";
        
        $query = $this->my_query($sql);
        
        if($query){
            $result = array();
            $i = 0;
            while($info = $this->my_fetch_array($query)){
                $result[$i]['Cash']   =  $info['Cash'];
                $result[$i]['Card']   =  $info['Card'];
                $result[$i]['Net']    =  $info['Net'];
                $result[$i]['Money']  =  $info['Money'];
                $i++;
            }
        }
        else{
            return "Error Database Repdc_activity, error code: 0002 $sql";
        }
        
        return $result;
        
        
        
    }
    
    public function getProducts($idBooth, $startDateTime, $endDateTime, $currency){
        $result = FALSE;
        
        $startDateTime = $startDateTime->format('Y-m-d H:i:s');
        $endDateTime = $endDateTime->format('Y-m-d H:i:s');
        
        $sql = "SELECT  App_products.descr AS Product, myInfo.myPlays AS Play, myInfo.myMoney AS Money, myInfo.prints AS Prints
                FROM App_products 
                LEFT JOIN (SELECT i1, SUM(`money`) AS myMoney, COUNT(*) AS myPlays,
                          (COALESCE(SUM(App_info.`in4`), 0) + COALESCE(SUM(App_info.`in8`),0)) AS prints
                           FROM `App_info`   
                           WHERE currency= '{$currency}' 
                           AND `typeInfo`= 10 
                           AND money IS NOT NULL 
                           AND (`when` >= '{$startDateTime}' 
                           AND `when` <= '{$endDateTime}') 
                           AND idBooth = {$idBooth}
                           GROUP BY i1) AS myInfo 
                ON  App_products.id = myInfo.i1  
                WHERE (App_products.id BETWEEN 1 AND 999 
                OR App_products.id=1031 )   
                ORDER BY App_products.id
        ";
         
        $query = $this->my_query($sql);
        
        if($query){
            $result = array();
            $i = 0;
            while($info = $this->my_fetch_array($query)){
                $result[$i]['Product']  =  $info['Product'];
                $result[$i]['Play']     =  $info['Play'];
                $result[$i]['Money']    =  $info['Money'];
                $result[$i]['Prints']   =  $info['Prints'];
                $i++;
            }
        }
        else{
            return "Error Database Repdc_activity, error code: 0002 $sql";
        }
        
        return $result;
    }
    
    public function getTotalitzacioProducts($idBooth, $startDateTime, $endDateTime, $currency){
        $result = FALSE;

        $startDateTime = $startDateTime->format('Y-m-d H:i:s');
        $endDateTime = $endDateTime->format('Y-m-d H:i:s');
        
        $sql = "SELECT count(*) AS Play, SUM(`money`) AS Money,
                (COALESCE(SUM(App_info.`in4`), 0) + COALESCE(SUM(App_info.`in8`),0)) AS Prints
                FROM `App_info`  
                WHERE currency = '{$currency}' 
                AND `typeInfo`= 10 
                AND money IS NOT NULL 
                AND `i1` > 999 
                AND i1 <> 1031 
                AND (`when` >= '{$startDateTime}' AND `when` <= '{$endDateTime}') 
                AND idBooth= {$idBooth} 
        ";
                
        $query = $this->my_query($sql);
        
        if($query){
            $result = array();
            $i = 0;
            while($info = $this->my_fetch_array($query)){
                $result[$i]['Play']     =  $info['Play'];
                $result[$i]['Money']    =  $info['Money'];
                $result[$i]['Prints']    =  $info['Prints'];
                $i++;
            }
        }
        else{
            return "Error Database Repdc_activity, error code: 0002 $sql";
        }
        
        return $result;
    }
    
    public function getfreeplays($idBooth, $startDateTime, $endDateTime){
        $result = FALSE; 
        
        $startDateTime = $startDateTime->format('Y-m-d H:i:s');
        $endDateTime = $endDateTime->format('Y-m-d H:i:s');
        
        $sql = "
            SELECT  App_products.descr AS Product, myInfo.myPlays AS Plays, myInfo.prints AS Prints
            FROM App_products LEFT JOIN   
                (SELECT i1, COUNT(*) AS myPlays,
                (COALESCE(SUM(App_info.`in4`), 0) + COALESCE(SUM(App_info.`in8`),0)) AS prints
                 FROM `App_info`   
                 WHERE `typeInfo`= 10 
                 AND money IS NULL 
                 AND (`when` >= '{$startDateTime}' AND `when` <= '{$endDateTime}') 
                 AND idBooth = {$idBooth}  
                 GROUP BY i1) AS myInfo 
            ON  App_products.id = myInfo.i1  
            WHERE (App_products.id BETWEEN 1 AND 999 OR App_products.id=1031 )   
            ORDER BY App_products.id
        ";
        
        $query = $this->my_query($sql);
        
        if($query){
            $result = array();
            $i = 0;
            while($info = $this->my_fetch_array($query)){
                $result[$i]['Product'] =  $info['Product'];
                $result[$i]['Plays']   =  $info['Plays'];
                $result[$i]['Prints']   =  $info['Prints'];
                $i++;
            }
        }
        else{
            return "Error Database Repdc_activity, error code: 0002 $sql";
        }
        
        return $result;
    }
    
    public function getOtherProducts($idBooth, $startDateTime, $endDateTime){
        $result = FALSE;

        $startDateTime = $startDateTime->format('Y-m-d H:i:s');
        $endDateTime = $endDateTime->format('Y-m-d H:i:s');
        
        $sql = "
            SELECT count(*) AS myPlays, (COALESCE(SUM(App_info.`in4`), 0) + COALESCE(SUM(App_info.`in8`),0)) AS prints
            FROM `App_info`  
            WHERE `typeInfo` = 10 
            AND money IS NULL 
            AND `i1` > 999 
            AND i1 <> 1031 
            AND (`when` >= '{$startDateTime}' AND `when` < '{$endDateTime}') 
            AND idBooth = {$idBooth} 
        ";
        
        $query = $this->my_query($sql);
        
        if($query){
            $result = array();
            $i = 0;
            while($info = $this->my_fetch_array($query)){
                $result[$i]['myPlays'] =  $info['myPlays'];
                $result[$i]['prints'] =  $info['prints'];
                $i++;
            }
        }
        else{
            return "Error Database Repdc_activity, error code: 0002 $sql";
        }
        
        return $result;
    }
    
    public function getMailSummary($idPb, $startDate, $endDate){
        /* Consulta amb les currency
        $sql = "
            SELECT COUNT(App_info.`idInfo`) AS plays,
            COALESCE(SUM(App_info.`i3`),0) + COALESCE(SUM(App_info.`i4`),0) + COALESCE(SUM(App_info.`i5`),0) - COALESCE(SUM(App_info.`money`), 0) AS overpayment,  
            COALESCE(SUM(App_info.`i3`),0) + COALESCE(SUM(App_info.`i4`),0) + COALESCE(SUM(App_info.`i5`),0) AS money,
            (COALESCE(SUM(App_info.`in4`), 0) + COALESCE(SUM(App_info.`in8`),0)) AS prints, 
            App_currencies.`position` AS currency_position,
            App_currencies.`symbol` AS currency_symbol,
            App_info.`currency`
            
            FROM  (SELECT * FROM `App_info` ORDER BY App_info.`when` DESC) App_info
            LEFT JOIN App_currencies
            ON App_currencies.`code` = App_info.`currency`
            
            WHERE App_info.`typeInfo` IN (10, 60)
            AND (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            AND App_info.`idBooth` = {$idPb}
                
            GROUP BY currency";
        */
        
        /*Count de plays, freeplays i stock final*/
        $result = array();
        $sql0 ="  
            SELECT 
                COUNT(CASE WHEN (App_info.`typeInfo` = 10) AND (App_info.`money` IS NOT NULL) THEN 1 ELSE NULL END) AS `plays`,
                COUNT(CASE WHEN (App_info.`typeInfo` = 10) AND (App_info.`money` IS NULL) THEN 1 ELSE NULL END) AS `freeplays`,
                App_info.`stock`
            FROM  (SELECT * FROM `App_info` ORDER BY App_info.`when` DESC) App_info
            LEFT JOIN App_currencies
            ON App_currencies.`code` = App_info.`currency`
            WHERE App_info.`typeInfo` IN (10,20)
            AND (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            AND App_info.`idBooth` = {$idPb}
        ";
            
        $query = $this->my_query($sql0);
        $info = $this->my_fetch_array($query);
                
        if($query){
            $result[0]['plays'] .=  $info['plays'];
            $result[0]['freeplays'] .=  $info['freeplays'];
        }
        
        $sql01 ="  
            SELECT App_info.`stock`
            FROM  (SELECT * FROM `App_info` ORDER BY App_info.`when` DESC) App_info
            LEFT JOIN App_currencies
            ON App_currencies.`code` = App_info.`currency`
            WHERE App_info.`typeInfo` IN (10,20)
            AND (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            AND App_info.`idBooth` = {$idPb}
        ";
        
        $query01 = $this->my_query($sql01);
        
        if($query01){
            while($info01 = $this->my_fetch_array($query01)){
                $stock = $info01['stock'];
                if($stock){
                    $result[0]['stock'] =  $stock;
                }
            }
        }
        
        /*Count de errors*/
        $sql2 ="  
            SELECT COUNT(App_info.`idInfo`) AS errors
            FROM  (SELECT * FROM `App_info` ORDER BY App_info.`when` DESC) App_info
            LEFT JOIN App_currencies
            ON App_currencies.`code` = App_info.`currency`
            WHERE App_info.`typeInfo` = 40
            AND (
            App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
            AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            AND App_info.`idBooth` = {$idPb}
        ";
            
        $query = $this->my_query($sql2);
        $info = $this->my_fetch_array($query);
                
        if($query){
            $result[0]['errors'] .=  $info['errors'];
        }
        
        $sql = "
            SELECT COALESCE(SUM(App_info.`i3`),0) + COALESCE(SUM(App_info.`i4`),0) + COALESCE(SUM(App_info.`i5`),0) - COALESCE(SUM(App_info.`money`), 0) AS overpayment,  
            COALESCE(SUM(App_info.`i3`),0) + COALESCE(SUM(App_info.`i4`),0) + COALESCE(SUM(App_info.`i5`),0) AS money,
            COALESCE(SUM(App_info.`in4`),0) + COALESCE(SUM(CASE WHEN App_info.`typeInfo` = 10 THEN App_info.`in8` ELSE 0 END),0) AS prints
            
            FROM  (SELECT * FROM `App_info` ORDER BY App_info.`when` DESC) App_info
            LEFT JOIN App_currencies
            ON App_currencies.`code` = App_info.`currency`
            
            WHERE App_info.`typeInfo` IN (10, 60)
            AND (
                App_info.`when` >= '{$startDate->format('Y-m-d H:i:s')}'
                AND App_info.`when` <= '{$endDate->format('Y-m-d H:i:s')}'
            )
            AND App_info.`idBooth` = {$idPb}
            ";
            
        
        $query = $this->my_query($sql);
        $info = $this->my_fetch_array($query);
        
        if($query){
            if($info['overpayment']<0){               
               $sumaOver = -$info['overpayment'];
               $info['overpayment'] = 0; 
               $info['money'] = $info['money'] + $sumaOver;
            }
            $result[0]['overpayment'] .=  $info['overpayment'];
            $result[0]['money']       .=  $info['money'];
            $result[0]['prints']      .=  $info['prints'];
        }
        
        return $result;
    }
    
    public function getActivityBySession($idBooth, $startDateTime, $endDateTime){
        $result = FALSE;
        
        $startDateTime = $startDateTime->format('Y-m-d H:i:s');
        $endDateTime   = $endDateTime->format('Y-m-d H:i:s');
        
        $sql = "
        SELECT App_info.`when`, 
        App_info.`typeInfo`, 
        App_info.`i1`, 
        App_info.`stock`, 
        App_products.`descr` AS Product, 
        App_info.`i3` AS Cash,
        App_info.`i4` AS Card, 
        App_info.`i5` AS Net,
        App_info.`in4`,
        App_info.`in8`,
        App_info.`money` AS money_, 
        App_info.`money2`AS extraMoney,
        App_info.`PBnew` AS PBnew, 
        App_currencies.`position` AS currency_position,
        App_currencies.`symbol` AS currency_symbol
        FROM App_info 
        LEFT JOIN App_products ON App_info.i1 = App_products.id  
        LEFT JOIN App_currencies ON App_currencies.`code` = App_info.`currency`
        WHERE (`when` >= '{$startDateTime}' AND `when` <= '{$endDateTime}')
        AND idBooth= {$idBooth}
        ";
        
        $query = $this->my_query($sql);
        
        if($query){
            $result = array();
            $i = 0;
            while($info = $this->my_fetch_array($query)){
                $result[$i]['when'] = DateTime::createFromFormat('Y-m-d H:i:s', $info['when']);
                $result[$i]['typeInfo'] = $info['typeInfo'];
                $result[$i]['i1'] = $info['i1'];
                $result[$i]['in4'] = $info['in4'];
                $result[$i]['in8'] = $info['in8'];
                $result[$i]['stock'] = $info['stock'];
                $result[$i]['Product'] = $info['Product'];
                $result[$i]['Cash'] = $info['Cash'];
                $result[$i]['Card'] = $info['Card'];
                $result[$i]['Net'] = $info['Net'];
                $result[$i]['money_'] = $info['money_'];
                $result[$i]['extraMoney'] = $info['extraMoney'];
                $result[$i]['PBnew'] = $info['PBnew'];
                $result[$i]['currency_position'] = $info['currency_position'];
                $result[$i]['currency_symbol'] = $info['currency_symbol'];
                $i++;
            }
        }
        else{
            return "Error Database Repdc_activity, error code: 0002 $sql";
        }
        
        return $result;
    }
}
