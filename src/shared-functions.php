<?php
require_once('classes/Database.php');
if(isset($_SESSION['user_email']))
{
    switch(true)
    {
            case isset($_POST['openDirectory']) :
                                                require_once('classes/SharedFile.php');
                                                $file = new SharedFile();
                                                $file->openDirectory($_POST['openDirectory']);
            break;

            case isset($_POST['getFileInformationJSON']) : 
                                                require_once('classes/SharedFile.php');
                                                $file = new SharedFile();
                                                $file->getFileInformationJSON($_POST['user_file_name'] , $_POST['user_file_location']);
            break;

            case isset($_POST['deleteFile']) : 
                                                require_once('classes/SharedFile.php');
                                                $file = new SharedFile();
                                                $file->deleteFile($_POST['deleteFile']);
            break;

            case isset($_GET['downloadFile']) : 
                                                require_once('classes/SharedFile.php');
                                                $file = new SharedFile();
                                                $file->downloadFile($_GET['downloadFile']);
            break;
    }
}
else
{
    $_SESSION['error'] = "<script>toastr.error('Email Directory Invalid', 'Shared With Me');</script>";
}
?>