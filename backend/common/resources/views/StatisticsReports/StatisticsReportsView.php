<?php
class StatisticsReportsView extends EntityUtility { //extends BaseView
    
    public function getMailEventsStatisticsReports($title, $data){
//        $dataHTML = "";
//        $dataHTML .= "<table border='1' style='margin-right:50px; margin:auto;'>";
//            $dataHTML .= "<tr>      <td colspan='2'>{$title}</td>       </tr>";
//            $dataHTML .= "<tr><td>Autocreated       </td><td>{$data['autocreated']}  </td></tr>";
//            $dataHTML .= "<tr><td>Not Autocreated   </td><td>{$data['noAutocreated']}</td></tr>";
//            $dataHTML .= "<tr><td>Total             </td><td>{$data['total']}        </td></tr>";
//        $dataHTML .= "</table>";
        $dataHTML = "<tr>";
            $dataHTML .= "<td >{$title}</td>";
            $dataHTML .= "<td>{$data['autocreated']}</td>";
            $dataHTML .= "<td>{$data['noAutocreated']}</td>";
            $dataHTML .= "<td>{$data['total']}</td>";
        $dataHTML .= "</tr>";
        return $dataHTML;
    }
    
    public function getMailPhotoUploadsStatisticsReports($titles, $data, $viewed){
        $dataHTML = "";
        $dataHTML .= "<table border='1' style='margin-right:50px; margin:auto;'>";
            $dataHTML .= "<tr ><td>Period</td><td>Uploaded Photos</td><td>Viewed Photos</td></tr>";
            $dataHTML .= "<tr><td>$titles[0]</td><td>{$data[0]}</td><td>{$viewed[0]}%</td></tr>";
            $dataHTML .= "<tr><td>$titles[1]</td><td>{$data[1]}</td><td>{$viewed[1]}%</td></tr>";
            $dataHTML .= "<tr><td>$titles[2]</td><td>{$data[2]}</td><td>{$viewed[2]}%</td></tr>";
            $dataHTML .= "<tr><td>$titles[3]</td><td>{$data[3]}</td><td>{$viewed[3]}%</td></tr>";
            $dataHTML .= "<tr><td>$titles[4]</td><td>{$data[4]}</td><td>{$viewed[4]}%</td></tr>";
//        $total = (int)$data[0]+(int)$data[1]+(int)$data[2]+(int)$data[3]+(int)$data[4];
//        $dataHTML .= "<tr><td><b>Total Upload</b></td><td><b>{$total}</b></td></tr>";
        $dataHTML .= "</table>";
        return $dataHTML;
    }
    
    public function getMailOwnersLoginStatisticsReports($titles, $data){
        $dataHTML = "";
        $dataTotal = 0;
        $titleInserted = false;
//        $dataHTML .= "<table border='1' style='margin-right:50px; margin:auto;'>";
//            $dataHTML .= "<tr>      <td colspan='2'>$titles</td>       </tr>";
//            foreach($data as $dt){
//                $dataHTML .= "<tr><td>{$dt['pais']}</td><td>{$dt['counter']}  </td></tr>";
//                $dataTotal += $dt['counter'];
//            }
//                $dataHTML .= "<tr><td><b>Total Logins</b></td><td><b>{$dataTotal}</b></td></tr>";
//        $dataHTML .= "</table>";
        foreach($data as $dt){
            $dataHTML .= "<tr>";
            if(!$titleInserted){
                $titleInserted = true;
                $rowspan = count($data)+1;
                $dataHTML .= "<td  rowspan='{$rowspan}'>{$titles}</td>";
            }
            $dataHTML .= "<td>{$dt['pais']}</td><td>{$dt['counter']}  </td></tr>";
            $dataTotal += $dt['counter'];
        }
        $dataHTML .= "<tr><td><b>Total</b></td><td colspan='2'><b>{$dataTotal}</b></td></tr>";
        return $dataHTML;
    }
    
       public function getMailPhotoFilesStatisticsReports($titles, $data){
        $dataHTML = "";
        $dataHTML .= "<table border='1' style='margin-right:50px; margin:auto;'>";
            $dataHTML .= "<tr>      <td colspan='2'>Uploaded Photos</td>       </tr>";
            $dataHTML .= "<tr><td>$titles[0]</td><td>{$data[0]}  </td></tr>";
            $dataHTML .= "<tr><td>$titles[1]</td><td>{$data[1]}  </td></tr>";
            $dataHTML .= "<tr><td>$titles[2]</td><td>{$data[2]}  </td></tr>";
            $dataHTML .= "<tr><td>$titles[3]</td><td>{$data[3]}  </td></tr>";
            $dataHTML .= "<tr><td>$titles[4]</td><td>{$data[4]}  </td></tr>";
        $dataHTML .= "</table>";
        return $dataHTML;
    }
    
    public function getMailCloudStatisticsReports($statisticsArray, $titles){
       $dataHTML = <<<HTML
               <table border='1' style='margin-right:50px; margin:auto;'>
                    <tr>   
                        <td rowspan='2'>Period</td>
                        <td rowspan='2'>Viewed Photos</td>   
                        <td colspan='3'>Shared Photos</td>   
                    </tr>
                    <tr>   
                        <td>Face</td>
                        <td>Email</td>
                        <td>Twiter</td>
                    </tr>
HTML;
        
        
        for ($i = 0; $i <= 4; $i++) {
           $total       = $statisticsArray[0][$i];
           
           $facebook    = $statisticsArray[1][$i];
           $facebook100 = utils::get_percent($facebook, $total);
           
           $mail        = $statisticsArray[2][$i];
           $mail100     = utils::get_percent($mail, $total);
           
           $twitter     = $statisticsArray[3][$i];
           $twitter100  = utils::get_percent($twitter, $total);
           
           $dataHTML .= <<<HTML
                    <tr>
                        <td>$titles[$i]</td>
                        <td>{$total}</td>
                        <td>{$facebook100}%</td>
                        <td>{$mail100}%</td>
                        <td>{$twitter100}%</td>
                    </tr> 
                
HTML;
            
        }
        
        $dataHTML .= "</table>";
        
        return $dataHTML;
    }
}
