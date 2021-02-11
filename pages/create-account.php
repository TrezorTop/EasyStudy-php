<?php

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';

if (isset($_POST['create-account-btn'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    if (!DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $username))) {

        if (strlen($username) >= 3 && strlen($username) <= 60) {

            if (preg_match('/[a-zA-Z0-9_]+/', $username)) {

                if (strlen($password) >= 6 && strlen($password) <= 60) {

                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                        if (!DB::query('SELECT email FROM users WHERE email=:email', array(':email' => $email))) {

                            DB::query('INSERT INTO users VALUES (id, :username, :password, :email, \'0\', NULL)', array(':username' => $username, ':password' => password_hash($password, PASSWORD_BCRYPT), ':email' => $email));
                            echo "Success!";
                        } else {
                            echo 'Email in use!';
                        }
                    } else {
                        echo 'Invalid email!';
                    }
                } else {
                    echo 'Invalid password!';
                }
            } else {
                echo 'Invalid username';
            }
        } else {
            echo 'Invalid username';
        }

    } else {
        echo 'User already exists!';
    }
}

?>

<h1>Register</h1>
<form action="create-account.php" method="post">
    <input type="text" name="username" placeholder="Username"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <input type="email" name="email" placeholder="example@mail.com"><br>
    <input type="submit" name="create-account-btn" value="Create Account">
</form>
