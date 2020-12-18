<?php

include $_SERVER['DOCUMENT_ROOT'] . '/php/connect.php';

if (isset($_POST['reset-password-btn'])) {

    $email = $_POST['email'];

    $crypto_strong = True;
    $token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));
    $sha1Token = sha1($token);

    $userIdMysqlResult = mysqli_query($link, "SELECT id FROM `users` WHERE email = '$email'");
    $userId = mysqli_fetch_row($userIdMysqlResult)[0];

    mysqli_query($link, "INSERT INTO password_tokens VALUES (id, '$sha1Token', '$userId')");

    echo "Email sent!";

    echo "<br> token is $token";
}

?>

<h1>Forgot Password</h1>
<form action="forgot-password.php" method="post">
    <input type="text" name="email" placeholder="Email"><br>
    <input type="submit" name="reset-password-btn" value="Reset password">
</form>
