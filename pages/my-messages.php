<?php

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';

if (Login::isLoggedIn()) {
    $userId = Login::isLoggedIn();
} else {
    die ("Not logged in");
}

if (isset($_GET['messageId'])) {
    $message = DB::query('SELECT * FROM messages WHERE id=:messageid AND receiver=:receiver OR sender=:sender', array(':messageid' => $_GET['messageId'], ':receiver' => $userId, ':sender' => $userId))[0];
    echo '<h1>View message</h1>';
    echo htmlspecialchars($message['body']);
    echo '<hr>';

    if ($message['sender'] == $userId) {
        $id = $message['receiver'];
    } else {
        $id = $message['sender'];
    }
    DB::query('UPDATE messages SET `is_read`=1 WHERE id=:messageid', array(':messageid' => $_GET['messageId']))

    ?>

    <form action="send-message.php?receiver=<?php echo $id ?>" method="post">
        <textarea name="body" cols="30" rows="8"></textarea>
        <input type="submit" name="send" value="Message">
    </form>

    <?php

} else {

    ?>

    <h1>My messages</h1>

    <?php

    $messages = DB::query('SELECT messages.*, users.username FROM messages, users WHERE (receiver=:receiver OR sender=:sender) AND users.id = messages.sender', array(':receiver' => $userId, ':sender' => $userId));

    foreach ($messages as $message) {

        if (strlen($message['body']) > 20) {
            $messageBody = substr($message['body'], 0, 10) . " ...";
        } else {
            $messageBody = $message['body'];
        }

        if ($message['is_read'] == 0) {
            echo "<a href='my-messages.php?messageId=" . $message['id'] . "'><strong>" . $messageBody . "</strong></a> sent by " . $message['username'] . '<hr />';
        } else {
            echo "<a href='my-messages.php?messageId=" . $message['id'] . "'>" . $messageBody . "</a> sent by " . $message['username'] . '<hr />';
        }

    }
}

?>