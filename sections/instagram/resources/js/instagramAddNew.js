function saveInstagramHashtagUsers(){
    var postData = JSON.stringify($('#addSuggestions').serializeArray());

    $.ajax({
        url: 'edit/functions/instagram/addNewInstagramHashtagsUsers.php',      
        dataType: 'text',
        type: 'POST',
        data: {
            dades : postData
        },
        success: function(data) {
            if(data === "Ok"){
                setSection("instagram", 1);
            }
            else{
                alert("insert fail");
            }
        }
    });
}

