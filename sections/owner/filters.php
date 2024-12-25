<div style='background-color:#378CE8;' class='fDiv'>

    <table>
        <tr>
            <td>
                Owner
            </td>
            <td>
                <input type='text' id='companyName' class='textInput'>
            </td>
            <?php
                if ($_SESSION['USERTYPE'] < 4 || $_SESSION['USERTYPE']==6 ) {
                    ?>
            <td>
                User Name
            </td>
            <td>
                <input type='text' id='username' class='textInput'>
            </td>
                <?PHP } ?>
            <td>
                <input type='button' class='okB okButton' onclick='filtersOwner();' style='top:-5px;'> 
            </td>
        </tr>
    </table>


</div>

<script>
                    $(document).ready(function() {
                        $("#companyName").keyup(function(event) {
                            if (event.which == 13) {
                                filtersOwner();
                            }
                        });
                        $("#username").keyup(function(event) {
                            if (event.which == 13) {
                                filtersOwner();
                            }
                        });
                    });

                    function filtersOwner() {
                        var companyName = $("#companyName").val();
                        var username = $("#username").val();
                        if (companyName.length > 0 || username.length > 0) {
                            var data = {cName: companyName, uName: username, fil: 1};
                            filters("owner", data);
                        }
                        else {
                            var data = {cName: "%", uName: "%", fil: 1};
                            filters("owner", data);
                        }

                    }

</script>