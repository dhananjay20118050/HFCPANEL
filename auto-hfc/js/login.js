$(document).ready(function() {
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: 'include/login/login-checker.php',
        success: function(response) {
            if (response.status == 'ERR'){
                $("#login-modal").modal('show');
            }else {
                setLables(response);
                selTab("#t1");
                getBotStatus();
            }
        }
    });
});

$("#popup-login").click(function(event){
    event.preventDefault();
    var user = $('#popup-user').val();
    var pass = $('#popup-password').val();
    $.ajax({
        type: 'post',
        url: 'include/login/login.php',
        dataType: 'json', 
        data:{'user':user,'pass':pass},
        success: function(response) {
          console.log(response);
            if(response.result != ''){
                setUserData(response.result);
            }else{
                toastr["error"]("Invalid Username and Password!");
            }
        }
    });
});

$("#logout").click(function(event){
    event.preventDefault();
    $.ajax({
        type: 'post',
        dataType : 'json',
        url: 'include/login/logout.php',
        success: function(response) {
            window.location.href = response.target;
            cleaner();
            $("#popup-user").focus();
            toastr["success"]('Logged out sucessfully. Login again.');
        }
    });
});

function setUserData(result){
    $.ajax({
        type: 'post',
        dataType :'json',
        data : result,
        url: 'include/login/set-user-data.php',
        success: function(response) {
            setLables(response);
            $("#login-modal").modal('hide');
            selTab("#t1");
            toastr["success"]('Welcome '+userData.fullName);
        }
    });
}

function setLables(response){
    userData = response;
    $('#username-title').append(response.fullName);
}

function cleaner(){
    $('#popup-user').val('');
    $('#popup-password').val('');
    $('#username-title').empty();
    $('#login-modal').modal('show');
}
/* Browser settings */
window.history.forward();
function noBack() {
    window.history.forward();
}
history.pushState(null, null, document.URL);
window.addEventListener('popstate', function () {
    history.pushState(null, null, document.URL);
});

function getBotStatus(){
    $.ajax({
        type: 'post',
        dataType :'json',
        url: 'include/login/get-bot-status.php',
        success: function(data) {
            if(data.status == "error"){
                toastr[data.status](data.msg);
                $("#wait").show();
                $("#wait-loading-img").removeClass("none");
            }
        }
    });
}