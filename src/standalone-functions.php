<?php
require_once('classes/Database.php');
switch(true)
{
    case isset($_POST['getFolderListJSON']) : 
                                        require_once('classes/StandaloneFile.php');
                                        $standaloneFile = new StandaloneFile();
                                        $standaloneFile->getFolderListJSON($_POST['getFolderListJSON']);
    break;
}
?>