var num_answers = 2;
var undef = 0;
var idOpen = 0;
var possibleAnswerEdit = 0;
var possibleQustionEdit = 0;
var candau = true;
var barraObertaTancada = 1;
var solutionFilterNextQuestion = 0;
var popupStatus = 0;
var span;

$(document).ready(function(){
    $(".logOut").hover(function(){
       $(this).addClass("logRed"); 
    },
    function(){
        $(this).removeClass("logRed"); 
    });
});

function hideLOAD() {
    $("#load").hide();
}

function nextQuestion(answer) {
    // var answer = $('input[name=answer]:checked').val();
    var actualQuestion = $("#actualQuestion").val();
    var actualSolution = $("#actualSolution").val();
    if (answer === undefined) {
        answer = 0;
        undef = undef + 1;
    }
    if (undef < 2) {
        var url = "../ajax/nextQuestion.php";
        var datos = {"answer": answer, "question": actualQuestion, "solution": actualSolution};
        $.ajax({
            url: url,
            type: "POST",
            beforeSend: function() {
                $("#errors").html("");
                //$(".loadingImage").show();
                $("#question").fadeOut(800);
                //$("#load").slideDown(200);
            },
            success: function(data) {

                if (1) {
                    $(".tot").html(data);
                    $(".tot").fadeIn(800);
                    // $(".loadingImage").hide(300);
                    //$("#load").slideUp(800);

                } else {
                    $("#errors").html(data);
                    $("#load").hide(800);
                    // $("#question").show(800);
                }
            },
            error: function() {
                $("#errors").html("I'm sorry , I have one error");
            },
            data: datos,
            contentType: 'application/x-www-form-urlencoded'
        });
    } else {
        $("#errors").html("Please select one answer");
        undef = 1;
    }

}
function lastQuestion() {
    var url = "../ajax/lastQuestion.php";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {
            $("#errors").html("");
            //$(".loadingImage").show();
            $("#question").fadeOut(800);
            //$("#load").slideDown(200);
        },
        success: function(data) {
            if (data == 0) {
                location.href = "../php/newQuestionari.php";
            } else {
                if (1) {
                    $(".tot").html(data);
                    $(".tot").fadeIn(800);

                } else {
                    $("#errors").html(data);
                    $("#load").hide(800);
                    // $("#question").show(800);
                }
            }
        },
        error: function() {
            $("#errors").html("I'm sorry , I have one error");
        },
        //data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });
}


function Solved(problem, solved) {
    var comment = $("#ownerComment").val();
    var url = "../ajax/solved.php";
    var datos = {"problem": problem, "comment": comment, "solved": solved};
    if (solved == 0) {
            var cE = $("#contactEmail").val();
            var cN = $("#contactName").val();
            var cP = $("#contactPhone").val();
            var c = $("#comments").val();            
             
            if(cN.length == 0){
                $("#error").html("The contact name is required.");
                return;
            }              
            if(cE.length == 0){
                $("#error").html("The contact email is required.");
                return;
            }            
            if (validarEmail(cE)) {
                $("#error").html("The email is no correct.");
                return;
            }
            if(cP.length == 0){
                $("#error").html("The contact phone is required.");
                return;
            }  
            if(c.length == 0){
                $("#error").html("The comments is required.");
                return;
            }  
            datos = {"problem": problem, "comment": comment, "solved": solved, "contact": cN, "email": cE, "comments": c, "telefon": cP};
        }
           defaultAjax(datos, url, "../main.php");
    }
 


function goodBye() {
    $("#logo1").fadeOut(1000);
    $("#logo2").fadeOut(1000);
    $("#logo3").fadeOut(1000);
    $("#logo4").fadeOut(1000);
    setTimeout(function() {
        $("#questions").hide();
        $("#principal").hide();
        $("#errors").hide();
        $("#bordersDIV").fadeOut(2000);

    }, 1000);


}

function getunsolvedList() {
    var url = "../ajax/unsolvedList.php";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {
            goodBye();
        },
        success: function(data) {
            $("#unsolvedList").html(data);
        },
        error: function() {
            alert("I have a error");
        },
        contentType: 'application/x-www-form-urlencoded'
    });
}
function getsolvedList() {
    var url = "../ajax/solvedList.php";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {
            goodBye();
        },
        success: function(data) {

            $("#solvedList").html(data);
        },
        error: function() {
            alert("I have a error");
        },
        contentType: 'application/x-www-form-urlencoded'
    });
}

function showProblemProfile(problem) {
    var datos = {"problemId": problem};
    var url = "../ajax/problemProfile.php";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {
            goodBye();
        },
        success: function(data) {

            $("#inicio").html(data);
        },
        error: function() {
            alert("I have a error");
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });
}
function to(href) {
    location.href = href;
}
//Crea el problema i et rediregeix a comença les preguntes.
function newProblem() {
    var booth = $("#boothType").val();
    var owner = $("#Owner").val();
    //var info = $("#problemInfo").val();
    /* if(owner ==0 || booth==0 || info.length ===0){*/
    if (owner == 0 || booth == 0) {
        var e = "";
        if (owner == 0) {
            e = e + "Please select the owner.";
        }
        if (booth == 0) {
            e = e + " Select the photobooth.";
        }
        /*  if(info.length === 0){
         e = e+" Explain us the problem.";
         }*/
        errores(e);
    } else {
        //"info": info,
        var datos = {"booth": booth, "owner": owner};
        var url = "../ajax/createproblem.php";
        defaultAjax(datos, url, "preguntes.php");
    }
}
function addAnswer() {
    var answers = new Array();
    var i = 0;
    var x = 1;
    var answer;
    while (x < num_answers + 1) {
        answer = $("#answer" + x).val();
        if (answer.length > 0) {
            answers[i] = answer;
            i++;
        }
        x++;
    }
    num_answers = num_answers + 1;
    var answerHTML = "<p class='text' style='color:black;display:block;float:left;width:100%;'> <span style='width: 10%;display: inline;float: left;'>Answer" + num_answers + ":</span> <input type='text' id='answer" + num_answers + "' name='answer" + num_answers + "'></p>";
    $("#answers").html($("#answers").html() + answerHTML);
    x = 0;
    var j = 1;
    while (x < i) {
        $("#answer" + j).val(answers[x]);
        j++;
        x++;
    }


}
function newQuestion() {
    var answers = new Array();
    var i = 0;
    var x = 1;
    var answer;
    var question = $("#textAreaQuestion").val();
    while (x < num_answers + 1) {
        answer = $("#answer" + x).val();
        if (answer.length > 0) {
            answers[i] = answer;
            i++;
        }
        x++;
    }
    if (question.length > 0) {
        if (i > 1) {
            var url = "../ajax/addQuestion.php";
            var datos = {"answers": answers, "question": question};
            defaultAjax(datos, url, "../menuAdmin.php");

        } else {

            errores("Please , write a two minium answers");
        }
    } else {
        errores("The question text is empty , please write the question");
    }
}
function newSolution() {
    var solution_text = $("#textAreaSolution").val();
    if (solution_text.length > 0) {
        var url = "../ajax/addSolution.php";
        var datos = {"solution": solution_text};
        defaultAjax(datos, url, "../menuAdmin.php");
    } else {
        errores("The solution text is empty , please write the solution.");
    }

}

function getQuestions() {
    $("#titulo").html("Questions");
    // closeMiniPopup();
    var url = "../ajax/getAllQuestions.php";
    var datos = "";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {
            $("#load").slideUp(500);
            $("#errores").hide();
        },
        success: function(data) {
            $("#lista").html(data);
            changes();
            $("#search").attr("onclick", "filtersQuestions();");
            getFiltersQuestions();
        },
        error: function() {
            errores("I have a error , please try again");
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });
}
function getSolutions() {
    $("#titulo").html("Solutions");
    // closeMiniPopup();
    var url = "../ajax/getAllSolutions.php";
    var datos = "";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {
            $("#load").slideUp(500);
            $("#errores").hide();
        },
        success: function(data) {
            $("#lista").html(data);
            $("span").hover(function() {
                $(this).attr("style", "color:greenyellow;cursor:pointer;text-shadow:1px 1px 1px green;");
            }, function() {
                $(this).attr("style", "");
            });
            $("#search").attr("onclick", "filterSolutions();");
            getFilterSolutions();
        },
        error: function() {
            errores("I have a error , please try again");
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });
}
function defaultAjax(datos, url, toGo) {
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {
            $("#load").slideUp(500);
            $("#errores").hide();
        },
        success: function(data) {
            to(toGo);
        },
        error: function() {
            errores("I have a error , please try again");
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });
}
function openCloseAnswers(open) {
    if (candau) {
        if (open == idOpen) {
            $("#q" + open).removeClass("seleccionada");
            $("#question" + open).slideUp(500);
            idOpen = 0;
        } else {
            if (idOpen !== 0) {
                $(".hiddenAnswers").slideUp(500);
                $(".llista ul").removeClass("seleccionada");
            }
            $("#q" + open).addClass("seleccionada");
            $("#question" + open).slideDown(500);
            idOpen = open;
        }
    }
}

function nextSolutionOrQuestion(QorS, idQorS, a, q) {
    if (candau) {
        possibleAnswerEdit = a;
        possibleQustionEdit = q;
        var datos = {"QorS": QorS, "id": idQorS};
        var url = "../ajax/infoMiniPopup.php";
        $.ajax({
            url: url,
            type: "POST",
            beforeSend: function() {
                $("#load").slideUp(500);
                $("#errores").hide();
            },
            success: function(data) {
                openMiniPopup(data);
            },
            error: function() {
                errores("I have a error , please try again");
            },
            data: datos,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
}
function editQS(apartat, idQS, QoS, s) {
    if (candau) {
        span = s;
        var datos = {"QorS": apartat, "QS": idQS, "QoS": QoS};
        var url = "../ajax/infoMiniPopup.php";
        $.ajax({
            url: url,
            type: "POST",
            beforeSend: function() {
                // $("#load").slideUp(500);
                //$("#errores").hide();
            },
            success: function(data) {
                openMiniPopup(data);
            },
            error: function() {
                errores("I have a error , please try again");
            },
            data: datos,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
}
function updateAnswer(ID) {
    var text = $("#txtEdit").val();
    if (text == "") {
        $("#errPOP").html("No es pot deixar el text buit");
        return;
    }
    var datos = {ID: ID, txt: text};
    var url = "../ajax/editAnswer.php";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {

        },
        success: function(data) {
            $("#" + span).html(text);
            closeMiniPopup();
        },
        error: function() {
            errores("I have a error , please try again");
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });

}
function updateQS(QSID, QoS) {
    var text = $("#txtEdit").val();
    if (text == "") {
        $("#errPOP").html("No es pot deixar el text buit");
        return;
    }
    var datos = {QSID: QSID, QoS: QoS, txt: text};
    var url = "../ajax/editQS.php";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {

        },
        success: function(data) {
            $("#" + span).html(text);
            closeMiniPopup();
        },
        error: function() {
            errores("I have a error , please try again");
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });

}
function openMiniPopup(html) {
    candau = false;
    $("#contentMiniPopup").html(html);
    $("#minipopup").fadeIn(500);
}
function closeMiniPopup() {
    candau = true;
    $("#minipopup").fadeOut(500);
    $("#contentMiniPopup").html("");
    if (barraObertaTancada % 2 == 0) {
        openQorSList("");
    }
}
function openQorSListNoHtml() {
    $("#barralateralF").animate({width: 'toggle'});
}
function openQorSList(html) {
    $("#barralateral").animate({width: 'toggle'});
    $("#barralateralContent").html(html);
    barraObertaTancada = barraObertaTancada + 1;
}
function filters(html) {
    $("#barralateralF").animate({width: 'toggle'});
}
function AjaxOpenQorSList(qs, typeBooth) {
    var datos;
    if (typeBooth !== undefined) {
        datos = {"question": possibleQustionEdit, "QorS": qs, "boothType": typeBooth};
    } else {
        datos = {"question": possibleQustionEdit, "QorS": qs};
    }
    var url = '../ajax/llistaLateral.php';
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {
            $("#load").slideUp(500);
            $("#errores").hide();
        },
        success: function(data) {
            openQorSList(data);
        },
        error: function() {
            errores("I have a error , please try again");
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });
}
//action 
//= 1 : assigna solucio a question
// =2 assignar question a question
//  = 3 asigna question a solució
function assign(assign_id, action, bt) {
    var datos;
    if (action == 3) {
        datos = {"id_assign": assign_id, "id": possibleQustionEdit, "action": action};
    } else if (action == 4) {
        datos = {"id_assign": assign_id, "id": bt, "action": action};
    } else {
        datos = {"id_assign": assign_id, "id": possibleAnswerEdit, "action": action};
    }
    var url = "../ajax/assign.php";
    $.ajax({
        url: url,
        type: "POST",
        success: function(data) {
            idOpen = 0;
            $("#QorS").html(data);
            if (action == 3) {
                getSolutions();
            } else if (action == 4) {
                getTypes();
            } else {
                getQuestions();
            }
            openQorSList("");
        },
        error: function() {
            errores("I have a error , please try again");
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });
}
function addNewAnswer() {
    var answer = $("#textAreaAnswer").val();
    if (answer.length > 0) {
        var datos = {"question": possibleQustionEdit, "answer": answer};
        var url = "../ajax/newAnswer.php";
        $.ajax({
            url: url,
            type: "POST",
            success: function(data) {
                $("#question" + possibleQustionEdit).html(data);
                closeMiniPopup();
            },
            error: function() {
                errores("I have a error , please try again");
            },
            data: datos,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
}
function noDefined() {
    datos = {"answer": possibleAnswerEdit, "question": possibleQustionEdit};
    var url = "../ajax/noDefined.php";
    $.ajax({
        url: url,
        type: "POST",
        success: function(data) {
            $("#question" + possibleQustionEdit).html(data);
            changes();
            closeMiniPopup();
        },
        error: function() {
            errores("I have a error , please try again");
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function changes() {
    $(".spanPointer").hover(function() {
        $(this).attr("style", "color:greenyellow;cursor:pointer;text-shadow:1px 1px 1px green;");
    }, function() {
        $(this).attr("style", "");
    });
    $("#qsts li").click(function() {
        $("#qsts li").attr("style", "border-bottom:1px solid grey;text-align:center;color:darkcyan;cursor:pointer;");
        $(this).attr("style", "background-color:darkcyan;color:white;border-bottom:1px solid grey;text-align:center;cursor:pointer;");
        solutionFilterNextQuestion = $(this).val();
    });
}
function getTypes() {
    var url = "../ajax/firstQuestions.php";
    $.ajax({
        url: url,
        type: "POST",
        success: function(data) {
            $("#boothTypes").html(data);
        },
        error: function() {
            errores("I have a error , please try again");
        },
        contentType: 'application/x-www-form-urlencoded'
    });
}
function getOwnerBooths() {
    var owner = $("#Owner").val();
    if (owner != 0) {
        var datos = {"owner": owner};
        var url = "../ajax/ownerBooths.php";
        $.ajax({
            url: url,
            type: "POST",
            success: function(data) {
                $("#boothType").html(data);
            },
            error: function() {
                errores("I have a error , please try again");
            },
            data: datos,
            contentType: 'application/x-www-form-urlencoded'
        });
    } else {
        $("#boothType").html("<option value='0'>SELECT OWNER</option>");
    }
}
function getOwnerBoothsByType(type) {

    var datos = {"typeBooth": type};
    var url = "../ajax/ownerBoothsType.php";
    $.ajax({
        url: url,
        type: "POST",
        success: function(data) {
            $("#boothType").html(data);
        },
        error: function() {
            errores("I have a error , please try again");
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function setToSolved() {
    var problemID = $("#problemID").val();
    var solution = $("#txtMiniPopup").val();
    if (solution.length != 0) {
        var datos = {"solution": solution, "problemID": problemID};
        var url = "../ajax/setToSolvedFromUnsolved.php";
        $.ajax({
            url: url,
            type: "POST",
            success: function() {
                showProblemProfile(problemID);
                closeMiniPopup();
            },
            error: function() {
                errPop("I have a error , please try again");
            },
            data: datos,
            contentType: 'application/x-www-form-urlencoded'
        });
    } else {
        errPop("Please , write solution");
    }

}


function errores(text) {
    $("#errors").html("<marquee><span class='red'>" + text + "</span></marquee>").slideDown(1000);
}
function errPop(text) {
    $("#errPOP").html("<marquee><span class='red'>" + text + "</span></marquee>").slideDown(1000);
}

function setPhotoboothsFilters() {
    var owner = $("#ownerFilter").val();
    var boothType = $("#typeFilter").val();
    var datos = {"owner": owner, "boothType": boothType};
    var url = "../ajax/setFilters.php";
    $.ajax({
        url: url,
        type: "POST",
        success: function(data) {
            $("#photoboothsFilters").html(data);
        },
        error: function() {
            errores("I have a error , please try again");
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function filtersProblems() {
    var owner = $("#ownerFilter").val();
    var boothType = $("#typeFilter").val();
    var photobooth = $("#photoboothsFilters").val();
    var date1 = $("#date1").val();
    var date2 = $("#date2").val();
    var datos = {"owner": owner, "boothType": boothType, "booth": photobooth, "dateS": date1, "dateE": date2, "filtre": 1};
    var url = "../ajax/solvedList.php";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {
            goodBye();
        },
        success: function(data) {

            $("#solvedList").html(data);
        },
        error: function() {
            alert("I have a error");
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });

    url = "../ajax/unsolvedList.php";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {
            goodBye();
        },
        success: function(data) {
            $("#unsolvedList").html(data);
        },
        error: function() {
            alert("I have a error");
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });
}
function getFiltersQuestions() {
    var url = "../ajax/templateQuestionsFilters.php";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {
            goodBye();
        },
        success: function(data) {
            $("#barralateralContentF").html(data);
        },
        error: function() {
            alert("I have a error");
        },
        contentType: 'application/x-www-form-urlencoded'
    });
}
function filtersQuestions() {
    var codeQ = $("#questionCode").val();
    var questionF = $("#questionFilter").val();
    var answerF = $("#answerFilter").val();
    var nOfAnswers = $("#answersNumeberF").val();
    var datos = {"questionF": questionF, "answerF": answerF, "nOfAnswers": nOfAnswers, "filtros": 0, "code": codeQ};
    var url = "../ajax/getAllQuestions.php";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {
            $("#load").slideUp(500);
            $("#errores").hide();
        },
        success: function(data) {
            $("#lista").html(data);
            changes();
        },
        error: function() {
            errores("I have a error , please try again");
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });
}
function filterSolutions() {
    var solutionF = $("#solutionFilter").val();
    var codeS = $("#solutionCode").val();
    var datos = {"nextQuestion": solutionFilterNextQuestion, "solutionF": solutionF, "filtros": 0, "code": codeS};
    var url = "../ajax/getAllSolutions.php";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {
            $("#load").slideUp(500);
            $("#errores").hide();
        },
        success: function(data) {
            $("#lista").html(data);
            changes();
        },
        error: function() {
            errores("I have a error , please try again");
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });
}
function getFilterSolutions() {
    var url = "../ajax/templateSolutionsFilters.php";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {
            goodBye();
        },
        success: function(data) {
            $("#barralateralContentF").html(data);
            changes();
        },
        error: function() {
            alert("I have a error");
        },
        contentType: 'application/x-www-form-urlencoded'
    });
}
function showVideo() {
    if ($(".mediaV").height() > 100) {
        $("#arrowVideo").attr("src", "../images/down.png");
        $(".mediaV").animate({
            height: "55px"

        }, 2000);

    } else {
        $(".mediaV").animate({
            height: "250px"

        }, 2000);
        $("#arrowVideo").attr("src", "../images/up.png");
    }




}
function showImage() {
    if ($(".mediaI").height() > 100) {
        $("#arrowImage").attr("src", "../images/down.png");
        $(".mediaI").animate({
            height: "55px"

        }, 2000);
    } else {
        $(".mediaI").animate({
            height: "80%"

        }, 2000);
        $("#arrowImage").attr("src", "../images/up.png");
    }




}
function bigImage(qs, id) {
    var datos = {qs: qs, id: id};
    var url = "../ajax/getImages.php";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {

        },
        success: function(data) {
            $("#IMGPop").html(data);
            $("#IMGPop").fadeIn(1500);
            $(".popUp").fadeIn(1500);
            setTimeout(function() {
                $("#myGallery").galleryView();
            }, 2000);


            popupStatus = 1;
        },
        error: function() {
            // $("#error").html("Connection error , please try again later.")
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });

}
function bigImage2(r) {

    $("#IMGPop").attr("src", r);
    $("#IMGPop").fadeIn(1500);
    $(".popUp").fadeIn(1500);


}
function popupVideo(r) {
    $("#MP4").attr("src", r);
    $("#MP4").fadeIn(1500);
    $(".popUp").fadeIn(1500);
    popupStatus = 1;
}
function disablePopup() {
    $("#IMGPop").fadeOut(1500);
    $("#MP4").fadeOut(1500);
    $(".popUp").fadeOut(1500);

    popupStatus = 0;
}
function login() {

    var user = $("#user").val();
    var pass = $("#pswd").val();
    var datos = {pswd: pass, username: user};
    var url = "ajax/login.php";
    $.ajax({
        url: url,
        type: "POST",
        beforeSend: function() {

        },
        success: function(data) {
            if (data == "INCORRECT LOGIN") {
                $("#error").html(data);
            } else {
                window.top.location.href = data;
            }
        },
        error: function() {
            $("#error").html("Connection error , please try again later.")
        },
        data: datos,
        contentType: 'application/x-www-form-urlencoded'
    });

}
function validarEmail(email) {
    var expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!expr.test(email)) {
        return true;
    } else {
        return false;
    }
}
function tryAgain() {
    var url = "../ajax/tryAgain.php";
    var datos = {};
    var to = "../php/newQuestionari.php";
    defaultAjax(datos, url, to);
}
function deleteMedia(span, media) {
    if (confirm("Vols eliminar-la de veritat ? ") == true) {
        var datos = {MID: media};
        var url = "../ajax/deleteMedia.php";
        $.ajax({
            url: url,
            type: "POST",
            beforeSend: function() {

            },
            success: function(data) {
                $(span).parent().parent().remove();
            },
            error: function() {
                $("#error").html("Connection error , please try again later.")
            },
            data: datos,
            contentType: 'application/x-www-form-urlencoded'
        });



    }

}
function showQuestion() {
    $(".questionText").fadeIn(200);
    $(".responses").fadeIn(200);
    $(".instrucctions p").fadeOut(100);
    $(".instrucctions ol").fadeOut(100);
    $(".nextInst").hide();
   $(".instrucctions").toggleClass("instrucctions2" , 1000);
    $(".showIns").show();
}
function showInstructtions() {
  
    $(".questionText").fadeOut(200);
    $(".responses").fadeOut(200);
    $(".instrucctions p").fadeIn(100);
    $(".instrucctions ol").fadeIn(100);
    $(".instrucctions").removeClass("instrucctions2");
    $(".showIns").hide();    
    $(".nextInst").show();
}