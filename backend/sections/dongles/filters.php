<div style='background-color:#5C6883;' class='fDiv' >
    <table>
        <tr>
            <td>String: </td>
            <td><input type="text" class="textInput" id='code'></td>
            <td><input type='button' class='okB okButton' onclick='filtersDongle();' style="top:-5px;"></td>
        </tr>
    </table>
</div>

<script>
        $(document).ready(function() {

                            $("#code").keyup(function(event) {
                                if (event.which == 13) {
                                    filtersDongle();
                                }
                            });
       
                        });
    
    function filtersDongle(){
        var code = $("#code").val();

        if(code.length >0){
            var data = {code : code, fil : 1};
            filters("dongles" , data);
        }
        
    }
</script>