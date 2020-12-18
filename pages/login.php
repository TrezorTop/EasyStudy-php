<?php

include $_SERVER['DOCUMENT_ROOT'] . '/php/connect.php';

if (isset($_POST['login-btn'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $usernameMysqlResult = mysqli_query($link, "SELECT username FROM `users` WHERE username = '$username'");

    if (mysqli_num_rows($usernameMysqlResult) > 0) {
        $passwordMysqlResult = mysqli_query($link, "SELECT password FROM `users` WHERE username = '$username'");
        $passwordFormDB = mysqli_fetch_row($passwordMysqlResult)[0];

        if (password_verify($password, $passwordFormDB)) {

            $crypto_strong = True;
            $token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));
            $sha1Token = sha1($token);

            $userIdMysqlResult = mysqli_query($link, "SELECT id FROM `users` WHERE username = '$username'");
            $userId = mysqli_fetch_row($userIdMysqlResult)[0];

            mysqli_query($link, "INSERT INTO login_tokens VALUES (id, '$sha1Token', '$userId')");

            setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
            setcookie("SNID_SECOND", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);

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