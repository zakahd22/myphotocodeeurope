/* Conexio amb Facebook*/
var FB;
var accessToken;
var uid;
var faceBtnLog_iframe_src;

window.fbAsyncInit = function() {
    FB.init({
        appId: '127533357397300',
        cookie: true,
        xfbml: true, 
        version: 'v2.5'
    });

    FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
    }); 
};

function statusChangeCallback(response) {     
    if (response.status === 'connected') {
        uid = response.authResponse.userID;
        accessToken = response.authResponse.accessToken;
        showFB_iframe();
    } else {
        //not connected, hidde default
        hideFB_iframe();
    }
}

function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
}

(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


function post(url, message, type, accessToken, album, photo_url, callback) {
    switch (type){
        case "video":
            postVideo(url, message, accessToken, callback);
            
            break;
        case "photo":
        default:
            if(album == 1){
                postAlbum(url, message, accessToken, photo_url, callback);
            }else{
                postImage(url, message, accessToken, callback);
            }
            break;
    }
}

function postVideo(video_url, message, accessToken, callback) {
    var params = {};
    params['description'] = message;
    params['title'] = "aleixxxx";
    params['file_url'] = video_url;
    params['access_token'] = accessToken;
    
    FB.api('/me/videos', 'post', params, function (response) {
       callback(response);
    });
}

function postImage(photo_url, message, accessToken, callback) {
   
    var params = {};
    params['message'] = message;
    params['url'] = photo_url;
    params['access_token'] = accessToken;
    
    FB.api('/me/photos', 'post', params, function (response) {
       callback(response);
    });
}

function postAlbum(name, message, accessToken, photo_url, callback){
//    alert('/events'+name[0]);
    var params = {};
    params['message'] = message;
    params['url'] = 'https://www.myphotocode.com/events'+name[0];
    params['access_token'] = accessToken;
    
    FB.api('/me/albums', 'post', params, function (response) {
       callback(response);
    });
}
//https://www.myphotocode.com/events/201401017159/ADCA1234AB.jpg
function showName(callback) {    
    FB.api('/me', function (response, face_response) {
        console.log('Successful login for: ' + response.name);
        callback(response.name);
    });
}


function establishFacebookConect(url, type, hashtags, callback, album, photo_url){ 
    var alertText = '<br /><br />Do you want to post some text with your ' + type + '?';
    
    FB.login(function(response){
        console.log(response);
        if(response.status == 'connected'){
            var accessToken = response.authResponse.accessToken;
            publicationconstruct(response, hashtags, alertText, url, type, accessToken, callback, album, photo_url);
            showFB_iframe();
            updateStatus();
        }
    }, {
        scope: 'publish_actions', 
        return_scopes: true
    });
}

function publicationconstruct(response, hashtags, alertText, url, filetype, accessToken, callback, album, photo_url){
    showName(function(name){
        var welcome = "";
        var hashtext = "";

        if(name !== "") welcome = "Hello <b>" + name + "!</b>";
        if(hashtags != "") hashtext = "<br /><b>Hashtags</b> that will be posted: <br />" + hashtags;

        swal({
            html: welcome + hashtext + alertText,
            input: 'text',
            type: 'info',
            showCancelButton: true,
            confirmButtonText: 'Share',
            showLoaderOnConfirm: true,
            preConfirm: function(message) {
                return new Promise(function(resolve, reject) {
                    if (message === '') {
                        reject('This message is empty.');
                    }
                    else{
                        message += " " + hashtags;
                        post(url, message, filetype, accessToken, album, photo_url, function(response){
                            if(response.error !== undefined){
                                reject('Error 0' + response.error.code + ': ' + response.error.message);
                            }
                            resolve();
                        });
                    }
                });
            },
            allowOutsideClick: false
        }).then(function(message) {
            callback();

            if(message === null) message = "--";
            swal({
                type: 'success',
                title: 'Upload successful',
                html: "Submitted message: " + message + "<br /><br /> <div style='background-color:rgb(241,241,241);padding-top:8px;padding-bottom:8px;'><span style='color: #ea7d7d;'><i class='fa fa-exclamation-circle fa-lg' aria-hidden='true'></i> <!-- <b>Alert!</b><br /> --></span> You are logged in facebook as <span style='font-weight: bold;'>" + name + "</span></div><br />Do you want to Log Out?",
                showCancelButton: true,
                confirmButtonText: "<i class='fa fa-facebook-official fa-lg' style='color:white;' aria-hidden='true'></i>&nbsp &nbsp Log Out",
                confirmButtonColor: '#3b5998',
            }).then(function(message) {
                logoutFB();
            });
        });
    }); 
}

function updateStatus(){
    $('#faceBtnLog iframe').attr('src', faceBtnLog_iframe_src);
}

function logoutFB(){
    FB.logout(function(response) {
            // user is now logged out
    });
    hideFB_iframe();
}

function hideFB_iframe(){
    $("#content_face").hide();
}

function showFB_iframe(){
    $("#content_face").show();
    faceBtnLog_iframe_src = $('#faceBtnLog iframe').attr('src');
}

function loginAction(){
    checkLoginState();
}