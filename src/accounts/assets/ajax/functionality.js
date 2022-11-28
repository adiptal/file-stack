function signIn(){
    $(function(){
        if($('#user_email').val() == '' || $('#user_password').val() == '' )
        {
            $('#label_email , #label_password').css('color' , 'red').css('border-bottom' , 'solid 1px red').focus();
            $('.form-control').css('color' , 'red').css('border' , 'solid 1px red');
            $('#error').html('<i class="fas fa-times"></i> Fill all Credentials').stop(true, true).fadeIn().css("display" , "block").delay(5000).fadeOut();
        }
        else
        {
            var user_email = $('#user_email').val();
            var user_password = $('#user_password').val()
            var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            
            if(regex.test(user_email))
            {
                $('#form').stop(true, true).fadeIn();
                $('#form').html('<div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>');
                $.ajax({
                    type: 'POST',
                    url: baseUrl+'/accounts/functions.php',
                    data: 'signIn&user_email=' + user_email + '&user_password=' + user_password
                }).done(function (response) {
                    location.reload();
                });
            }
            else
            {
                $('.form-control').css({ color: 'red', borderColor: 'red' });
                $('#label_email').css('color' , 'red').css('border-bottom' , 'solid 1px red').focus();
                $('.password').css('color' , 'black').css('border' , 'solid 1px black');
                $('.email').css('color' , 'red').css('border' , 'solid 1px red');
                $('#label_password').css('color' , 'black').css('border-bottom' , 'solid 1px black');
                $('#error').html('<i class="fas fa-times"></i> Invalid Email Format').stop(true, true).fadeIn().css("display" , "block").delay(5000).fadeOut();
                $('.form-input').val('');
            }
        }
    });
}

function signUp(){
    $(function(){
        if($('#user_first_name').val() == '' || $('#user_last_name').val() == '' || $('#user_email').val() == '' || $('#user_password').val() == '' || $('#user_password_verify').val() == '' )
        {
            $('#label_first_name , #label_last_name , #label_email , #label_password , #label_password_verify').css('color' , 'red').css('border-bottom' , 'solid 1px red').focus();
            $('.form-control').css('color' , 'red').css('border' , 'solid 1px red');
            $('#error').html('<i class="fas fa-times"></i> Fill all Credentials').stop(true, true).fadeIn().css("display" , "block").delay(5000).fadeOut();
        }
        else
        {
            var user_first_name = $('#user_first_name').val();
            var user_last_name = $('#user_last_name').val();
            var user_email = $('#user_email').val();
            var user_password = $('#user_password').val()
            var user_password_verify = $('#user_password_verify').val();

            var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            
            if(regex.test(user_email))
            {
                if( user_password === user_password_verify)
                {
                    $('#form').stop(true, true).fadeIn();
                    $('#form').html('<div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>');
                    $.ajax({
                        type: 'POST',
                        url: baseUrl+'/accounts/functions.php',
                        data: 'signUp&user_first_name='+ user_first_name + '&user_last_name=' + user_last_name + '&user_email=' + user_email + '&user_password=' + user_password
                    }).done(function (response) {
                        location.reload();
                    });
                }
                else
                {
                    $('.form-control').css({ color: 'red', borderColor: 'red' });
                    $('#label_password , #label_password_verify').css('color' , 'red').css('border-bottom' , 'solid 1px red').focus();
                    $('.password , .password_verify').css('color' , 'red').css('border' , 'solid 1px red');

                    $('.first_name , .last_name , .email').css('color' , 'black').css('border' , 'solid 1px black');
                    $('#label_first_name , #label_last_name , #label_email').css('color' , 'black').css('border-bottom' , 'solid 1px black');
                    
                    $('#error').html('<i class="fas fa-times"></i> Invalid Email Format').stop(true, true).fadeIn().css("display" , "block").delay(5000).fadeOut();
                    $('#user_password').val('');
                    $('#user_password_verify').val('');
                }
            }
            else
            {
                $('.form-control').css({ color: 'red', borderColor: 'red' });
                $('#label_email').css('color' , 'red').css('border-bottom' , 'solid 1px red').focus();
                $('.email').css('color' , 'red').css('border' , 'solid 1px red');

                $('.first_name , .last_name , .password , .password_verify').css('color' , 'black').css('border' , 'solid 1px black');
                $('#label_first_name , #label_last_name , #label_password , #label_password_verify').css('color' , 'black').css('border-bottom' , 'solid 1px black');

                $('#error').html('<i class="fas fa-times"></i> Invalid Email Format').stop(true, true).fadeIn().css("display" , "block").delay(5000).fadeOut();
                $('#user_email').val('');
            }
        }
    });
}

function forgot(){
    $(function(){
        if($('#user_email').val() == '')
        {
            $('#label_email').css('color' , 'red').css('border-bottom' , 'solid 1px red').focus();
            $('.form-control').css({ color: 'red', borderColor: 'red' });
            $('#error').html('<i class="fas fa-times"></i> Fill all Credentials').stop(true, true).fadeIn().css("display" , "block").delay(5000).fadeOut();
        }
        else
        {
            var user_email = $('#user_email').val();
            var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            
            if(regex.test(user_email))
            {
                $('#form').stop(true, true).fadeIn();
                $('#form').html('<div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>');
                $.ajax({
                    type: 'POST',
                    url: baseUrl+'/accounts/forgot/'+user_email
                }).done(function (response) {
                    // location.reload();
                    $('.card').html(response);
                });
            }
            else
            {
                $('#label_email').css('color' , 'red').css('border-bottom' , 'solid 1px red').focus();
                $('.form-control').css({ color: 'red', borderColor: 'red' });
                $('#error').html('<i class="fas fa-times"></i> Invalid Email Format').stop(true, true).fadeIn().css("display" , "block").delay(5000).fadeOut();
                $('.form-input').val('');
            }
        }
    });
}

function resendForgotEmail(user_email){
    $(function(){
        $('#form').stop(true, true).fadeIn();
        $('#form').html('<div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>');
        $.ajax({
            type: 'POST',
            url: baseUrl+'/accounts/resendForgotEmail/'+user_email
        }).done(function (response) {
            // location.reload();
            $('.card').html(response);
        });
    });
}

function resetPassword(){
    $(function(){
        var user_password = $('#user_password').val();
        var user_confirm = $('#user_confirm').val();
        
        if( user_password == '' || user_confirm == '' )
        {
            $('#label_password , #label_confirm').css('color' , 'red').css('border-bottom' , 'solid 1px red').focus();
            $('.form-control').css({ color: 'red', borderColor: 'red' });
            $('#error').html('<i class="fas fa-times"></i> Fill all Credentials').stop(true, true).fadeIn().css("display" , "block").delay(5000).fadeOut();
        }
        else if( user_password.length < 8 || user_confirm.length < 8 )
        {
            $('#label_password , #label_confirm').css('color' , 'red').css('border-bottom' , 'solid 1px red').focus();
            $('.form-control').css({ color: 'red', borderColor: 'red' });
            $('#error').html('<i class="fas fa-times"></i> Atleast 8 Credentials').stop(true, true).fadeIn().css("display" , "block").delay(5000).fadeOut();
        }
        else if( user_password != user_confirm )
        {
            $('#label_password , #label_confirm').css('color' , 'red').css('border-bottom' , 'solid 1px red').focus();
            $('.form-control').css({ color: 'red', borderColor: 'red' });
            $('#error').html('<i class="fas fa-times"></i> Password not Match').stop(true, true).fadeIn().css("display" , "block").delay(5000).fadeOut();
        }
        else
        {
            var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}/;

            if(regex.test(user_password))
            {
                $('#form').stop(true, true).fadeIn();
                $('#form').html('<div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>');                $.ajax({
                    type: 'POST',
                    url: baseUrl+'/accounts/resetPassword/'+user_password
                }).done(function (response) {
                    // location.reload();
                    $('.card').html(response);
                });
            }
            else
            {
                $('#label_password , #label_confirm').css('color' , 'red').css('border-bottom' , 'solid 1px red').focus();
                $('.form-control').css({ color: 'red', borderColor: 'red' });
                $('#error').html('<i class="fas fa-times"></i> Password Should Contain :<br/>1 uppercase letter<br/> 1 lowercase letter<br/> 1 number').stop(true, true).fadeIn().css("display" , "block").delay(5000).fadeOut();
            }
        }
    });
}