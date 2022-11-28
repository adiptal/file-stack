<!-- CARD HEADING -->
<h1>FileStack.io</h1>
<h2>Sign Up</h2>
<!-- END CARD HEADING -->


<div class="form-group">
    <input type="text" class="form-control" id="user_first_name" placeholder="First Name">
</div>

<div class="form-group mt-5">
    <input type="text" class="form-control" id="user_last_name" placeholder="Last Name">
</div>

<div class="form-group mt-5">
    <input type="email" class="form-control" id="user_email" placeholder="Email">
</div>

<div class="form-group mt-5">
    <input type="password" class="form-control" id="user_password" placeholder="Password">
</div>

<div class="form-group mt-5">
    <input type="password" class="form-control" id="user_password_verify" placeholder="Confirm Password">
</div>

<div class="button-bg">
    <button class="btn btn-link" onclick="signInPage()"><i class="fas fa-angle-left"></i> Back to Signin</button><br/>
    <button class="btn" onclick="signUp()"><i class="fas fa-sign-in-alt"></i> SignUp</button>
</div>
<label class="error" id="error"></label>