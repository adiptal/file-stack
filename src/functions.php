<?php
require_once('classes/Database.php');
if(isset($_SESSION['user_email']))
{
    switch(true)
    {
            case isset($_POST['getUserListJSON']) : 
                                                require_once('classes/File.php');
                                                $file = new File();
                                                $file->getUserListJSON();
            break;
            
            case isset($_POST['create_folder']) : 
                                                require_once('classes/File.php');
                                                $file = new File();
                                                $file->createFolder($_POST['create_folder']);
            break;

            case isset($_POST['upload_file_submit']) : 
                                                require_once('classes/File.php');
                                                $file = new File();
                                                $file->uploadFile();
            break;

            case isset($_POST['upload_folder_submit']) : 
                                                require_once('classes/File.php');
                                                $file = new File();
                                                $file->uploadFolder();
            break;

            case isset($_POST['getFolderListJSON']) : 
                                                if( $_POST['reset'] )
                                                {
                                                    $_SESSION['current_directory'] = '';
                                                }
                                                require_once('classes/File.php');
                                                $file = new File();
                                                $file->getFolderListJSON($_POST['getFolderListJSON']);
            break;

            case isset($_POST['getFileInformationJSON']) : 
                                                require_once('classes/File.php');
                                                $file = new File();
                                                $file->getFileInformationJSON($_POST['getFileInformationJSON']);
            break;

            case isset($_POST['shareFile']) : 
                                                require_once('classes/File.php');
                                                $file = new File();
                                                $file->shareFile($_POST['shareFile'] , $_POST['user_id_list']);
            break;

            case isset($_POST['deleteFile']) : 
                                                require_once('classes/File.php');
                                                $file = new File();
                                                $file->deleteFile($_POST['deleteFile']);
            break;

            case isset($_GET['downloadFile']) : 
                                                require_once('classes/File.php');
                                                $file = new File();
                                                $file->downloadFile($_GET['downloadFile']);
            break;
    }
}
else
{
    $_SESSION['error'] = "<script>toastr.error('Email Directory Invalid', 'My Drive');</script>";
}
?>