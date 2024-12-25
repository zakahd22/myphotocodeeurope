<?php
require_once '../../common/global.php';
include '../../sessio.php';
require_once G_PATH . 'common/conexio.php';

switch($_POST['p']){
    case 'orders':
        echo getFilterOrders();
        echo getScriptFilters();
        break;
    
    case 'dongles':
        echo getFilterDongles();
        echo getScriptFilters();
        break;
    
    default:
        break;
}
    
function getFilterDongles(){
    $html = <<<HTML
        <div style="background-color:#fbba2f;" class="fDiv">
            <table>
                <tr>
                    <td>
                        Owner
                    </td>
                    <td> 
                        <input type="text" id="owner" class="textInput">
                    </td>
                    <td>
                        String
                    </td>
                    <td> 
                        <input type="text" id="string" class="textInput">
                    </td>
                    <td>
                        <input type="button" class="okB okButton" onclick="filterOrders();" style="top:-5px;">
                    </td>
                </tr>
            </table>
        </div> 
        <script>
            function getPageId(){
                return 2;
            }
        </script>
HTML;
    
    return $html;    
}


function getFilterOrders(){
    $html = <<<HTML
        <div style="background-color:#fbba2f;" class="fDiv">
            <table>
                <tr>
                    <td>
                        Owner
                    </td>
                    <td> 
                        <input type="text" id="owner" class="textInput">
                    </td>
                    <td>
                        String
                    </td>
                    <td> 
                        <input type="text" id="string" class="textInput">
                    </td>
                    <td>
                        Status
                    </td>
                    <td>
                        <select id="status" class='selectText'>
                            <option value='0'>-all-</option>
                            <option value='1'>validated</option>
                            <option value='2'>pending</option>
                        </select>
                    </td>
                    <td>
                        <input type="button" class="okB okButton" onclick="filterOrders();" style="top:-5px;">
                    </td>
                </tr>
            </table>
        </div>
        <script>
            function getPageId(){
                return 3;
            }
        </script>
HTML;
    
    return $html;
}


function getScriptFilters(){
    $script = <<<HTML
        <script>
            $(document).ready(function() {
                $("#owner").keyup(function(owner) {
                    if(owner.which == 13) {
                        filtersOwners();
                    }
                });
                $("#string").keyup(function(string) {
                    if(string.which == 13) {
                        filtersStrings();
                    }
                });
            });

            function filterOrders() {
                $("#string").val();
                $("#owner").val();

                if($("#owner").val() != ""){
                    filtersOwners();
                }
                else if($("#string").val() != ""){
                    filtersStrings();
                }
                else{
                    filtersOwners();
                }
            }

            function filtersOwners() {
                var name = "Owner";
                var owner = $("#owner").val();
                var status = $("#status").val();

                if (name.length > 0 || owner.length > 0) {
                    var data = {a: 'filterOwner', title: name, value: owner, s: status, fil: 1};
                    var pxp_pageId = getPageId();
                    filtersProfile("payxprint", pxp_pageId, data);
                }
            }

            function filtersStrings() {
                var name = "String";
                var string = $("#string").val();
                var status = $("#status").val();
            
                if (name.length > 0 || owner.length > 0) {
                    var data = {a: 'filterString', title: name, value: string, s: status, fil: 1};
                    var pxp_pageId = getPageId();
                    filtersProfile("payxprint", pxp_pageId, data);
                }
            }
        </script>           
HTML;
    
    return $script;
}
?>