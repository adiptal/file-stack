<?php
class SharedFile
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

// ----------------------------------------------------------------------------------------------------//
                                                // JSON
// ----------------------------------------------------------------------------------------------------//

    public function fetchDataFromDB( $current_directory )
    {
        // ------------------------------------------------------------------------------------------ //
                                // FETCH FOLDER AND FILE NAMES IN DB
        // ------------------------------------------------------------------------------------------ //

        $folder_list  = array('');
        if( $current_directory == 'null' )
        {
            $_SESSION['shared_current_directory'] = '';
        }
        $folder_list['directory'] = array();
        $folder_list['file'] = array();
    
        $query = "SELECT user_file_name , user_file_location FROM user_file_share WHERE user_id = ? AND is_deleted = ?";
        $preparedStatement = $this->connection->prepare( $query );
        $preparedStatement->bind_param( "ii" , $_SESSION['user_id'] , $this->false );
        $preparedStatement->execute();
        $preparedStatement->store_result();
        $count = $preparedStatement->num_rows();

        if( $count != 0 )
        {
            $preparedStatement->bind_result( $user_file_name , $user_file_location );
            while($preparedStatement->fetch())
            {
                if( file_exists( $user_file_location . $user_file_name ) )
                {
                    if( is_dir( $user_file_location . $user_file_name ) )
                    {
                        array_push( $folder_list['directory'] , array( 'key' => $user_file_name , 'user_file_location' => $user_file_location.$user_file_name ) );
                    }
                    else
                    {
                        array_push( $folder_list['file'] , array( 'key' => $user_file_name , 'user_file_location' => $user_file_location , 'user_data_location' => $user_file_location , 'user_data_name' => $user_file_name ) );
                    }
                }
            }
        }
        return $folder_list;
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    public function openDirectory( $current_directory )
    {
        // ------------------------------------------------------------------------------------------ //
                                // FETCH FOLDER AND FILE NAMES IN DIRECTORY
        // ------------------------------------------------------------------------------------------ //

        $folder_list  = array('');

        if( $_SESSION['shared_current_directory'] == '' && ( $current_directory == '' || $current_directory == null ) )
        {
            $folder_list = $this->fetchDataFromDB( $current_directory );
        }
        else
        {
            if( $current_directory != 'null' )
            {
                if( $current_directory != '' || $current_directory != null )
                {
                    $_SESSION['shared_current_directory'] = $current_directory ;
                }
                $current_directory_array = explode( "/" , rtrim( $_SESSION['shared_current_directory'] , "/" ) );
                $_SESSION['shared_directory_name'] = $current_directory_array[3];
                $_SESSION['shared_directory_location'] = implode( '/' , array_slice( $current_directory_array, 0 , 3 ) ) . '/';
    
                $folder_list['breadcrumps'] = array();
                $folder_list['directory'] = array();
                $folder_list['file'] = array();
        
                $data = $this->dirToArray( $_SESSION['shared_current_directory'] );
        
                foreach( $data as $key => $value )
                {
                    if( !is_int( $key ) )
                    {
                        array_push( $folder_list['directory'] , array( 'key' => $key , 'user_file_location' => $_SESSION['shared_current_directory'] . '/' . $key ) );
                    }
                    else
                    {
                        array_push( $folder_list['file'] , array( 'key' => $value , 'user_file_location' => $_SESSION['shared_current_directory'] , 'user_data_location' => implode('/' , array_slice(explode( '/' , $_SESSION['shared_current_directory'] ) , 0 , 3)) . '/' , 'user_data_name' => implode('' , array_slice(explode( '/' , $_SESSION['shared_current_directory'] ) , 3 , 1)) ) );
                    }
                }
            }
            else
            {
                $folder_list = $this->fetchDataFromDB( $current_directory );
            }
        }
        
        // CURRENT DIR BREADCRUMPS
        if( $_SESSION['shared_current_directory'] == null || $_SESSION['shared_current_directory'] == '' )
        {
            $folder_list['breadcrumps'] = array('<li class="list-inline-item"><i class="far fa-folder-open"></i>&nbsp;' . $_SESSION['user_email'] . '</li>');
        }
        else
        {
            $folder_list['breadcrumps'] = array('<li class="list-inline-item" onclick="openDirectory('. "'". 'null' ."'" .')"><i class="far fa-folder"></i>&nbsp;' . $_SESSION['user_email'] . '</li>');
            $breadcrumb_array = explode( "/" , rtrim( $_SESSION['shared_current_directory'] , "/" ) );
            for( $i=3 ; $i<sizeof( $breadcrumb_array ) ; $i++ )
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

    public function getFileInformationJSON( $user_file_name , $user_file_location )
    {
        // ------------------------------------------------------------------------------------------ //
                                        // GETTING FILE INFORMATION
        // ------------------------------------------------------------------------------------------ //

        $file_info_json = array();
        $query = "SELECT shared_at , from_user_email FROM user_file_share WHERE user_id = ? AND user_file_name LIKE ? AND user_file_location LIKE ? AND is_deleted = ?";
        $preparedStatement = $this->connection->prepare( $query );
        $preparedStatement->bind_param( "issi" , $_SESSION['user_id'] , $user_file_name , $user_file_location , $this->false );
        $preparedStatement->execute();
        $preparedStatement->store_result();
        $count = $preparedStatement->num_rows();

        if( $count != 0 )
        {
            $preparedStatement->bind_result( $shared_at , $from_user_email );
            $preparedStatement->fetch();
            array_push( $file_info_json , $shared_at , $from_user_email );
        }
        
		header('Content-Type: application/json');
        echo json_encode($file_info_json);
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

// ----------------------------------------------------------------------------------------------------//
                                            // GET FILES
// ----------------------------------------------------------------------------------------------------//
    
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
                $path = $file;
                if( file_exists($path) )
                {
                    if( !is_dir( $path ) )
                    {
                        // IF NOT DIRECTORY
                        $zip->addFromString( basename($path) , file_get_contents($path) );
                    }
                    else
                    {
                        $path = $file;
                        $user_file = substr($path , strrpos($path , 'user_datas') , strlen($path));
                        $user_file = implode('\\' , array_slice( explode('/' , $path) , sizeof(explode('/' , $path))-1 ));
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
                                $relativePath = substr($filePath , strpos($filePath , $user_file) , strlen($filePath));
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
            $path = $user_file_name;
            $user_file_name = substr($user_file_name , strrpos($user_file_name , 'user_datas') , strlen($user_file_name));
            $user_file_name = implode('\\' , array_slice( explode('/' , $user_file_name) , sizeof(explode('/' , $user_file_name))-1 ));
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
            $file_url = $user_file_name[0];
            
            if( file_exists($file_url) )
            {
                if( !is_dir($file_url) )
                {
                    $content_type=mime_content_type($file_url);
                    header('Content-Description: File Transfer');
                    header("Content-Type: $content_type");
                    header('Content-Disposition: attachment; filename="'.basename( $user_file_name[0] ).'"');
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
}
?>