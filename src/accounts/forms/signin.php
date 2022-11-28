<!-- CARD HEADING -->
<h1>FileStack.io</h1>
<h2>Sign In</h2>
<!-- END CARD HEADING -->


<div class="form-group">
    <input type="email" class="form-control" id="user_email" placeholder="Email">
</div>

<div class="form-group mt-5">
    <input type="password" class="form-control" id="user_password" placeholder="Password">
</div>

<div class="button-bg">
    <button class="btn btn-link" onclick="signUpPage()">Create Account</button><br/>
    <button class="btn btn-link" onclick="forgotPage()">Forgot ?</button><br/>
    <button class="btn" onclick="signIn()"><i class="fas fa-sign-in-alt"></i> Sign-In</button>
</div>
<label class="error" id="error"></label>