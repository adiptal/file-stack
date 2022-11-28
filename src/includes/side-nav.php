<div class="sidebar">
    <div class="container">
<?php
if( !isset( $_SESSION['user_id'] ) )
{
?>
        <div class="login-form">
        <div class="form-group">
        <input type="email" class="form-control" id="user_email" name="user_email" placeholder="Email">
        </div>
        <div class="form-group">
        <input type="password" class="form-control" id="user_password" name="user_password" placeholder="Password">
        </div>
        <a href="http://localhost/filestack/accounts/?page=signup" class="signup-link">Create Account</a>
        <button onclick="signIn()" class="btn mt-2">Sign-In</button>
        </div>
<?php
}
else
{
?>
        <ul class="list-unstyled">
            <li id="my-drive"><a href="index.php" onclick="loadPageViewAjax( event , 'index.php' , 'main-content.php' )">My Drive</a></li>
            <li id="shared-with-me"><a href="shared-with-me.php" onclick="loadPageViewAjax( event , 'shared-with-me.php' , 'shared-content.php' )">Shared With Me</a></li>
        </ul>
<?php
}
?>
    </div>
</div>