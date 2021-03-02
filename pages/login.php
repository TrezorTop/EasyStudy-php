<?php

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';

if (isset($_POST['login-btn'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $username))) {

        if (password_verify($password, DB::query('SELECT password FROM users WHERE username=:username', array(':username' => $username))[0]['password'])) {

            $crypto_strong = True;
            $token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));

            $userId = DB::query('SELECT id FROM users WHERE username=:username', array(':username' => $username))[0]['id'];

            DB::query('INSERT INTO login_tokens VALUES (id, :token, :user_id)', array(':token' => sha1($token), ':user_id' => $userId));

            setcookie("SNID", $token, time() + 60 * 60 * 24 * 14, '/', NULL, NULL, TRUE);
            setcookie("SNID_SECOND", '1', time() + 60 * 60 * 24 * 6, '/', NULL, NULL, TRUE);

            echo "</br> Success!";

        } else {
            echo "Incorrect password!";
        }

    } else {
        echo "User not registered";
    }
}

?>

<h1>Login to your account</h1>
<form action="login.php" method="post">
    <input type="text" class="username" name="username" placeholder="Username"><br>
    <input type="password" class="password" name="password" placeholder="Password"><br>
    <input type="button" class="login-btn" name="login-btn" value="Login">
</form>

<script src="../libs/jquery-3.5.1.min.js"></script>

<script type="text/javascript">

    $('.login-btn').click(function () {

        $.ajax({

            type: "POST",
            url: "../api/auth",
            processData: false,
            contentType: "application/json",
            data: '{ "username": "' + $(".username").val() + '", "password": "' + $(".password").val() + '" }',
            success: function (r) {
                console.log(r)
            },
            error: function (r) {
                console.log(r)
            }

        });
    });
</script>