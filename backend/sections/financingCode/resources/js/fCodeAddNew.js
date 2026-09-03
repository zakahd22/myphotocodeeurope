function saveFinancingCode(){
    var postData = JSON.stringify($('#addFinancing').serializeArray());

    $.ajax({
        url: 'edit/functions/financingCode/addNewFinancingDongle.php',      
        dataType: 'text',
        type: 'POST',
        data: {
            dades : postData
        },
        success: function(data) {
            if(data === "Ok"){
                setSection("financingCode", 1);
            }
            else{
                alert("insert fail");
            }
        }
    });
}

