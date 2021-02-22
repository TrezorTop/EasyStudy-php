<?php

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';

if (isset($_POST['reset-password-btn'])) {

    $crypto_strong = True;
    $token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));
    $email = $_POST['email'];
    $userId = DB::query('SELECT id FROM users WHERE email=:email', array(':email' => $email))[0]['id'];

    DB::query('INSERT INTO password_tokens VALUES (id, :token, :user_id)', array(':token' => sha1($token), ':user_id' => $userId));

    echo "Email sent!";

    echo "<br> token is $token";
}

?>

<h1>Forgot Password</h1>
<form action="forgot-password.php" method="post">
    <input type="text" name="email" placeholder="Email"><br>
    <input type="submit" name="reset-password-btn" value="Reset password">
</form>
