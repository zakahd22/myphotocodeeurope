var accessToken = "";

function setUserAccesToken(){
    FB.api('/me', function(response) {
        var object = {
            token: $("#token").val(),
            name: response.name,
            access_token: accessToken
        };
        var data = JSON.stringify(object);        
        $.ajax({

            url: '/API/facebook/smartprint/face_login.php',
            type: 'POST',
            dataType: 'json',
            data: data,
            complete: function(e, xhr, settings){
                if(e.status === 200){
                    $('#pestanya_mssg').hide();
                    $('#fb-login-button').hide();
                    //treure quan tinguem premisos facebook a user_photos
                   
                    if($("#token").val().toUpperCase()=="QILHZF"){
                        //console.log('Hello Meta:' + $("#token").val());
                        var hostName = window.location.hostname;
                        var protocol = window.location.protocol;
                        var finalUrl = protocol + '//' + hostName;
                        
                        $("#fb_token").load(finalUrl + "/API/facebook/smartprint/download_photos_test.php?token=QILHZF");
                        //$('#fb_token').html('<iframe id="form" src="https://myphotocode.com/API/facebook/smartprint/download_photos_test.php?token=QILHZF"></iframe>');
                        
                    }else{
                        //console.log('Hello Meta' + $("#token").val());
                        $('#fb_token').html("<p>In short, you will see your photos in the PhotoBooth!</p>");
                    }
                    
                } else{
                    $('#mssg').html("<p>ERROR - INCORRECT CODE <img src='../../images/web/facebook_error.png'></p>");
                    $('#pestanya_mssg').show();
                }
            }
        });
   });
};


// This function is called when someone finishes with the Login
// Button.  See the onlogin handler attached to it in the sample
// code below.
function checkLoginState() {
  FB.getLoginStatus(function(response) {
      accessToken =   FB.getAuthResponse()['accessToken'];
      if (accessToken != null) {
          setUserAccesToken();
      }
  });
}

window.fbAsyncInit = function() {
  FB.init({
    appId      : '127533357397300',
    cookie     : true,  // enable cookies to allow the server to access 
                        // the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v4.0', // The Graph API version to use for the call
    status     : true
  });

  // Now that we've initialized the JavaScript SDK, we call 
  // FB.getLoginStatus().  This function gets the state of the
  // person visiting this page and can return one of three states to
  // the callback you provide.  They can be:
  //
  // 1. Logged into your app ('connected')
  // 2. Logged into Facebook, but not your app ('not_authorized')
  // 3. Not logged into Facebook and can't tell if they are logged into
  //    your app or not.
  //
  // These three cases are handled in the callback function.

  FB.getLoginStatus(function(response) {
    console.log(response);
    var access_token = FB.getAuthResponse()['accessToken'];
  });

};

// Load the SDK asynchronously
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


