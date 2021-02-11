<?php

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';

if (isset($_POST['login-btn'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $username))) {

        if (password_verify($password, DB::query('SELECT password FROM users WHERE username=:username', array(':username' => $username))[0]['password'])) {

            $crypto_strong = True;
            $token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));
            $sha1Token = sha1($token);

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
    <input type="text" name="username" placeholder="Username"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <input type="submit" name="login-btn" value="Login">
</form>