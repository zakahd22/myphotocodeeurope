<?php
include "../../sessio.php";
if ($USERTYPE == 1 || $USERTYPE == 6 ) {
?>
<div style='background-color:#378CE8;float:left;padding-top:11px;' class='fDiv'>
    <table>
        <tr>            
           
            <td>
               <form name="searchOwnerAuditsSuper" id="searchOwnerAuditsSuper" method="" action="">
                            <input type="text" id="searchword" placeholder="Search with Owner's name or username" autocomplete="off" />
                                
                        </form>
            </td>
            <td>
               
            </td>
            
        </tr>
    </table>
                        
                       
                </div>
<div id="id_suggesstions" class="suggestionOwner" style='float:left;'>        
    <!--<div id="name-list">
                    <div class="rowOwnerSuggest" onClick="selectname('','');">Show All Owner's PhotoBooths</div>
                    
                <?php
                foreach($rentals as $rental){
                ?>
                    <div class="rowOwnerSuggest" onClick="selectname('<?php echo $rental["id"]; ?>','<?php echo $rental["name"]; ?>');"><?php echo $rental["name"]." ".$rental["username"]; ?></div>
                <?php 
		} 
		?>
    </div>-->
</div>
<!--<button type="button" class="popup-confirm selectedBtn" onClick="selectname('','');" style="margin-top:10px;">All Owners</button>-->
<button type="button" class="popup-confirm selectedBtn" onClick="setSection('audits', 1);" style="margin-top:10px;">Show All Owners & All PBs</button>
<?php
}
?>


<script>
                    $(document).ready(function() {
                        $("#companyName").keyup(function(event) {
                            if (event.which == 13) {
                                filtersAudits();
                            }
                        });
                        $("#username").keyup(function(event) {
                            if (event.which == 13) {
                                filtersAudits();
                            }
                        });
                        $("#searchOwnerAuditsSuper").keypress(function(e) {
                            if (e.which == 13) {
                                return false;
                            }
                        });
                        
                        
                        
                        
                        // when any character press on the input field keyup function call
                        $("#searchword").keyup(function(){
                            $.ajax({
                            type: "POST", // here used post method
                            url: "readOwners.php",//php file where retrive the post value and fetch all the matched item from database
                            data:'searchterm='+$(this).val(),//send data or search term to readname file to process
                            beforeSend: function(){
                                // show loader icon
                                $("#searchword").css("background","#FFF");
                            },
                            success: function(data){
                                // get the output from database on success
                                $("#id_suggesstions").show();//show the suggestions
                                $("#id_suggesstions").html(data);//append data in the box for selection
                                $("#searchword").css("background","#FFF");
                            }
                            });
                        });
                    });
                    
                    
                    
                    // call this function after select one of these suggestion for hide the suggestion box and select the value
                    function selectname(idOwner,nameOwner) {
                            $("#searchword").val(nameOwner);
                            $("#id_suggesstions").hide();
                            var data = {idOwner: idOwner, nameOwner: nameOwner, fil: 1};
                            filters("audits", data);
                    }
                    

                    function filtersAudits() {
                        var companyName = $("#companyName").val();
                        var username = $("#username").val();
                        if (companyName.length > 0 || username.length > 0) {
                            var data = {cName: companyName, uName: username, fil: 1};
                            filters("audits", data);
                        }
                        else {
                            var data = {cName: "%", uName: "%", fil: 1};
                            filters("audits", data);
                        }

                    }

</script>