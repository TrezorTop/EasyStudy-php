<?php

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';

if (Login::isLoggedIn()) {
    $userId = Login::isLoggedIn();
} else {
    die ("Not logged in");
}

if (isset($_POST['send'])) {
    if (DB::query('SELECT id FROM users WHERE id=:receiver', array(':receiver'=>$_GET['receiver']))) {

        DB::query("INSERT INTO messages VALUES (id, :body, :sender, :receiver, 0)", array(':body'=>$_POST['body'], ':sender'=>$userId, ':receiver'=>htmlspecialchars($_GET['receiver'])));
        echo "Message sent!";
    } else {
        die('Invalid user\'s ID!');
    }

}

?>

<h1>Message</h1>

<form action="send_message.php?receiver=<?php echo htmlspecialchars($_GET['receiver']) ?>" method="post">
    <textarea name="body" cols="30" rows="8"></textarea>
    <input type="submit" name="send" value="Message">
</form>