<?php
    require_once('classes/Database.php');
    if( !isset($_SESSION['user_id']) )
    {
        header('Location: http://' . substr( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] , 0 , strrpos( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] , '/' ) ) );
    }
    if( !isset( $_SESSION['shared_current_directory'] ) )
    {
        $_SESSION['shared_current_directory'] = '';
    }
?>
<!DOCTYPE html>
<html lang="en">
<?php
    require_once('includes/header.php');
?>
<body>
<?php
    require_once('includes/top-nav.php');
    require_once('includes/side-nav.php');
    require_once('includes/scripts.php');
    require_once('includes/shared-content.php');
?>
</body>
</html>