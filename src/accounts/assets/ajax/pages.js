$(function(){
    $('#form').show();
    $('#form').html('<div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>');

    switch(Cookies.get('form'))
    {
        case ''   : ; // Reset Page
        break;

        case 'reset'   : resetPage();
        break;

        case 'signin'   : signInPage();
        break;
        
        case 'forgot'   :   forgotPage();
        break;
        
        case 'signup'   :   signUpPage();
        break;

        default :   signInPage();
    }
    setTimeout(function(){
        $.getScript("https://use.fontawesome.com/releases/v5.0.13/js/all.js");
    },2500);
});

function signInPage(){
    $('#form').hide();
    $.ajax({
        url: baseUrl+'/accounts/forms/signin.php'
    }).done(function (response) {    
        $(function() {
            $("#form").html(response);
            $('#form').show();
            Cookies.set('form', 'signin');
        });
    })
}

function signUpPage(){
    $('#form').hide();
    $.ajax({
        url: baseUrl+'/accounts/forms/signup.php'
    }).done(function (response) {    
        $(function() {
            $("#form").html(response);
            $('#form').show();
            Cookies.set('form', 'signup');
        });
    })
}

function forgotPage(){
    $('#form').hide();
    $.ajax({
        url: baseUrl+'/accounts/forms/forgot.php'
    }).done(function (response) {    
        $(function() {
            $("#form").html(response);
            $('#form').show();
            Cookies.set('form', 'forgot');
        });
    })
}

function resetPage(){
    Cookies.set('form', '');
    $('#form').hide();
    $.ajax({
        url: baseUrl+'/accounts/forms/reset.php'
    }).done(function (response) {    
        $(function() {
            $("#form").html(response);
            $('#form').show();
        });
    })
}