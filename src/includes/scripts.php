<script>var baseUrl = 'http://localhost/filestack';</script>

<link rel="stylesheet" href="assets/css/jquery.contextMenu.min.css">
<link rel="stylesheet" href="assets/select2/select2.min.css" />
<script src="assets/js/jquery.min.js"></script>
<script>
    setTimeout(function(){
        $.when(
        $.getScript( "assets/js/jquery.contextMenu.min.js" ),
        $.getScript( "assets/js/jquery.ui.position.js" ),
        $.getScript( "assets/js/popper.js" ),
        $.getScript( "assets/js/bootstrap.min.js" ),
        $.getScript( "assets/toastr/build/toastr.min.js" ),
        $.getScript( "assets/js/jszip.min.js" ),
        $.getScript( "assets/select2/select2.min.js" ),
        $.getScript( "assets/scripts.js" ),
        $.Deferred(function( deferred ){
            $( deferred.resolve );
        })
    ).done(function(){
<?php
    // --------------- DISPLAY ERRORS IF ANY --------------- //
    if(isset($_SESSION['error']))
    {
        echo $_SESSION['error'];
        $_SESSION['error'] = '';
    }
    // ---------- X ---------- X ---------- X  ---------- //
?>
    });
    } , 1000);
</script>

<script>
    function loadPageViewAjax( event , page_name , keyword )
    {
        event.preventDefault();
        window.history.replaceState(null, null, baseUrl +"/" + page_name);
        $('.container-box').remove();
        $('.context-menu-list').css({'display' : 'none'});

        $.ajax({
            type: 'POST',
            url: baseUrl +'/includes/' + keyword
        }).done(function (response) {
            $('body').append(response);
            if(window.innerWidth > 1199)
            {
                $('.container-box').stop().css({
                    'width' : $('body').width() - $('.sidebar').width()
                });
            }
            else
            {
                toggleSidebar();
            }
        });
    }

    function toggleSidebar()
    {
        $('.sidebar').toggleClass('show');
        
        if(window.innerWidth > 1199)
        {
            if( $('.sidebar').position().left < 0 )
            {
                $('.container-box').stop().animate({
                    'width' : $('body').width() - $('.sidebar').width()
                } , 250);
            }
            else
            {
                $('.container-box').stop().animate({
                    'width' : $('body').width()
                } , 250);
            }
        }
        $('.overlayer').toggle();
        $('.main-box').mousedown(function(){
            $('.sidebar').removeClass('show');
            $('.overlayer').hide();
        });
    }

    function toggleDropDown( posX , id )
    {
        $(id).toggle().css({
            'left' : posX - '135'
        });
        $('body').mouseup(function(){
            $(id).hide();
        });
    }


    // -------------------------------------------------------------------------------- //
                            // USER LIST OPTIONS IN SHARE FILE
    // -------------------------------------------------------------------------------- //

    function getUserListJSON()
    {
        $.ajax({
            method: "POST",
            url: baseUrl + "/functions.php",
            data: 'getUserListJSON',
            dataType: "json"
        }).done(function(response) {
            $('#user_id_list').html('');
            if ( !jQuery.isEmptyObject(response) )
            {
                for(var i=0 ; i< response.length ; i++)
                {
                    $('#user_id_list').append(response[i]);
                }
            }

        });
    }
    
    // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //



    // -------------------------------------------------------------------------------- //
                                    // SIGNIN
    // -------------------------------------------------------------------------------- //

    function signIn()
    {
        if($('#user_email').val() != '' && $('#user_password').val() != '' )
        {
            var user_email = $('#user_email').val();
            var user_password = $('#user_password').val()
            var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            
            if(regex.test(user_email))
            {
                $.ajax({
                    type: 'POST',
                    url: baseUrl + '/accounts/functions.php',
                    data: 'signIn&user_email=' + user_email + '&user_password=' + user_password
                }).done(function (response) {
                    location.reload();
                });
            }
        }
    }
    
    // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //



    // -------------------------------------------------------------------------------- //
                                    // SIGNOUT
    // -------------------------------------------------------------------------------- //

    $('#signout').click(function(){
        $.ajax({
            method: "POST",
            url: baseUrl + '/accounts/functions.php',
            data: 'signOut'
        }).done(function(response) {
            location.reload();
        });
    });

    // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
</script>