<?php
//header('Content-Type: application/json');
require_once dirname(__FILE__) . '/../../../common/global.php';
require_once G_PATH . 'common/conexio.php';

if(isset($_SESSION['USERID'])){
    $USERID = $_SESSION['USERID'];
}

$title = "New Pay x Print Dongle";
$content = "";
$buttons = "";

$content .= "<div id='step_to_step'>";
$content .= "</div>";

$buttons .= "<input type='button' class='popup-confirm' id='okey' style='display:none;' value='Accept'>";
$buttons .= "<input type='button' class='popup-confirm' id='save' style='display:none;' value='Save'>";
$buttons .= "<input type='button' class='popup-cancel' value='Cancel' onclick=' hidePopupv2(); setSection(\"payxprint\" , 2 , {$USERID});'>";

$content .= <<<HTML
    <script>
        $(document).ready(function(){
            $("#okey").hide();
            $("#save").hide();
            $(".remove").hide();
            $("#close_popup_id").hide();
            get_selectedInfo({$USERID}, 1);
        });
            
        function get_selectedInfo(dongle_id, step){
            var ajaxData = {id: dongle_id, s: step};
            $.ajax({
                url: 'addNew/forms/payxprint/infoDongle.php',
                type: 'POST',
                //Ajax events
                success: function(data){
                    var Did;
                    $('#step_to_step').html(data);
                    
                    if(step == 1){
                        $('#dongleSelection').change(function(){
                            get_selectedInfo($(this).val(), 2);
                            $('#okey').show();
                        });
                    }
                    else if(step == 2){
                        $('#okey').click(function(){
                            $('#okey').hide();
                            $('#save').show();
                            Did = $('.dongle_id').attr('id');
                            get_selectedInfo(Did, 3);
                        });
                    }
                    else{
                        $('#save').click(function(){
                            Did = $('.dongle_id').attr('id');
                            update_payprintDongle(Did);
                        });
                    }
                },
                // Form data
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });
        }
                
        function update_payprintDongle(dongle_id, old_minStock, old_quantitat, old_preu) {
            var minStock=0, quantitat=0, preu=0;

            minStock = $('#min_stock').val();
            quantitat = $('#quantitat').val();
            preu = $('#preu').val();

            //alert("minstock = "+minStock+"quantity="+quantitat+"price="+preu);
            var ajaxData = {id: dongle_id, ms: minStock, qty: quantitat, pr: preu, new: 1};
            $.ajax({
                url: 'edit/functions/payxprint/saveDongleData.php',
                type: 'POST',
                //Ajax events
                success: function(data){
                    //alert(data);
                    if(data === "OK"){
                        hidePopupv2();
                        //closePopup();
                        setSection('payxprint' , 2 , {$USERID});
                    }
                    else{
                        alert(data);
                    }
                },
                // Form data
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });
        }
    </script>
                                
HTML;

// echo <<<HTML
//    <div class=popup_title>
//        <h1>Create a new Pay x Print Dongle</h1>
//    </div>
//    <div class="popup_content">
//        <div id="step_to_step">
//        </div>            
//
//        <p class='buttonsPopup'>
//            <input type='button' class='okB okey'>
//            <input type='button' class='okB save'>
//            <input type='button' class='cancelB donothing' onclick='closePopup(); setSection("payxprint" , 2 , {$USERID});'>
//        </p>
//    </div>
//    <script>
//        $(document).ready(function(){
//            $(".okB").hide();
//            $(".remove").hide();
//            $("#close_popup_id").hide();
//
//            get_selectedInfo({$USERID}, 1);
//        });
//        
//        function get_selectedInfo(dongle_id, step){
//            var ajaxData = {id: dongle_id, s: step};
//            $.ajax({
//                url: 'addNew/forms/payxprint/infoDongle.php',
//                type: 'POST',
//                //Ajax events
//                success: function(data){
//                    var Did;
//                    $("#step_to_step").html(data);
//                    
//                    if(step == 1){
//                        $("#dongleSelection").change(function(){
//                            get_selectedInfo($(this).val(), 2);
//                            $(".okey").show();
//                        });
//                    }
//                    else if(step == 2){
//                        $(".okey").click(function(){
//                            $(".okey").hide();
//                            $(".save").show();    
//                            Did = $(".dongle_id").attr('id');
//                            get_selectedInfo(Did, 3);
//                        });
//                    }
//                    else{
//                        $(".save").click(function(){
//                            Did = $(".dongle_id").attr('id');
//                            update_payprintDongle(Did);
//                        });
//                    }
//                },
//                // Form data
//                data: ajaxData,
//                contentType: 'application/x-www-form-urlencoded'
//            });
//        }
//                
//        function update_payprintDongle(dongle_id, old_minStock, old_quantitat, old_preu) {
//            var minStock=0, quantitat=0, preu=0;
//
//            minStock = $("#min_stock").val();
//            quantitat = $("#quantitat").val();
//            preu = $("#preu").val();
//
//            //alert("minstock = "+minStock+"quantity="+quantitat+"price="+preu);
//            var ajaxData = {id: dongle_id, ms: minStock, qty: quantitat, pr: preu, new: 1};
//            $.ajax({
//                url: 'edit/functions/payxprint/saveDongleData.php',
//                type: 'POST',
//                //Ajax events
//                success: function(data){
//                    //alert(data);
//                    if(data === "OK"){
//                        closePopup();
//                        setSection("payxprint" , 2 , {$USERID});
//                    }
//                    else{
//                        alert(data);
//                    }
//                },
//                // Form data
//                data: ajaxData,
//                contentType: 'application/x-www-form-urlencoded'
//            });
//        }
//    </script>
//                                
//HTML;

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);