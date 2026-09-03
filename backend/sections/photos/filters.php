<div style='background-color:#FF7400;' class='fDiv' >
    <table >
        <tr>
            <td>Code : </td>
            <td><input type="text" class="textInput" id='code'></td>
            <td><input type='button' class='okB okButton' onclick='filtersPhoto();' style="top:-5px;"></td>
        </tr>
    </table>
</div>

<script>
                $(document).ready(function() {

                    $("#code").keyup(function(event) {
                        if (event.which == 13) {
                            filtersPhoto();
                        }
                    });

                });
                function filtersPhoto() {
                    var code = $("#code").val();

                    if (code.length > 0) {
                        var data = {code: code, fil: 1};
                        filters("photos", data);
                    }

                }
</script>