<?php
    require_once('classes/Database.php');
    if( !isset( $_SESSION['current_directory'] ) )
    {
        $_SESSION['current_directory'] = '';
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
    if( isset( $_SESSION['user_id'] ) )
    {
        require_once('includes/main-content.php');
    }
    else
    {
        require_once('includes/static-content.php');
    }
?>
</body>
</html>