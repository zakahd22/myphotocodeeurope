/***************************/
//@Author: Adrian "yEnS" Mato Gondelle
//@website: www.yensdesign.com
//@email: yensamg@gmail.com
//@license: Feel free to use it, but keep this credits please!					
/***************************/

var popupStatus = 0;

function loadPopup()
{

	if(popupStatus==0)
	{
		
		var windowWidth = $(document).width();
		var windowHeight = $(document).height();
		var popupWaitWidth = $("#popupWait").width();
		var popupWaitHeight = $("#popupWait").height();
		
		$("#backgroundPopup").css({
			"width": "100%",
			"height": windowHeight
		});
		
		$("#backgroundPopup").css({"opacity": "0.9"});
		$("#backgroundPopup").fadeIn({duration:200});
		
		var scrollTop = $(document).scrollTop();
		
		$("#popupWait").css({
			"position": "absolute",
			"top": "50%",
			"left": "50%",
			"margin-left": popupWaitWidth / -2,
			"margin-top": (popupWaitHeight / -2) + scrollTop
		});
		
		$("#popupWait").fadeIn({duration:200});
		
		popupStatus = 1;
		
	}
	
}


function disablePopup()
{

	if(popupStatus==1)
	{
		$("#backgroundPopup").fadeOut({duration:200});
		$("#popup").fadeOut({duration:200});
		popupStatus = 0;
	}
	
}


function centerPopup(){

	var windowWidth = $(document).width();
	var windowHeight = $(document).height();
	var popupWidth = $("#popup").width();
	var popupHeight = $("#popup").height();

	var scrollTop = $(document).scrollTop();

	$("#popup").css({
		"position": "absolute",
		"top": "50%",
		"left": "50%",
		"margin-left": popupWidth / -2,
		"margin-top": (popupHeight / -2) + scrollTop
	});
	
	$("#popupWait").hide();
	$("#popup").fadeIn({duration:200});
	
}


function startPopup(content,id)
{
	
	switch (content)
	{
		case "video" : file = "popup_video.php?id=" + id; break;
		case "email" : file = "popup_email.php?id=" + id; break;
	}

	loadPopup();	
	$("#popup").load("assets/php/templates/" + file, function() {
	  	centerPopup();
	});
	
}


$(document).ready(function(){
	
	$("#backgroundPopup").click(function(){
		//if (popupStatus) disablePopup();
	});

	$(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});

});