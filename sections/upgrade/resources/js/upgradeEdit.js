function saveBootDCAllowed(id){    
    var postData = JSON.stringify($('#upgrade_info').serializeArray());
    
    $.ajax({
        url: 'edit/functions/upgrade/setBootDCAllowed.php',        
        dataType: 'json',
        type: 'POST',
        data: {
            dades : postData
        },
        success: function(data) {
            reloadPage();
        }
    });
}


//function deleteRow(id){
//    var Dongle = $("#addnewbtn").attr("dong");
//    
//    $.ajax({
//        url: 'edit/functions/financingCode/delRow.php',        
//        dataType: 'text',
//        type: 'POST',
//        data: {
//            dades : id
//        },
//        success: function(data) {
//           if(data === "Ok"){
//               edit(71 , Dongle);
//           }
//           reloadPage();
//        }
//    });   
//}



function reloadPage(){
    setSection('upgrade', 1);
}