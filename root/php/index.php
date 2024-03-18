<!DOCTYPE html>
<html>
<link rel="stylesheet" href="index.css">

<head>
    <title>Login Form</title>
</head>

<body>
    <hr>
    <h1>Login Page</h1><br>
    <div class="login">
        <form id="login" method="POST" action="process.php">
            <label for="uname"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="uname" id="Uname" required>

            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="psw" id="Pass" required>

            <div>
                <button type="submit" class="logInButton">LogIn</button>
                <button type="button" onclick="location.href='sign_up.html';" class="logInButton">SignUp</button>
            </div>
            <br><br>
        </form>
    </div>
</body>

</html>