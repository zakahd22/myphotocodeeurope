<?php
include '../../sessio.php';
require_once G_PATH . 'common/conexio.php';

if ($_SESSION['USERTYPE'] < 5 || $_SESSION['USERTYPE']==6) {
    ?>

    <div style='background-color:#6BBA70;' class='fDiv'>
        <table >
            <tr>
                <td>Title </td><td><input type='text' id='title' class='textInput'></td>
                <td>ID </td><td><input type='text' id='id' class='textInput'></td>
                <?php
                if ($_SESSION['USERTYPE'] < 4 || $_SESSION['USERTYPE']==6 ) {
                    ?>
                    <td>Owner</td><td> <input type='text' id='owner' class='textInput'></td>
                    <?php
                }
                ?>
                <td>
                    <?php
                    if ($_SESSION['USERTYPE'] < 4 || $_SESSION['USERTYPE']==6 ) {
                        ?>
                        <input type='button' class='okB okButton' onclick='filtersEvents();' style="top:-5px;">
                        <?php
                    } else {
                        ?>
                        <input type='button' class='okB okButton' onclick='filtersEvents2();' style="top:-5px;">
                        <?php
                    }
                }
                ?>
            </td>
        </tr>            
    </table>


    <script>




<?php
if ($_SESSION['USERTYPE'] < 4 || $_SESSION['USERTYPE']==6 ) {
    ?>
                        $(document).ready(function() {

                            $("#title").keyup(function(event) {
                                if (event.which == 13) {
                                    filtersEvents();
                                }
                            });
                            $("#id").keyup(function(event) {
                                if (event.which == 13) {
                                    filtersEvents();
                                }
                            });
                            $("#owner").keyup(function(event) {
                                if (event.which == 13) {
                                    filtersEvents();
                                }
                            });
                        });
    <?php
} else {
    ?>
    $(document).ready(function() {

                            $("#title").keyup(function(event) {
                                if (event.which == 13) {
                                    filtersEvents2();
                                }
                            });
                            $("#id").keyup(function(event) {
                                if (event.which == 13) {
                                    filtersEvents2();
                                }
                            });
                            $("#owner").keyup(function(event) {
                                if (event.which == 13) {
                                    filtersEvents2();
                                }
                            });
                        });
    <?php
}
?>


                    function filtersEvents() {
                        var name = $("#title").val();
                        var id = $("#id").val();
                        var owner = $("#owner").val();

                        if (name.length > 0 || owner.length > 0 || id.length > 0) {
                            var data = {title: name, id: id, owner: owner, fil: 1};
                            filters("events", data);
                        }
                        else {
                            var data = {title: "%", owner: owner, fil: 1};
                            filters("events", data);
                        }
                    }
                    function filtersEvents2() {
                        var name = $("#title").val();
                        var id = $("#id").val();

                        if(name.length > 0 && id.length > 0){
                                var data = {title: name, id: id, owner: "", fil: 1};
                                filters("events", data);
                            }else{
                                if(name.length > 0){
                                    var data = {title: name, id: "", owner: "", fil: 1};
                                    filters("events", data);
                                }else{
                                    var data = {title: "", id: id, owner: "", fil: 1};
                                    filters("events", data);
                                }
                            }

                    }

    </script>