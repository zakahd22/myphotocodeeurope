<div style='background-color:#A20326;' class='fDiv'>
    <table >
        <tr>
            <td>PhotoBoothSN</td>
            <td><input type='text' id='serialnumber' class='textInput'></td>
            <td><input type='button' class='okB okButton' onclick='filtersIncidents();' style="top:-5px;"></td>
        </tr>
    </table>
</div>

<script>
                function filtersIncidents() {
                    var sn = $("#serialnumber").val();

                    if (sn.length > 0) {
                        var data = {sn: sn, fil: 1};
                        filters("incidents", data);
                    }

                }
</script>