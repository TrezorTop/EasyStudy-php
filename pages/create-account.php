<?php

include $_SERVER['DOCUMENT_ROOT'] . '/php/connect.php';

if (isset($_POST['create-account-btn'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $usernameMysqlResult = mysqli_query($link, "SELECT username FROM `users` WHERE username = '$username' OR email = '$email'");

    if (mysqli_num_rows($usernameMysqlResult) < 1) {

        if (strlen($username) >= 3 && strlen($username) <= 32) {

            if (preg_match('/[a-zA-Z0-9_]+/', $username)) {

                if (strlen($password) >= 6 && strlen($password) <= 60) {

                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                        $query = "INSERT INTO users VALUES (id, '$username', '$hashedPassword', '$email', 0)";

                        mysqli_query($link, $query);
                        echo "Success";
                    } else {
                        echo "Invalid email!";
                    }

                } else {
                    echo "Invalid password!";
                }

            } else {
                echo "Invalid username characters (a-z A-Z 0-0 is only allowed)";
            }

        } else {
            echo "Invalid username!";
        }

    } else {
        echo "User already exists! Check your login or email address";
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
