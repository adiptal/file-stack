<?php
class Accounts
{
	private $current_time;
    private $connection;
    private $false = 0;
    private $true = 1;

	function __construct()
	{
		// Default Values
        global $database;
        $this->connection = $database->getConnection();
		$this->current_time = date('Y-m-d h:i:s', time());
	}

	private function checkEmailExists( $user_email )
	{
		// ********** Checking if Email Exists and Returning Resultset **********//
		
		// Fetching via Class Account_Crud Model
        $query = "SELECT user_id , user_password , user_first_name , user_token FROM users WHERE user_email = ? AND is_deleted = ?";
        $preparedStatement = $this->connection->prepare( $query );
        $preparedStatement->bind_param( "si" , $user_email , $this->false );
        $preparedStatement->execute();
        $preparedStatement->store_result();
		$count = $preparedStatement->num_rows();
		return $preparedStatement;

		// ********** xx ********** xx ********** xx ********** xx ********** //
	}

	private function generateRandomString( $length = 10 )
	{
		// ********** Generating Random String **********//
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen( $characters );
		$randomString = '';
		for ( $i = 0; $i < $length; $i++ )
		{
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;

		// ********** xx ********** xx ********** xx ********** //
	}

	private function mailData( $user_email , $subject , $body )
	{
		// ********** Mailing Data to Recipent **********//
		require(APPPATH."third_party/phpmailer/src/PHPMailer.php");
		require(APPPATH."third_party/phpmailer/src/SMTP.php");
		
		$mail = new PHPMailer\PHPMailer\PHPMailer();
		$mail->IsSMTP(); // enable SMTP

		$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true; // authentication enabled
		$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465; // or 587
		$mail->IsHTML(true);
		$mail->Username = "placeyourself.college@gmail.com";
		$mail->Password = "Place@studylinkclasses";
		$mail->SetFrom("placeyourself.college@gmail.com","Recruitme");
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AddAddress( $user_email );

		if( !$mail->Send() )
		{
			return false;
		}
		else
		{
			return true;
		}

		// ********** xx ********** xx ********** xx ********** //
	}

	public function signIn( $user_email , $verify_password )
	{
		// ********** SignIn Process ********** //

		// Verifying Email Exists 
		$query = $this->checkEmailExists( $user_email );
		$query->bind_result( $user_id , $user_password , $user_first_name , $user_token );
		$query->fetch();


		if( $query->num_rows() > 0 )
		{
			// Check Token Exists
			if( $user_token == '' )
			{
				// Verifying Password
				if( !password_verify( $verify_password , $user_password ) )
				{
					// Showing Password Error
					$_SESSION['error'] = "<script>toastr.error('Invalid Password' , 'Signin');</script>";
				}
				else
				{
					$_SESSION['user_email'] = $user_email;
					$_SESSION['user_id'] = $user_id;
					$_SESSION['user_first_name'] = $user_first_name;
					echo "###LOGGED_IN###";
				}
			}
			else
			{
				// Showing User Forget Password Error
				$_SESSION['error'] = "<script>toastr.error('Requested to Reset Password' , 'Signin');</script>";
			}
		}
		else
		{
			// Showing User Error
			$_SESSION['error'] = "<script>toastr.error('Invalid User Email' , 'Signin');</script>";
		}

		// ********** xx ********** xx ********** //
	}

	public function signUp( $user_first_name , $user_last_name , $user_email , $user_password )
	{
		$query = $this->checkEmailExists( $user_email );

		if( $query->num_rows() === 0 )
		{
			$password_hash = password_hash($user_password , PASSWORD_BCRYPT);
			$query = "INSERT INTO users ( user_first_name , user_last_name , user_email , user_password , created_at , is_deleted ) VALUES ( ? , ? , ? , ? , ? , ? )";
			$preparedStatement = $this->connection->prepare( $query );
			$preparedStatement->bind_param( "sssssi" , $user_first_name , $user_last_name , $user_email , $password_hash , $this->current_time , $this->false );
			$preparedStatement->execute();
			
			mkdir( "../user_datas/" . $user_email );
			$_SESSION['error'] = "<script>toastr.success('User Registered' , 'Signup');</script>";
		}
		else
		{
			$_SESSION['error'] = "<script>toastr.error('User already Exists' , 'Signup');</script>";
		}
	}

	public function signout()
	{
		$_SESSION['user_id'] = null;
		$_SESSION['user_email'] = null;
		$_SESSION['current_directory'] = null;
		$_SESSION['shared_current_directory'] = null;
		session_destroy();
	}
}
?>