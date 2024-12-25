function saveUpgrade(){
    var postData = JSON.stringify($('#addUpgrades').serializeArray());
//alert(postData);
    $.ajax({
        url: 'edit/functions/upgrade/addNewUpgrade.php',      
        dataType: 'text',
        type: 'POST',
        data: {
            dades : postData
        },
        success: function(data) {
            if(data === "Ok"){
                setSection("upgrade", 1);
            }
            else{
                alert("insert fail");
            }
        }
    });
}

