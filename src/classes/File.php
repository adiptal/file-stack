<?php
class File
{
    private $connection;
    private $file_location;
    private $false = 0;
    private $true = 1;

    public function __construct()
    {
        global $database;
        $this->connection = $database->getConnection();
        $this->file_location = "./user_datas/" . $_SESSION['user_email'] . '/';
    }

    private function dirToArray( $directory )
    {
        $result = array();
        $result['directory'] = array();
        $result['file'] = array();
        $cdir = scandir( $directory ); 
        foreach ( $cdir as $key => $value ) 
        { 
            if ( !in_array( $value,array( ".",".." ) ) ) 
            { 
                if ( is_dir( $directory . DIRECTORY_SEPARATOR . $value ) ) 
                { 
                    array_push($result['directory'] ,  array( 'key' => $value ) );
                } 
                else 
                { 
                    array_push($result['file'] ,  array( 'key' => $value ) );
                } 
            } 
        } 
        return $result; 
    }
    
    private function checkFileInformationExists( $user_file_name , $user_file_location )
    {
        // ------------------------------------------------------------------------------------------ //
                                // Check File Information Existance in Database
        // ------------------------------------------------------------------------------------------ //
        
        $query = "SELECT * FROM user_file WHERE user_id = ? AND user_file_name LIKE ? AND user_file_location LIKE ?";
        $preparedStatement = $this->connection->prepare( $query );
        $preparedStatement->bind_param( "iss" , $_SESSION['user_id'] , $user_file_name , $user_file_location );
        $preparedStatement->execute();
        $preparedStatement->store_result();
        $count = $preparedStatement->num_rows();
        
        if( $count != 0 )
        {
            return true;
        }
        else
        {
            return false;
        }
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    private function manageFileInformation( $user_file_name , $user_file_location , $user_file_size )
    {
        // ------------------------------------------------------------------------------------------ //
                                // Manage File Information in Database
        // ------------------------------------------------------------------------------------------ //

        $time = date("Y-m-d h:i:sa");
        if( !$this->checkFileInformationExists( $user_file_name , $user_file_location ) )
        {
            // IF FILE INFORMATION NOT EXISTS
            $query = "INSERT INTO user_file ( user_id , user_file_name , user_file_location , user_file_size , created_at , is_deleted ) VALUES ( ? , ? , ? , ? , ? , ? )";
            $preparedStatement = $this->connection->prepare( $query );
            $preparedStatement->bind_param( "issisi" , $_SESSION['user_id'] , $user_file_name , $user_file_location , $user_file_size , $time , $this->false );
            $preparedStatement->execute();
        }
        else
        {
            // IF FILE INFORMATION EXISTS
            $query = "UPDATE user_file SET user_file_size = ? , updated_at = ? , is_deleted = ? WHERE user_id = ? AND user_file_name LIKE ? AND user_file_location LIKE ?";
            $preparedStatement = $this->connection->prepare( $query );
            $preparedStatement->bind_param( "isiiss" , $user_file_size , $time , $this->false , $_SESSION['user_id'] , $user_file_name , $user_file_location );
            $preparedStatement->execute();
        }
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }
    
    private function checkFileShareExists( $user_id , $user_file_name )
    {
        // ------------------------------------------------------------------------------------------ //
                                // Check File Share Information Existance in Database
        // ------------------------------------------------------------------------------------------ //

        $user_file_location = $this->file_location . $_SESSION['current_directory'];

        $query = "SELECT * FROM user_file_share WHERE user_id = ? AND from_user_email = ? AND user_file_name LIKE ? AND user_file_location LIKE ? AND is_deleted = ?";
        $preparedStatement = $this->connection->prepare( $query );
        $preparedStatement->bind_param( "isssi" , $user_id , $_SESSION['user_email'] , $user_file_name , $user_file_location , $this->false );
        $preparedStatement->execute();
        $preparedStatement->store_result();
        $count = $preparedStatement->num_rows();
        
        if( $count != 0 )
        {
            return true;
        }
        else
        {
            return false;
        }
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    public function createFolder( $folder_name )
    {
        // ------------------------------------------------------------------------------------------ //
                                                // FOLDER CREATE
        // ------------------------------------------------------------------------------------------ //

        if( !file_exists( $this->file_location . $_SESSION['current_directory'] . $folder_name ) )
        {
            // IF FOLDER NOT EXISTS
            if( !mkdir( $this->file_location . $_SESSION['current_directory'] . $folder_name ) )
            {
                // ERROR UPLOADING
                echo "<script>toastr.error('Error while creating Folder', 'Upload Folder');</script>";
            }
            else
            {
                // SUCCESS STATUS
                echo "<script>toastr.success('Folder created Successfully', 'Upload File');</script>";
            }
        }

        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    public function uploadFile()
    {
        // ------------------------------------------------------------------------------------------ //
                                                // FILE UPLOAD
        // ------------------------------------------------------------------------------------------ //

        $zip = new ZipArchive;
        $res = $zip->open($_FILES['zip_file']['tmp_name']);
        if ($res === TRUE)
        {
            $zip->extractTo($this->file_location . $_SESSION['current_directory']);
            $zip->close();
        }

        $file_name_array = (explode("###",rtrim($_POST['file_names'],"###")));
        for($i=0 ; $i<sizeof($file_name_array) ; $i++)
        {
            $file_path = $this->file_location . $_SESSION['current_directory'] . $file_name_array[$i];
            if( file_exists( $file_path ) )
            {
                $this->manageFileInformation( $file_name_array[$i] , $this->file_location . $_SESSION['current_directory'] , filesize( $file_path ) );
            }
        }

            // SUCCESS STATUS
            echo "<script>toastr.success('File Uploaded Successfully', 'Upload File');</script>";

        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    public function uploadFolder()
    {
        // ------------------------------------------------------------------------------------------ //
                                                // FOLDER UPLOAD
        // ------------------------------------------------------------------------------------------ //
        
        $zip = new ZipArchive;
        $res = $zip->open($_FILES['zip_file']['tmp_name']);
        if ($res === TRUE)
        {
            $zip->extractTo($this->file_location . $_SESSION['current_directory']);
            $zip->close();
        }

        $file_path_array = (explode("###",rtrim($_POST['file_paths'],"###")));
        for($i=0 ; $i<sizeof($file_path_array) ; $i++)
        {
            $file_path = $this->file_location . $_SESSION['current_directory'] . $file_path_array[$i];
            if( file_exists( $file_path ) )
            {
                $this->manageFileInformation( basename( $file_path ) , substr($file_path , 0 , strpos($file_path , basename( $file_path ))) , filesize( $file_path ) );
            }
        }

        echo "<script>toastr.success('Folder Uploaded Successfully', 'Upload Folder');</script>";
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

// ----------------------------------------------------------------------------------------------------//
                                                // JSON
// ----------------------------------------------------------------------------------------------------//

    public function getUserListJSON()
    {
        $user_list_json = array();
        $query = "SELECT user_id , user_email FROM users WHERE is_deleted = ?";
        $preparedStatement = $this->connection->prepare( $query );
        $preparedStatement->bind_param( "i" , $this->false );
        $preparedStatement->execute();
        $preparedStatement->store_result();
        $count = $preparedStatement->num_rows();

        if( $count != 0 )
        {
            $preparedStatement->bind_result( $user_id , $user_email );
            while($preparedStatement->fetch())
            {
                if( $user_id != $_SESSION['user_id'] )
                {
                    array_push( $user_list_json , '<option value="'. $user_id .'">'. $user_email .'</option>' );
                }
            }
        }

		header('Content-Type: application/json');
        echo json_encode($user_list_json);
    }
    
    public function getFolderListJSON( $current_directory = '' )
    {
        // ------------------------------------------------------------------------------------------ //
                                    // FOLDER LIST IN SPECIFIC DIRECTORY
        // ------------------------------------------------------------------------------------------ //
        
        if( $current_directory != '' || $current_directory != null )
        {
            $_SESSION['current_directory'] .= $current_directory . '/' ;
        }

        if( $current_directory != 'null' )
        {
            $folder_list = $this->dirToArray( $this->file_location . $_SESSION['current_directory'] );
            $folder_list['breadcrumps'] = array();
        }
        
        // CURRENT DIR BREADCRUMPS
        if( $_SESSION['current_directory'] == null || $_SESSION['current_directory'] == '' )
        {
            $folder_list['breadcrumps'] = array('<li class="list-inline-item"><i class="far fa-folder-open"></i>&nbsp;' . $_SESSION['user_email'] . '</li>');
        }
        else
        {
            $folder_list['breadcrumps'] = array('<li class="list-inline-item" onclick="openDirectory()"><i class="far fa-folder"></i>&nbsp;' . $_SESSION['user_email'] . '</li>');
            $breadcrumb_array = explode( "/" , rtrim( $_SESSION['current_directory'] , "/" ) );
            for( $i=0 ; $i<sizeof( $breadcrumb_array ) ; $i++ )
            {
                if( $breadcrumb_array[$i] != end( $breadcrumb_array ) )
                {
                    if( $breadcrumb_array[$i] != '' || $breadcrumb_array[$i] != null )
                    array_push( $folder_list['breadcrumps'] , '<li class="list-inline-item" onclick="openDirectory('. "'" . implode( '/' , array_slice( $breadcrumb_array, 0 , ($i+1) ) ) . "'" .')"><i class="far fa-folder"></i>&nbsp;' . array_slice( $breadcrumb_array, $i , 1 )[0] . '</li>' );
                }
                else
                {
                    array_push( $folder_list['breadcrumps'] , '<li class="list-inline-item"><i class="far fa-folder-open"></i>&nbsp;'. $breadcrumb_array[$i] .'</li>' );
                }
            }
        }
        
		header('Content-Type: application/json');
        echo json_encode($folder_list);
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    public function getFileInformationJSON( $user_file_name )
    {
        // ------------------------------------------------------------------------------------------ //
                                        // GETTING FILE INFORMATION
        // ------------------------------------------------------------------------------------------ //

        $file_info_json = array();
        $user_file_location = $this->file_location . $_SESSION['current_directory'];
        $query = "SELECT user_file_size , created_at , updated_at FROM user_file WHERE user_id = ? AND user_file_name LIKE ? AND user_file_location LIKE ? AND is_deleted = ?";
        $preparedStatement = $this->connection->prepare( $query );
        $preparedStatement->bind_param( "issi" , $_SESSION['user_id'] , $user_file_name , $user_file_location , $this->false );
        $preparedStatement->execute();
        $preparedStatement->store_result();
        $count = $preparedStatement->num_rows();

        if( $count != 0 )
        {
            $preparedStatement->bind_result( $user_file_size , $created_at , $updated_at );
            $preparedStatement->fetch();

            $user_file_size = $user_file_size;
            $user_file_size = number_format(($user_file_size/1024)/1024 ,2) . ' MiB';
            
            $file_info_json = array( $user_file_size , $created_at , $updated_at );
        }
        
		header('Content-Type: application/json');
        echo json_encode($file_info_json);
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

// ----------------------------------------------------------------------------------------------------//
                                            // GET FILES
// ----------------------------------------------------------------------------------------------------//

    public function shareFile( $user_file_name , $user_id_list )
    {
        // ------------------------------------------------------------------------------------------ //
                                            // SHARING FILE
        // ------------------------------------------------------------------------------------------ //
        
        $file_url = $this->file_location . $_SESSION['current_directory'] . $user_file_name;

        if( file_exists($file_url) )
        {
            $user_id_array = explode( ',' , $user_id_list );
            $user_file_location = $this->file_location . $_SESSION['current_directory'];
            $time = date("Y-m-d h:i:sa");

            for($i=0 ; $i<sizeof($user_id_array) ; $i++)
            {
                if( !$this->checkFileShareExists( $user_id_array[$i] , $user_file_name ) )
                {
                    // IF FILE INFORMATION NOT EXISTS
                    $query = "INSERT INTO user_file_share ( user_id , from_user_email , user_file_name , user_file_location , shared_at , is_deleted ) VALUES ( ? , ? , ? , ? , ? , ? )";
                    $preparedStatement = $this->connection->prepare( $query );
                    $preparedStatement->bind_param( "issssi" , $user_id_array[$i] , $_SESSION['user_email'] , $user_file_name , $user_file_location , $time , $this->false );
                    $preparedStatement->execute();
                }
            }
        }
        else
        {
            echo"file does not exist";
        }
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }
    
    private function downloadMultiData( $user_file_name )
    {
        // ------------------------------------------------------------------------------------------ //
                                // ADDITIONAL TO FILE DOWNLOAD FUNCTIONALITY
        // ------------------------------------------------------------------------------------------ //

        // IF MULTIPLE FILES OR FOLDER
        $time = time();

        $zip = new ZipArchive();
        if ($zip->open('zip_bin/'.$time.'.zip', ZipArchive::CREATE)!==TRUE)
        {
            exit("cannot open file\n");
        }
        
        if( is_array( $user_file_name ) )
        {
            foreach ($user_file_name as $file)
            {
                $path = $this->file_location . $_SESSION['current_directory'] . $file;
                if( file_exists($path) )
                {
                    if( !is_dir( $path ) )
                    {
                        // IF NOT DIRECTORY
                        $zip->addFromString( basename($path) , file_get_contents($path) );
                    }
                    else
                    {
                        // IF DIRECTORY
                        $directory_files = new RecursiveIteratorIterator(
                            new RecursiveDirectoryIterator($path),
                            RecursiveIteratorIterator::LEAVES_ONLY
                        );
                        
                        foreach ($directory_files as $dir_name => $dir_file)
                        {
                            if ( !$dir_file->isDir() )
                            {
                                $filePath = $dir_file->getRealPath();
                                $relativePath = substr($filePath , strpos($filePath , $file) , strlen($filePath));
                                $zip->addFile($filePath, $relativePath);
                            }
                        }
                    }
                }
                else
                {
                    echo"file does not exist";
                }
            }
        }
        else
        {
            $path = $this->file_location . $_SESSION['current_directory'] . $user_file_name;
            // IF DIRECTORY
            $directory_files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            foreach ($directory_files as $dir_name => $dir_file)
            {
                if ( !$dir_file->isDir() )
                {
                    $filePath = $dir_file->getRealPath();
                    $relativePath = substr($filePath , strpos($filePath , $user_file_name) , strlen($filePath));
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }

        $zip->close();
        header("Content-type: application/zip"); 
        header("Content-Disposition: attachment; filename=$time.zip");
        header("Content-length: " . filesize('zip_bin/'.$time.'.zip'));
        header("Pragma: no-cache"); 
        header("Expires: 0"); 
        readfile('zip_bin/'.$time.'.zip');
        $no_use = unlink('zip_bin/'.$time.'.zip');
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    public function downloadFile( $user_file_name )
    {
        // ------------------------------------------------------------------------------------------ //
                                        // DOWNLOADING FILE
        // ------------------------------------------------------------------------------------------ //

        $user_file_name = explode( ',' , $user_file_name );

        if( sizeof( $user_file_name ) == 1 )
        {
            // IF ONLY 1 FILE
            $file_url = $this->file_location . $_SESSION['current_directory'] . $user_file_name[0];
            
            if( file_exists($file_url) )
            {
                if( !is_dir($file_url) )
                {
                    $content_type=mime_content_type($file_url);
                    header('Content-Description: File Transfer');
                    header("Content-Type: $content_type");
                    header('Content-Disposition: attachment; filename="'.$user_file_name[0].'"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file_url));
                    readfile($file_url);
                }
                else
                {
                    $this->downloadMultiData( $user_file_name[0] );
                }
            }
            else
            {
                echo"file does not exist";
            }
        }
        else
        {
            $this->downloadMultiData( $user_file_name );
        }
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    public function deleteFile( $user_file_name )
    {
        // ------------------------------------------------------------------------------------------ //
                                            // DELETE FILE
        // ------------------------------------------------------------------------------------------ //
        
        $user_file_name = explode( ',' , $user_file_name );
        $user_file_location = $this->file_location . $_SESSION['current_directory'];
        $time = date("Y-m-d h:i:sa");
        
        foreach ($user_file_name as $file)
        {
            $path = $this->file_location . $_SESSION['current_directory'] . $file;

            if( file_exists( $path ) )
            {
                if( is_dir( $path ) )
                {
                    $dir_iteration = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
                    $dir_files = new RecursiveIteratorIterator($dir_iteration,
                                RecursiveIteratorIterator::CHILD_FIRST);
                    foreach($dir_files as $dir_file)
                    {
                        if ($dir_file->isDir())
                        {
                            rmdir($dir_file->getRealPath());
                        }
                        else
                        {
                            $file_name_array = explode('/' , str_replace('\\', '/', $dir_file));
                            $file_name = array_pop($file_name_array);
                            
                            $file_path = implode( '/' , $file_name_array) . '/';

                            // UPDATING IS_DELETED TO TRUE IN user_file
                            $query = "UPDATE user_file SET is_deleted = ? , deleted_at = ? WHERE user_id = ? AND user_file_name LIKE ? AND user_file_location LIKE ?";
                            $preparedStatement = $this->connection->prepare( $query );
                            $preparedStatement->bind_param( "isiss" , $this->true , $time , $_SESSION['user_id'] , $file_name , $file_path );
                            $preparedStatement->execute();

                            // DELETING FILE
                            unlink($dir_file->getRealPath());
                        }
                    }
                    rmdir($path);
                }
                else
                {
                    unlink($path);
                    
                    // UPDATING IS_DELETED TO TRUE IN user_file
                    $query = "UPDATE user_file SET is_deleted = ? , deleted_at = ? WHERE user_id = ? AND user_file_name LIKE ? AND user_file_location LIKE ?";
                    $preparedStatement = $this->connection->prepare( $query );
                    $preparedStatement->bind_param( "isiss" , $this->true , $time , $_SESSION['user_id'] , $file , $user_file_location );
                    $preparedStatement->execute();
                }
            }
            else
            {
                echo"file does not exist";
            }
        }
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }
}
?>