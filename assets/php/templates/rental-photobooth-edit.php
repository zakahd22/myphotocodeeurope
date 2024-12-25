<?
	
	//get the photobooth..
        $sql = "SELECT * FROM booth_types WHERE `char`='{$myRS['type']}'";
	$q = mysql_query($sql);
        $bt = mysql_fetch_array($q);
	if ($bt)
	{
            $pb_type = $bt['name'];
	}
        else $pb_type = "-";
	
//SELECT `idBooth`, `estat`, `type`, `owner`, `name`, `obs`, `serialnumber`, `location`, `latitude`, `longitude`, `alertOffline`, `hS`, `mS`, `hE`, `mE`, `report`, `cardReaderSN`, `version` FROM `App_booths` WHERE 1
	
	if ($_REQUEST['form_id'] == "pb")
	{
	
		$name = $_REQUEST['name'];
		$location = $_REQUEST['location'];
		$tmp = $_REQUEST['latitude']; if($tmp) {$latitude =  sprintf("%.0f",$tmp*1000000);} else {$latitude = 'NULL';}
		$tmp = $_REQUEST['longitude'];if($tmp) {$longitude = sprintf("%.0f",$tmp*1000000);} else {$longitude = 'NULL';}
                
		$alertOffline = $_REQUEST['alertOffline'];//Yes NO
		$hS = $_REQUEST['hS']; 
                $pmS = $_REQUEST['pmS']; if($pmS == "PM"){if($hS != 12){$hS+=12;}} else {if($hS == 12){$hS = 0;}}
		$mS = $_REQUEST['mS'];
		$hE = $_REQUEST['hE'];
                $pmE = $_REQUEST['pmE']; if($pmE == "PM"){if($hE != 12){$hE+=12;}} else {if($hE == 12){$hE = 0;}}
		$mE = $_REQUEST['mE'];
                
//20131003 INICI hora i minuts corretgits per a que sigui server time
//20131003                if($hE > $hS){
//20131003                    $midnight = 0;
//20131003                }
//20131003                elseif($hE < $hS){
//20131003                    $midnight = 1;
//20131003                }
//20131003                else{ //la mateixa hora
//20131003                    if($mE >= $mS){
//20131003                        $midnight = 0;
//20131003                    }
//20131003                    elseif($mE < $mS){
//20131003                        $midnight = 1;
//20131003                    }
//20131003                }

//20131003 FINAL
                
                
                
                
                
                
 //20131003 INICI  timeZone
                
    $ara = new DateTime("now");
//    echo "<p>ara: ".$ara->format("'Y-m-d H:i'")."</p>";
    $araTzone = $ara->getTimezone();//time zone del server
    
                if(isset($_REQUEST['tz'])){
                    $tz = $_REQUEST['tz']; 
                }
                else{
                    $tz = $araTzone->getName();
                }
               
                
        
        $araStart = new DateTime("now",new DateTimeZone($tz));//time zone del PB
        $araStart->setTime($hS, $mS, 0);
//        echo "<p>ara start local: ".$araStart->format("'Y-m-d H:i'")."</p>";
        $araStart->setTimezone($araTzone);
//        echo "<p>ara start server: ".$araStart->format("'Y-m-d H:i'")."</p>";
        $hmS = $araStart->format("'Hi'");
        
        $araEnd = new DateTime("now",new DateTimeZone($tz));//time zone del PB
        $araEnd->setTime($hE, $mE, 0);
//        echo "<p>ara end local: ".$araEnd->format("'Y-m-d H:i'")."</p>";
        $araEnd->setTimezone($araTzone);
//        echo "<p>ara end server: ".$araEnd->format("'Y-m-d H:i'")."</p>";
        $hmE = $araEnd->format("'Hi'");
  
        
                if($hmE > $hmS){
                    $midnight = 0;
                }
                else{
                    $midnight = 1;
                }
           
                
                
 //20131003 FINAL
               
                
		$report = $_REQUEST['report'];//freq. díes
                
                //,`alertOffline`, `hS`, `mS`, `hE`, `mE`,`report`

 
                $values = " name='$name', location='$location', latitude=$latitude, longitude=$longitude,`alertOffline`=$alertOffline, `hS`=$hS, `mS`=$mS, `hE`=$hE, `mE`=$mE,`report`='$report',`midnight` =$midnight ";
                $values.= ", `timeZone`='$tz',  hmS=$hmS, hmE=$hmE ";//20131003
                $sql = "UPDATE `App_booths` SET $values WHERE idBooth=$PB_id;";
// echo "TRACE $sql";
		
		mysql_query($sql);
		
		$form_succeed = true;
	
	}
	
?>

<script type="text/javascript">
	
	$(document).ready(function() {
					
		
		$('.btnUploading').click(function(){

			$("#uploading").fadeIn(300);
			
		});
	
	});
	
</script>

<div id="content">

	<?
	
		$nav_bar_title = "Edition";
		$content_title = "Edit the $pb_type PhotoBooth.";
	
	?>
	
	<div id="rental-navigation"><span class="raquo">&raquo;</span> <a href="<? echo $baseUrl; ?>/rental">Home</a> <span class="raquo">&raquo;</span> <a href="<? echo $baseUrl; ?>/rental/usbs">USB Set Ups</a> <span class="raquo">&raquo;</span> <? echo $nav_bar_title; ?></div>
	
	<div id="title" style="line-height:1.2em;"><? echo $content_title; ?></div>
	
	<div style="margin-top:32px;">
		
		<?
		
		if ($form_succeed)
		{
			
			echo "<div class='succeedMsg'>";
			
			echo "PhotoBooth edited successfully.";
			echo "<br /><a href='".$baseUrl."/rental/photobooths'>&raquo; Go back to PhotoBooths.</a>";
			
			echo "</div>";
			
		}
		else
		{
		
		?>
		
		<form method="post" action="<? echo $PHP_SELF; ?>">

			<div class="form_area">
			
					<div class="label">Type: <? echo $pb_type; ?></div>
					<div class="label">Name</div>
					<div class="sublabel">Used to identify the PhotoBooth.</div>
					<div class="textfield"><input type="text" name="name" size="50" maxlength="50" value="<? echo $myRS['name']; ?>" /></div>
					<div class="label">Location</div>
					<div class="textfield"><input type="text" name="location" size="50" maxlength="50" value="<? echo $myRS['location']; ?>" /></div>
					<div class="label">Latitude</div>
					<div class="sublabel">Numeric value!</div>
					<div class="textfield"><input type="text" name="latitude" value="<?  if($myRS['latitude'])  echo $myRS['latitude']/1000000;?>" /></div>
					<div class="label">Longitude</div>
					<div class="sublabel">Numeric value!</div>
					<div class="textfield"><input type="text" name="longitude" value="<? if($myRS['longitude']) echo $myRS['longitude']/1000000;?>" /></div>
                                        
 					<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>
					
					<div class="label">Alert Offline</div>
					<div class="textfield" style="margin-top:4px;font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif;font-size:0.9em;">
						<? echo "<input style='' type='radio' "; if ($myRS['alertOffline'] == 1) { echo "checked "; } echo "name='alertOffline' value='1' /> Yes"; ?>
						<? echo "<input style='margin-left:24px;' type='radio' "; if ($myRS['alertOffline'] == 0) { echo "checked "; } echo "name='alertOffline' value='0' /> No"; ?>
					</div>
                                        
		<?php	
		$hS = $myRS['hS']; if($hS > 12){$hS-=12;  $pmS="PM";} elseif($hS == 12) {$pmS="PM";} else {$pmS="AM"; if($hS == 0){$hS=12;}}
		$mS = $myRS['mS'];
		$hE = $myRS['hE']; if($hE > 12){$hE-=12;  $pmE="PM";} elseif($hE == 12) {$pmE="PM";} else {$pmE="AM"; if($hE == 0){$hE=12;}}
		$mE = $myRS['mE'];
		?>
                                        
                                        
                                        <span class="label">From&nbsp;</span>
                                        <select name="hS">
                                        <?php
                                        for($i=1;$i<=12;$i++){
                                        echo "<option value='$i'"; if ($hS == $i) { echo " selected"; } echo ">$i</option>";
                                        }
                                        ?>
                                        </select>
                                       &nbsp;
                                        <select name="mS">
                                        <?php
                                        for($i=0;$i<60;$i+=5){
                                        echo "<option value='$i'"; if ($mS == $i) { echo " selected"; } echo ">$i</option>";
                                        }
                                        ?>
                                        </select>
                                        
                                        <select name="pmS">
                                        <?php
                                        echo "<option value='AM'"; if ($pmS == "AM") { echo " selected"; } echo ">AM</option>";
                                        echo "<option value='PM'"; if ($pmS == "PM") { echo " selected"; } echo ">PM</option>";
                                        ?>
                                        </select>
                                        <span class="label">&nbsp;&nbsp; To </span>
                                        <select name="hE">
                                        <?php
                                        for($i=1;$i<=12;$i++){
                                        echo "<option value='$i'"; if ($hE == $i) { echo " selected"; } echo ">$i</option>";
                                        }
                                        ?>
                                        </select>
                                        &nbsp;
                                        <select name="mE">
                                        <?php
                                        for($i=0;$i<60;$i+=5){
                                        echo "<option value='$i'"; if ($mE == $i) { echo " selected"; } echo ">$i</option>";
                                        }
                                        ?>
                                        </select>
                                        <select name="pmE">
                                        <?php
                                        echo "<option value='AM'"; if ($pmE == "AM") { echo " selected"; } echo ">AM</option>";
                                        echo "<option value='PM'"; if ($pmE == "PM") { echo " selected"; } echo ">PM</option>";
                                        ?>
                                        </select>
  <!-- //20131003 INICI  timeZone -->
  
   
  <div class="label">Time zone:</div>
  <select name="tz">
      <option value=''> please select one</option>
      <?php
      $tz = $myRS['timeZone'];
      $llistaTz = timezone_identifiers_list(1024);//$what, $country)
    foreach($llistaTz  as $value) {
        echo "<option value='$value'"; if ($tz == $value) { echo " selected"; } echo ">$value</option>";
    }
     ?>
  </select>
<!-- //20131003 FINAL -->
                                      
                                       
                                        
               
					<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>
					<div class="label">Report frequency</div>
                                        <select name="report">
                                        <?php
                                        $report = $myRS['report'];
                                        echo "<option value='none'"; if ($report == "none") { echo " selected"; } echo ">none</option>";
                                        echo "<option value='15'"; if ($report == "15") { echo " selected"; } echo ">15 days</option>";
                                        echo "<option value='30'"; if ($report == "30") { echo " selected"; } echo ">30 days</option>";
                                        ?>
                                        </select>
                                        
                                        
					<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>
					<div class="button" style="float:left;width:220px;padding:0px;"><input style="margin:0px;padding:0px;" type="image" alt="Submit!" src="<? echo $baseUrl; ?>/assets/images/button-save.jpg" width="220" height="63" /></div>
					<div class="button" style="float:left;margin:0px 0px 0px 20px;"><a href="<? $baseUrl; ?>/rental/photobooths"><img alt="Cancel" src="<? echo $baseUrl; ?>/assets/images/button-cancel.jpg" width="220" height="63" /></a></div>
					<div style="clear:both;"></div>
					
					<input type="hidden" name="form_id" value="pb">
					
			</div>

		</form>
		
		<?php
		} //form succeed
		
		?>
		
	</div>
	
</div> <?php //id=content ?>

<div id="uploading"><table width="100%" height="100%"><tr><td align="center" valign="middle"><span style="font-size:42px;font-weight:bold;">Uploading, please wait!</span><br /><br /><img src="<? echo $baseUrl; ?>/assets/images/uploading.gif" width="220" height="19" /><br /></td></tr></table></div>