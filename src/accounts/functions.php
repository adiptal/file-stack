<?php
require_once('../classes/Database.php');

switch(true)
{
        case isset($_POST['signIn']) : 
                                            require_once('../classes/Accounts.php');
                                            $Accounts = new Accounts();
                                            $Accounts->signIn($_POST['user_email'] , $_POST['user_password']);
        break;
        
        case isset($_POST['signUp']) : 
                                            require_once('../classes/Accounts.php');
                                            $Accounts = new Accounts();
                                            $Accounts->signUp($_POST['user_first_name'] , $_POST['user_last_name'] , $_POST['user_email'] , $_POST['user_password']);
        break;
        
        case isset($_POST['signOut']) : 
                                            require_once('../classes/Accounts.php');
                                            $Accounts = new Accounts();
                                            $Accounts->signout();
        break;
        
        case isset($_POST['forgot']) : 
                                            require_once('../classes/Accounts.php');
                                            $Accounts = new Accounts();
                                            $Accounts->forgot($_POST['user_email']);
        break;
        
        case isset($_POST['resendForgotEmail']) : 
                                            require_once('../classes/Accounts.php');
                                            $Accounts = new Accounts();
                                            $Accounts->resendForgotEmail($_POST['user_email']);
        break;
        
        case isset($_POST['resetPassword']) : 
                                            require_once('../classes/Accounts.php');
                                            $Accounts = new Accounts();
                                            $Accounts->resetPassword($_POST['user_password']);
        break;
}
?>