<?php
ob_start();
session_start();
class Database
{
    private $host;
    private $username;
    private $password;
    private $database;
    private $connection;
    
    public function __construct()
    {
        $this->host = "localhost";
        $this->username = "adi987123@gmail.com";
        $this->password = "adi987123@gmail.com";
        $this->database = "filestack";
        $this->connectDB();
    }

    public function connectDB()
    {
        $this->connection = mysqli_connect( $this->host , $this->username , $this->password );
        if(mysqli_connect_error())
        {
            die("Connection Failed: " . mysqli_error());
        }
        
        $db_selected = $this->connection->select_db( $this->database );
        if( !$db_selected ){}
        else{}
    }

    public function query( $sql )
    {
        $result = $this->connection->query( $sql );
        if( !$result )
        {
            die("query Failed! " .$sql);
        }
        return $result;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function escape_string( $string )
    {
        $escaped_string = $this->connection->real_escape_string( $string );
        return $escaped_string;
    }
    
    function __destruct()
    {
        //this is a destructor
    }
}

$database = new Database();
?>