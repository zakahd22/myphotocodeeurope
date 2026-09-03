function showVideo(code) {
    openOnlyOpac();
    var video = "<video src='manuals/videos/" + code + "' controls id='video' autoplay='true' preload></video>";
    $("#close2Pop").show();
    $("#photoPop").html(video);
    $("#photoPop").fadeIn(500);
    $("#photoPop").css("display", "flex");
    $("#photoPop").css("z-index", "102");
}
function mostraTube(code) {
    openOnlyOpac();
    var video = '<iframe id="youtube-iframe" width="560" height="315" src="https://www.youtube.com/embed/' + code + '?rel=0" frameborder="0" allowfullscreen></iframe>';
    $("#close2Pop").show();
    $("#photoPop").html(video);
    $("#photoPop").fadeIn(500);
    $("#photoPop").css("display", "flex");
    $("#photoPop").css("z-index", "102");
}
function expandir(id) {
    document.getElementById(id).classList.toggle("hidden");
    
}
function showItemBox(itemID) {
    var x = itemID.value;
    var id = itemID.id;
    id = String(id).slice(-2);
    add = "#newadd" + id;
    del = "#newdel" + id;
    id = "#data" + id;
    switch (x) {
        case "":
            $(add).addClass('hidden');
            $(del).addClass('hidden');
            $(id).html('');
            break;
        case "pdf":
            html = '<input name="newfile[]" id="file' + id + '" type="file" accept=".pdf" ><input type="hidden" name="ndesc[]" id="ndesc' + id + '" value="" >';
            $(id).html(html);
            $(add).removeClass('hidden');
            $(del).removeClass('hidden');
            break;
        case "video":
            html = '<input name="newfile[]" id="file' + id + '" type="file" accept="video/*" ><input type="hidden" name="ndesc[]" id="ndesc' + id + '" value="" >';
            $(id).html(html);
            $(add).removeClass('hidden');
            $(del).removeClass('hidden');
            break;
        case "youtube":
            html = '<input name="newfile[]" id="file' + id + '" type="text" maxlength="11" value="YouTube Code" ><input name="ndesc[] id="ndesc' + id + '" type="text" value="Description" >';
            $(id).html(html);
            $(add).removeClass('hidden');
            $(del).removeClass('hidden');
            break;
    }
}
function showOldItems(itemID) {
    var x = itemID.value;
    var id = itemID.id;
    id = String(id).slice(-2);
    if (x == "") {
        // mount IDs to set visibility of buttons
        idsel = '#olddata' + id;
        tryme = '#oldtryme' + id;
        add = '#oldadd' + id;
        del = "#olddel" + id;
        //Empty and hidden things
        $(idsel).html('');
        $(tryme).html('');
        $(add).addClass('hidden');
        $(del).addClass('hidden');

    } else {
        var array = [x, id];
        var x = JSON.stringify(array);
        $.ajax({
            url: 'edit/forms/manuals/listItems.php',
            dataType: 'html',
            type: 'POST',
            data: {
                data: x
            },
            success: function (data) {

                idsel = '#olddata' + id;
                $(idsel).html('');
                $(idsel).html(data);
                //$("#contentDcFrames").html(data)
            }
        });
    }
}
function cambiaTryMe(kind, id, prefix, select) {
    selectid = "#" + String(select.id);
    var x = $("option:selected", selectid).data('cadena');
    
    //x = select.text();
    idtryme = "#" + prefix + "tryme" + id;
    
    var idadd = "#" + prefix + "add" + id;
    var iddel = "#" + prefix + "del" + id;
    var tryme = "";
    switch (kind) {
        case "":
            tryme = "";
            $(idadd).addClass('hidden');
            $(iddel).addClass('hidden');
        break;
        case "pdf":
            tryme = "<a href='manuals/" + x + "' download>Try me</a>";
            $(idadd).removeClass('hidden');
            $(iddel).removeClass('hidden');
            break;
        case "video":
            tryme = "<a onclick='showVideo(" +'"' + x + '"' + ")'>Try me</a>";
            $(idadd).removeClass('hidden');
            $(iddel).removeClass('hidden');
            break;
        case "youtube":
            tryme = "<a onclick='mostraTube(" +'"' + x + '"' + ")'>Try me</a>";
            $(idadd).removeClass('hidden');
            $(iddel).removeClass('hidden');
            break;
    }
    
    $(idtryme).html('');
    $(idtryme).html(tryme);

}

//declare variables to set the ids of every line, for new and reused items
var newlineid = 0;
var oldlineid = 0;

function itemLine(kind) {
    switch (kind) {
        case "old":

            oldlineid++;
            id = ("0" + oldlineid).slice(-2);

            html = "";
            html += `<div class="itemLine" id="oldline${id}" name="olditems">
                        <div class="itemType" id="old${id}">
                            <select name="oldtype[]" onchange=(showOldItems(this)); id="s_old${id}">
                                <option value=""></option>
                                <option value="pdf">PDF</option>
                                <option value="video">Video</option>
                                <option value="youtube">YouTube</option>
                            </select>
                        </div>
                        <div class="itemData" id="olddata${id}">
                        </div>
                        <div class="tryMe" id="oldtryme${id}"></div>
                        <div class="addItem hidden" id="oldadd${id}" onclick="itemLine('old')">
                            <img src="images/web/addNew.png" width="20px">
                        </div>
                        <div class="delItem hidden" id="olddel${id}" onclick="itemDel('old', '${id}')">
                            <img src="images/web/trash.png" width="20px">
                        </div>
                    </div>`;

            $('#optionReuse').append(html);
            break;

        case "new":

            newlineid++;
            id = ("0" + newlineid).slice(-2);

            html = "";
            html += `<div class="itemLine" id="newline${id}" name="newitems">
                        <div class="itemType" id="new${id}">
                            <select name="newtype[]" onchange=(showItemBox(this)); id="s_new${id}">
                                <option value=""></option>
                                <option value="pdf">PDF</option>
                                <option value="video">Video</option>
                                <option value="youtube">YouTube</option>
                            </select>
                        </div>
                        <div class="itemData" id="data${id}"></div>
                        <div class="addItem hidden" id="newadd${id}" onclick="itemLine('new')">
                            <img src="images/web/addNew.png" width="20px">
                        </div>
                        <div class="delItem hidden" id="newdel${id}" onclick="itemDel('new', '${id}')">
                            <img src="images/web/trash.png" width="20px">
                        </div>
                    </div>`;

            $('#optionNew').append(html);
            break;
            
    }
}

function itemDel(kind, id) {

    var list= 'div[name="'+ kind +'items"]';
    _tot = $(list).length;
    
    var id = parseInt(id);   
    
    id = ("0" + id).slice(-2);
    id = "#" + kind + 'line' + id;
    
    
    $(id).remove();   
    
    if (_tot === 1){
        itemLine(kind);
    }
    
}

//=============================== INSERTA EL NOU MANUAL ========================


$(document).ready(function (e) {

    $("#formulari").on('submit', (function (e) {
        e.preventDefault();


        $("#titolName, #titolVersion, #titolReuse, #titolNew, #titolBooths").removeClass('error');
        a = false;

        var name = $('[name="name"]').val();
        var version = [];
        $('#optionVersion input:checked').each(function () {
            version.push($(this).val());
        });


        var oldcadena = $('select[name=olddata\\[\\]] option:selected').map(function ()
        {
            return $(this).data('cadena');
        }).get();
        var olddesc = $('select[name=olddesc\\[\\]] option:selected').map(function ()
        {
            return $(this).data('desc');
        }).get();
        var newfile = $('select[name=newfile\\[\\]] option:selected').map(function ()
        {
            return $(this).data('desc');
        }).get();


        var booths = [];
        $('#optionBooths input:checked').each(function () {
            booths.push($(this).val());
        });


        error = [];
        if (!name) {
            error.push("Name");
        }
        if (version == "") {
            error.push("Version");
        }
        if (oldcadena == "" && newfile == "") {
            error.push("Reuse");
            error.push("New");
        }
        if (booths == "") {
            error.push("Booths");
        }


        var i = 0;
        while (i < error.length) {
            $("#titol" + error[i]).addClass('error');
            i++;
        }


        if (error == "") {
            data = new FormData(this);
            data.append('oldcadena[]', oldcadena);
            data.append('olddesc[]', olddesc);
            $.ajax({
                url: 'edit/forms/manuals/newItem.php',
                type: "POST",
                data: data,
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    hidePopupv2();
                },
                error: function (e) {}
            });
        } else {
            flag = false;
            var i = 0;
            while (i < error.length) {
                switch (error[i]) {
                    case "Name":
                    case "Version":
                    case "Booths":
                        alert("The manual must have " + error[i]);
                        break;
                    case "Reuse":
                    case "New":
                        if (flag == false) {
                            alert("The manual must have an Item, either in Reused items or New items");
                            flag = true;
                        }
                        break;
                }
                i++;
            }
        }
    }));
});


//==============================================================================


function deleteManual(id_manual){
    if (confirm('Are you sure you want to delete the Manual?')) {

        var array = [id_manual];
        var data = JSON.stringify(array);
        $.ajax({
            url: 'edit/forms/manuals/deleteManual.php',
            type: 'POST',
            dataType: 'html',
            data: {
                data : data
            },
            success: function(data) {
                $('div[id='+id_manual+']').addClass("hidden");
            }
        });
    }
    
}

function deleteSelectManual(){
    if (confirm('Are you sure you want to delete Manuals?')) {
        var seleccionat = $('input[name=seleccionat]:checked').map(function ()
        {
            return $(this).val();
        }).get();
        var multiple = 1;
        var array = [seleccionat, multiple];
        var data = JSON.stringify(array);
        $.ajax({
            url: 'edit/forms/manuals/deleteManual.php',
            type: 'POST',
            dataType: 'html',
            data: {
                data : data
            },
            success: function(data) {
                $.each(seleccionat , function (index, value){
                   $('div[id='+value+']').addClass("hidden");
                });
            }
        });
    }
}

//============================= EDIT MANUAL ====================================

$(document).ready(function (e) {
    $("#formulariedit").on('submit', (function (e) {
        e.preventDefault();


        $("#titolName, #titolVersion, #titolActive, #titolReuse, #titolNew, #titolBooths").removeClass('error');
        a = false;

        var name = $('[name="name"]').val();
        var version = [];
        $('#optionVersion input:checked').each(function () {
            version.push($(this).val());
        });


        var oldcadena = $('select[name=olddata\\[\\]] option:selected').map(function ()
        {
            return $(this).data('cadena');
        }).get();
        var olddesc = $('select[name=olddesc\\[\\]] option:selected').map(function ()
        {
            return $(this).data('desc');
        }).get();
        var newfile = $('select[name=newfile\\[\\]] option:selected').map(function ()
        {
            return $(this).data('desc');
        }).get();

        var olddesc = $('select[name=olddata\\[\\]] option:selected').map(function ()
        {
            return $(this).data('desc');
        }).get();

        var active = [];
        $('#optionActive input').each(function () {
            active.push($(this).val());
        });
        var todelete = [];
        $('#optionActive input:checked').each(function () {
            todelete.push($(this).val());
        });


        var booths = [];
        $('#optionBooths input:checked').each(function () {
            booths.push($(this).val());
        });


        error = [];
        if (!name) {
            error.push("Name");
        }
        if (version == "") {
            error.push("Version");
        }
        if (oldcadena == "" && newfile == "" && todelete.length == active.length) {
            error.push("Reuse");
            error.push("New");
            error.push("Active");
        }
        if (booths == "") {
            error.push("Booths");
        }



        var i = 0;
        while (i < error.length) {
            $("#titol" + error[i]).addClass('error');
            i++;
        }


        if (error == "") {

            data = new FormData(this);
            data.append('oldcadena[]', oldcadena);
            data.append('olddesc[]', olddesc);
            $.ajax({
                url: 'edit/forms/manuals/editItem.php',
                type: "POST",
                data: data,
                contentType: false,
                cache: false,
                processData: false,

                success: function (data)
                {
                    hidePopupv2();
                },
                error: function (e)
                {
                    //$("#err").html(e).fadeIn();
                }
            });
        } else {
            flag = false;
            var i = 0;
            while (i < error.length) {
                switch (error[i]) {
                    case "Name":
                    case "Version":
                    case "Booths":
                        alert("The manual must have " + error[i]);
                        break;
                    case "Active":
                    case "Reuse":
                    case "New":
                        if (flag == false) {
                            alert("The manual must have an Item, either in Active items, Reused items, or New items");
                            flag = true;
                        }
                        break;
                }
                i++;
            }
        }
    }));
});

//==============================================================================

//========================== Checkbox-Div changer ==============================

$('.bcheck').on('click', function () {

        //Changes the checked status on the hidden checkbox and the style of the div
    var checkbox = $(this).children('input[type="checkbox"]');
    checkbox.prop('checked', !checkbox.prop('checked'));

    $(this).toggleClass("bcheck");
    $(this).toggleClass("bchecked");





        //If clicking on checkall, check or uncheck all booths
    if ($(this).attr('name') == 'checkall') {
        $('input[name="booths\\[\\]"]').prop('checked', checkbox.prop("checked"));

        if (checkbox.prop("checked")) {
            $('div[name="check"]').removeClass();
            $('div[name="check"]').addClass("bchecked");
        } else {
            $('div[name="check"]').removeClass();
            $('div[name="check"]').addClass("bcheck");
        }
    }

        //if all the booths are selected, mark all. If not all are selected, unmark all.
//    tot = $('input[name="booths\\[\\]"]').length;
//    oish = 'input[name="booths\\[\\]"]:checked';
//    tot_checked = $(oish).length;

    tot = $('[name=check]').children('input').length;
    tot_checked = $('[name=check]').children('input:checked').length;

    if (tot == tot_checked) {
        $("#booth_all").prop('checked', true);
        $("#booth_all").parent().removeClass();
        $("#booth_all").parent().addClass("bchecked"); 
    }else{
        $("#booth_all").prop('checked', false);
        $("#booth_all").parent().removeClass();
        $("#booth_all").parent().addClass("bcheck"); 
    }
});

//==============================================================================

function deleteOrphans() {

    var orphans = [];
    $('[name=orphans\\[\\]').each(function () {
        orphans.push($(this).val());
    });

    if(confirm('Are you sure you want to completely remove the orphan files?')) {   
        var data = JSON.stringify(orphans);
            $.ajax({
                url: 'edit/forms/manuals/deleteOrphans.php',
            type: 'POST',
            dataType: 'html',
            data: {
                data : data
            },
            success: function (data)
            {
                alert('Orphans Deleted');
            },
            error: function (e)
            {
                alert('There was a problem while deleting the orphan files, some or all may have not been deleted.')
            }
            });
    }
}