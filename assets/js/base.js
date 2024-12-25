///* Conexio amb Facebook*/
//var FB;
//var accessToken;
//
//// Load the SDK asynchronously
//(function (d, s, id) {
//    var js, fjs = d.getElementsByTagName(s)[0];
//    if (d.getElementById(id)) return;
//    js = d.createElement(s); js.id = id;
//    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6&appId=127533357397300";
//    fjs.parentNode.insertBefore(js, fjs);
//}(document, 'script', 'facebook-jssdk'));
//
//
//function statusChangeCallback(response) {
//    console.log('statusChangeCallback');
//    console.log(response);
//    if (response.status === 'connected') {
//      testAPI();
//    } else if (response.status === 'not_authorized') {
//      document.getElementById('status').innerHTML = 'Please log ' +
//        'into this app.';
//    } else {
//      document.getElementById('status').innerHTML = 'Please log ' +
//        'into Facebook.';
//    }
//}
//        
//function post(url, message, type, accessToken, callback) {
//    switch (type){
//        case "video":
//            postVideo(url, message, accessToken, callback);
//            
//            break;
//        case "photo":
//        default:
//            postImage(url, message, accessToken, callback);
//            break;
//    }
//}
//
//function postVideo(video_url, message, accessToken, callback) {
//    var params = {};
//    params['description'] = message;
//    params['title'] = "aleixxxx";
//    params['file_url'] = video_url;
//    params['access_token'] = accessToken;
//    
//    console.log(params);
//    
//    FB.api('/me/videos', 'post', params, function (response) {
//       callback(response);
//    });
//}
//
//function postImage(photo_url, message, accessToken, callback) {
//    var params = {};
//    params['message'] = message;
//    params['url'] = photo_url;
//    params['access_token'] = accessToken;
//    
//    console.log(params);
//    
//    FB.api('/me/photos', 'post', params, function (response) {
//       callback(response);
//    });
//}
//
//function checkLoginState() {
//  FB.getLoginStatus(function(response) {
//    statusChangeCallback(response);
//  });
//}
//
//function showName(callback) {    
//    FB.api('/me', function (response, face_response) {
//        console.log('Successful login for: ' + response.name);
//        callback(response.name);
//    });
//}
//
//function establishFacebookConect(url, type, hashtags, callback){    
//    
//    window.fbAsyncInit = function () {
//        FB.init({
//            appId: '127533357397300',
//            cookie: true,
//            xfbml: true, 
//            version: 'v2.5'
//        });
//    };
//    var alertText = '<br /><br />Do you want to post some text with your ' + type + '?';
//    
//    FB.login(function(response){
//        console.log(response);
//        if(response.status == 'connected'){
//            var accessToken = response.authResponse.accessToken;
//            publicationconstruct(response, hashtags, alertText, url, type, accessToken, callback);
//        }
//    }, {
//        scope: 'publish_actions', 
//        return_scopes: true
//    });
//}
//
//
//function publicationconstruct(response, hashtags, alertText, url, filetype, accessToken, callback){
//    showName(function(name){
//    var welcome = "";
//    var hashtext = "";
//
//    if(name !== "") welcome = "Hello <b>" + name + "!</b>";
//    if(hashtags != "") hashtext = "<br /><b>Hashtags</b> that will be posted: <br />" + hashtags;
//
//    swal({
//    html: welcome + hashtext + alertText,
//    input: 'text',
//    type: 'info',
//    showCancelButton: true,
//    confirmButtonText: 'Share',
//    showLoaderOnConfirm: true,
//    preConfirm: function(message) {
//        return new Promise(function(resolve, reject) {
//            if (message === '') {
//                reject('This message is empty.');
//            }
//            else{
//                message += " " + hashtags;
//                post(url, message, filetype, accessToken, function(response){
//                    if(response.error !== undefined){
//                        reject('Error 0' + response.error.code + ': ' + response.error.message);
//                    }
//                    resolve();
//                });
//            }
//        });
//    },
//    allowOutsideClick: false
//    }).then(function(message) {
//        callback();
//        
//        if(message === null) message = "--";
//        swal({
//          type: 'success',
//          title: 'Upload successful',
//          html: 'Submitted message: ' + message
//        });
//    });
//    }); 
//}
//
///* ----- end ------*/