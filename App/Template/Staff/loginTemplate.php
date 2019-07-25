<script src="node_modules/jquery/dist/jquery.js"></script>
<script src="node_modules/toastr/build/toastr.min.js"></script>
<link href='https://fonts.googleapis.com/css?family=Ubuntu:500' rel='stylesheet' type='text/css'>
<link href="styles/login.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="node_modules/toastr/build/toastr.min.css">

<form method="POST">
    <div class="login">
        <div class="login-header">
            <h1>Login</h1>
        </div>
        <div class="login-form">
            <h3>Username:</h3>
            <input type="text" name="username" placeholder="Username"/><br>
            <h3>Password:</h3>
            <input type="password" name="password" placeholder="Password"/>
            <br>
            <input type="hidden" name="csrf_token" value="<?= $data['çsrf_token'] ?>" />
            <input type="submit" name="login" value="Login" class="login-button"/>
        </div>
    </div>
</form>
