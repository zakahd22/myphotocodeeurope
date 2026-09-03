<?
 header('Content-type: application/vnd.ms-word');
 header("Content-Disposition: attachment; filename=SerialsNumbers.doc");
 header("Pragma: no-cache");
 header("Expires: 0");
 $serials = explode(".,." , $_REQUEST['serials']);
?>
<html>
    <body>
        <?
        $x=0;
        $t=0;
        echo "<center>";
        echo "<table cellspadding='0' border='0' style='font-size:25pt;'>";
        while($x<sizeof($serials)){
           if(!empty($serials[$x])){
               $y=1;
               $t++;
               while($y<=8){
                   if($y%2 != 0){
                       echo "<tr style='height:32mm;'>";
                    }
                    echo "<td style='width:105mm;' valign='middle'><br><center><b><font face='Kabel Ult BT'>".$serials[$x];"</font></b></center></td>";
                   if($y%2 == 0){
                       echo "</tr>";
                   } 
                   $y++;
               }
               
           }
            $x++;
        }
        echo "</table>";
                echo "</center>";
        ?>
    </body>
</html>