<?php

session_start();

$crypto_strong = True;
$token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));

if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = $token;
}

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';

if (Login::isLoggedIn()) {
    $userId = Login::isLoggedIn();
} else {
    die ("Not logged in");
}

if (isset($_POST['send'])) {

    if (!isset($_POST['nocsrf'])) {
        die("INVALID TOKEN");
    }

    if ($_POST['nocsrf'] != $_SESSION['token']) {
        die("INVALID TOKEN");
    }

    if (DB::query('SELECT id FROM users WHERE id=:receiver', array(':receiver' => $_GET['receiver']))) {

        DB::query("INSERT INTO messages VALUES (id, :body, :sender, :receiver, 0)", array(':body' => $_POST['body'], ':sender' => $userId, ':receiver' => htmlspecialchars($_GET['receiver'])));
        echo "Message sent!";
    } else {
        die('Invalid user\'s ID!');
    }
    session_destroy();

}

?>

<h1>Message</h1>

<form action="send-message.php?receiver=<?php echo htmlspecialchars($_GET['receiver']) ?>" method="post">
    <textarea name="body" cols="30" rows="8"></textarea>
    <input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" name="send" value="Message">
</form>