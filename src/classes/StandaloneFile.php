<?php
class StandaloneFile
{
    private $connection;
    private $false = 0;
    private $true = 1;

    public function __construct()
    {
        global $database;
        $this->connection = $database->getConnection();
    }

    private function dirToArray( $directory )
    {
        $result = array(); 
        $cdir = scandir( $directory ); 
        foreach ( $cdir as $key => $value ) 
        { 
            if ( !in_array( $value,array( ".",".." ) ) ) 
            { 
                if ( is_dir( $directory . DIRECTORY_SEPARATOR . $value ) ) 
                { 
                    $result[$value] = null; 
                } 
                else 
                { 
                    $result[] = $value; 
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
    
    private function checkFileShareExists( $user_id , $user_file_name , $current_directory )
    {
        // ------------------------------------------------------------------------------------------ //
                                // Check File Share Information Existance in Database
        // ------------------------------------------------------------------------------------------ //

        $user_file_location = $current_directory;

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

    public function createFolder( $current_directory , $folder_name )
    {
        // ------------------------------------------------------------------------------------------ //
                                                // FOLDER CREATE
        // ------------------------------------------------------------------------------------------ //
        $_SESSION['error'] = '';

        if( !file_exists( $current_directory . $folder_name ) )
        {
            // IF FOLDER NOT EXISTS
            if( !mkdir( $current_directory . $folder_name ) )
            {
                // ERROR UPLOADING
                $_SESSION['error'] .= "<script>toastr.error('Error while creating Folder', 'Upload Folder');</script>";
            }
            else
            {
                // SUCCESS STATUS
                $_SESSION['error'] .= "<script>toastr.success('Folder created Successfully', 'Upload File');</script>";
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
            $zip->extractTo($_SESSION['current_directory']);
            $zip->close();
        }

        $file_name_array = (explode("###",rtrim($_POST['file_names'],"###")));
        for($i=0 ; $i<sizeof($file_name_array) ; $i++)
        {
            $file_path = $_SESSION['current_directory'] . $file_name_array[$i];
            if( file_exists( $file_path ) )
            {
                $this->manageFileInformation( $file_name_array[$i] , $_SESSION['current_directory'] , filesize( $file_path ) );
            }
        }

        if( $_SESSION['error'] == '' || $_SESSION['error'] == null )
        {
            // SUCCESS STATUS
            $_SESSION['error'] .= "<script>toastr.success('Folder Uploaded Successfully', 'Upload Folder');</script>";
        }

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

        if( $current_directory != '' )
        {
            $data = $this->dirToArray( $current_directory );
            $folder_list  = array();
            $folder_list['directory'] = array();
            $folder_list['file'] = array();

            foreach( $data as $key => $value )
            {
                if( !is_int( $key ) )
                {
                    array_push( $folder_list['directory'] , array( 'key' => $key ) );
                }
                else
                {
                    array_push( $folder_list['file'] , array( 'key' => $value ) );
                }
            }
        }
        
		header('Content-Type: application/json');
        echo json_encode($folder_list);
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    public function getFileInformationJSON( $current_directory , $user_file_name )
    {
        // ------------------------------------------------------------------------------------------ //
                                        // GETTING FILE INFORMATION
        // ------------------------------------------------------------------------------------------ //

        $file_info_json = array();
        $user_file_location = $current_directory;
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

    public function shareFile( $current_directory , $user_file_name , $user_id_list )
    {
        // ------------------------------------------------------------------------------------------ //
                                            // SHARING FILE
        // ------------------------------------------------------------------------------------------ //
        
        $file_url = $current_directory . $user_file_name;

        if( file_exists($file_url) )
        {
            $user_id_array = explode( ',' , $user_id_list );
            $user_file_location = $current_directory;
            $time = date("Y-m-d h:i:sa");

            for($i=0 ; $i<sizeof($user_id_array) ; $i++)
            {
                if( !$this->checkFileShareExists( $user_id_array[$i] , $user_file_name , $current_directory ) )
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

    public function deleteFile( $current_directory , $user_file_name )
    {
        // ------------------------------------------------------------------------------------------ //
                                            // DELETE FILE
        // ------------------------------------------------------------------------------------------ //
        
        $user_file_name = explode( ',' , $user_file_name );
        $user_file_location = $current_directory;
        $time = date("Y-m-d h:i:sa");
        
        foreach ($user_file_name as $file)
        {
            $path = $current_directory . $file;

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
                            $file_name = end($file_name_array);
                            
                            $file_path = './user_datas' . str_replace('\\', '/', substr($dir_file->getRealPath() , strlen($path)-1));
                            $plorp = substr(strrchr($file_path,'/'), 1);
                            $file_path = substr($file_path, 0, - strlen($plorp));

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