<?php
    require_once('../classes/Database.php');
    if( isset($_SESSION['user_id']) )
    {
        header('Location: http://localhost/filestack/');
    }
?>
<!DOCTYPE html>
<html lang="en"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FileStack</title>
    
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="../assets/toastr/build/toastr.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-10 offset-1 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-4 offset-lg-4 card-box">
                <div class="card-body" id="form">
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/jquery.min.js"></script>
    <script src="assets/js/cookie.js"></script>
<?php
    $getFromURL = array('signup' , 'signin' , 'forgot');
    if(isset($file) && $file == 'resetPage')
    {
        echo "<script>Cookies.set('form', 'reset');</script>";
    }
    elseif(isset($_GET['page']) && $_GET['page'] != '' && in_array($_GET['page'] , $getFromURL))
    {
        echo "<script>
                Cookies.set('form', '". $_GET['page'] ."');
            </script>";
    }
    else
    {
        echo "<script>
            if(Cookies.get('form') == '')
                Cookies.set('form', 'signin');
            </script>";
    }
?>
    <script>var baseUrl = 'http://localhost/filestack';</script>
    <script src="assets/ajax/pages.js"></script> 
    <script src="assets/ajax/functionality.js"></script>

<?php
    // ********************** Showing Error Through Session and Unsetting Session ********************************//
    if( isset($_SESSION['error']) && $_SESSION['error'] != '' )
    {
        echo '<link rel="stylesheet" href="../assets/toastr/build/toastr.min.css">
        <script src="../assets/toastr/build/toastr.min.js"></script>
        '.$_SESSION['error'];
        $_SESSION['error'] = '';
    }
    // ***********************************************************************************************************//
?>
</body>
</html>