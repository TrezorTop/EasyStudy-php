<?php
require_once("DB.php");
require_once("Mail.php");

$db = new DB("127.0.0.1", "social_network", "root", "root");

if ($_SERVER['REQUEST_METHOD'] == "GET") {

    if ($_GET['url'] == "m_users") {

        $token = $_COOKIE['SNID'];
        $userId = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token' => sha1($token)))[0]['user_id'];

        $users = $db->query('SELECT s.username AS Sender, r.username AS Receiver, s.id AS SenderId, r.id AS ReceiverId
                    FROM messages
                    LEFT JOIN users s ON s.id = messages.sender
                    LEFT JOIN users r ON r.id = messages.receiver
                    WHERE (s.id = :userid OR r.id = :userid)', array(":userid" => $userId));

        $u = array();
        foreach ($users as $user) {
            if (!in_array(array('username' => $user['Receiver'], 'id' => $user['ReceiverId']), $u)) {
                array_push($u, array('username' => $user['Receiver'], 'id' => $user['ReceiverId']));
            }
            if (!in_array(array('username' => $user['Sender'], 'id' => $user['SenderId']), $u)) {
                array_push($u, array('username' => $user['Sender'], 'id' => $user['SenderId']));
            }
        }

        echo json_encode($u);


    } else if ($_GET['url'] == "auth") {

    } else if ($_GET['url'] == "messages") {

        $sender = $_GET['sender'];
        $token = $_COOKIE['SNID'];
        $receiver = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token' => sha1($token)))[0]['user_id'];

        $messages = $db->query('SELECT messages.id, messages.body, s.username AS Sender, r.username AS Receiver 
                                FROM messages 
                                LEFT JOIN users s ON messages.sender = s.id 
                                LEFT JOIN users r ON messages.receiver = r.id 
                                WHERE (r.id=:r AND s.id=:s) OR r.id=:s AND s.id=:r', array(':r' => $receiver, ':s' => $sender));

        echo json_encode($messages);

    } else if ($_GET['url'] == "search") {

        $toSearch = explode(" ", $_GET['query']);
        if (count($toSearch) == 1) {
            $toSearch = str_split($toSearch[0], 2);
        }

        $whereClause = "";
        $paramsArray = array(':body' => '%' . $_GET['query'] . '%');
        for ($i = 0; $i < count($toSearch); $i++) {
            if ($i % 2) {
                $whereClause .= " OR body LIKE :p$i ";
                $paramsArray[":p$i"] = $toSearch[$i];
            }
        }
        $posts = $db->query("SELECT posts.id, posts.body, users.username, posts.posted_at FROM posts, users WHERE users.id = posts.user_id AND posts.body LIKE :body $whereClause", $paramsArray);

        echo json_encode($posts);

    } else if ($_GET['url'] == "users") {

        $token = $_COOKIE['SNID'];
        $user_id = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token' => sha1($token)))[0]['user_id'];
        $username = $db->query('SELECT username FROM users WHERE id=:userid', array(':userid' => $user_id))[0]['username'];
        echo $username;

    } else if ($_GET['url'] == "comments" && isset($_GET['postId'])) {

        $output = "";
        $comments = $db->query('SELECT comments.comment, users.username FROM comments, users WHERE post_id = :postid AND comments.user_id = users.id', array(':postid' => $_GET['postId']));

        $output .= "[";
        foreach ($comments as $comment) {
            $output .= "{";
            $output .= '"Comment": "' . $comment['comment'] . '",';
            $output .= '"CommentedBy": "' . $comment['username'] . '"';
            $output .= "},";
            //echo $comment['comment']." ~ ".$comment['username']."<hr />";
        }
        $output = substr($output, 0, strlen($output) - 1);
        $output .= "]";
        if (count($comments) != 0) {
            echo $output;
        } else {
            $output = "";
            $output .= "[";
            $output .= "]";

            echo $output;
        }

    } else if ($_GET['url'] == "posts") {

        $token = $_COOKIE['SNID'];

        $userId = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token' => sha1($token)))[0]['user_id'];

        $followingPosts = $db->query('SELECT posts.id, posts.body, posts.posted_at, posts.postimg, posts.likes, users.`username` FROM users, posts, followers
                                     WHERE (posts.user_id = followers.user_id
                                     OR posts.user_id = :userid) 
                                     AND users.id = posts.user_id
                                     AND follower_id = :userid
                                     ORDER BY posts.posted_at DESC;', array(':userid' => $userId), array(':userid' => $userId));

        $response = "[";
        foreach ($followingPosts as $post) {

            $response .= "{";

            $response .= '"PostId": ' . $post['id'] . ',';
            $response .= '"PostBody": "' . $post['body'] . '",';
            $response .= '"PostedBy": "' . $post['username'] . '",';
            $response .= '"PostDate": "' . $post['posted_at'] . '",';
            $response .= '"PostImage": "' . $post['postimg'] . '",';
            $response .= '"Likes": ' . $post['likes'] . '';

            $response .= "},";


        }

        $response = substr($response, 0, strlen($response) - 1);
        $response .= "]";

        echo $response;

    } else if ($_GET['url'] == "profileposts") {

        $start = (int)$_GET['start'];
        $userId = $db->query('SELECT id FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['id'];

//        echo 'userid is' . $_GET['username'] . '<br>';

        $followingPosts = $db->query('SELECT posts.id, posts.body, posts.posted_at, posts.postimg, posts.likes, users.`username` FROM users, posts
                                     WHERE users.id = posts.user_id
                                     AND users.id = :userid
                                     ORDER BY posts.posted_at DESC LIMIT 5 OFFSET ' . $start . ';', array(':userid' => $userId));

        $response = "[";
        foreach ($followingPosts as $post) {

            $response .= "{";
            $response .= '"PostId": ' . $post['id'] . ',';
            $response .= '"PostBody": "' . $post['body'] . '",';
            $response .= '"PostedBy": "' . $post['username'] . '",';
            $response .= '"PostDate": "' . $post['posted_at'] . '",';
            $response .= '"PostImage": "' . $post['postimg'] . '",';
            $response .= '"Likes": ' . $post['likes'] . '';
            $response .= "},";


        }

        $response = substr($response, 0, strlen($response) - 1);
        $response .= "]";

        http_response_code(200);
        echo $response;

    }

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {

    if (isset($_COOKIE['SNID'])) {
        $token = $_COOKIE['SNID'];
    } else {
        die();
    }

    $userId = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token' => sha1($token)))[0]['user_id'];

    $postBody = file_get_contents("php://input");
    $postBody = json_decode($postBody);

    $body = $postBody->body;
    $receiver = $postBody->receiver;

    if (strlen($body) > 100) {
        echo "{ 'Error': 'Message too long!' }";
    }
    if ($body == null) {
        $body = "";
    }
    if ($receiver == null) {
        die();
    }
    if ($userId == null) {
        die();
    }

    $db->query("INSERT INTO messages VALUES(id, :body, :sender, :receiver, '0')", array(':body' => $body, ':sender' => $userId, ':receiver' => $receiver));

    echo '{ "Success": "Message Sent!" }';

    if ($_GET['url'] == "message") {


    } else if ($_GET['url'] == "users") {
        $postBody = file_get_contents("php://input");
        $postBody = json_decode($postBody);

        $username = $postBody->username;
        $email = $postBody->email;
        $password = $postBody->password;


        if (!$db->query('SELECT username FROM users WHERE username=:username', array(':username' => $username))) {
            if (strlen($username) >= 3 && strlen($username) <= 60) {
                if (preg_match('/[a-zA-Z0-9_]+/', $username)) {
                    if (strlen($password) >= 6 && strlen($password) <= 60) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            if (!$db->query('SELECT email FROM users WHERE email=:email', array(':email' => $email))) {
                                $db->query('INSERT INTO users VALUES (id, :username, :password, :email, \'0\', NULL)', array(':username' => $username, ':password' => password_hash($password, PASSWORD_BCRYPT), ':email' => $email));

                                Mail::sendMail('Добро пожаловать в EasyStudy!', 'Ваш аккаунт был зарегистрирован!', $email);

                                echo '{ "Success": "User Created!" }';
                                http_response_code(200);
                            } else {
                                echo '{ "Error": "Email in use!" }';
                                http_response_code(409);
                            }
                        } else {
                            echo '{ "Error": "Invalid email" }';
                            http_response_code(409);
                        }
                    } else {
                        echo '{ "Error": "Invalid password!" }';
                        http_response_code(409);
                    }
                } else {
                    echo '{ "Error": "Invalid username(a-z, A-Z, 0-9 are only allowed)" }';
                    http_response_code(409);
                }
            } else {
                echo '{ "Error": "Invalid username!" }';
                http_response_code(409);
            }
        } else {
            echo '{ "Error": "User already exists!" }';
            http_response_code(409);
        }
    }

    if ($_GET['url'] == "auth") {
        $postBody = file_get_contents("php://input");
        $postBody = json_decode($postBody);

        $username = $postBody->username;
        $password = $postBody->password;

        if ($db->query('SELECT username FROM users WHERE username=:username', array(':username' => $username))) {
            if (password_verify($password, $db->query('SELECT password FROM users WHERE username=:username', array(':username' => $username))[0]['password'])) {
                $cstrong = True;
                $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
                $user_id = $db->query('SELECT id FROM users WHERE username=:username', array(':username' => $username))[0]['id'];
                $db->query('INSERT INTO login_tokens VALUES (id, :token, :user_id)', array(':token' => sha1($token), ':user_id' => $user_id));
                echo '{ "Token": "' . $token . '" }';
            } else {
                echo '{ "Error": "Invalid username or password!" }';
                http_response_code(401);
            }
        } else {
            echo '{ "Error": "Invalid username or password!" }';
            http_response_code(401);
        }

    } else if ($_GET['url'] == "likes") {

        $postId = $_GET['id'];

        $token = $_COOKIE['SNID'];
        $likerId = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token' => sha1($token)))[0]['user_id'];

        if (!$db->query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid' => $postId, ':userid' => $likerId))) {
            $db->query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid' => $postId));
            $db->query('INSERT INTO post_likes VALUES (id, :postid, :userid)', array(':postid' => $postId, ':userid' => $likerId));
//            Notify::createNotify("", $postId);
        } else {
            $db->query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid' => $postId));
            $db->query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid' => $postId, ':userid' => $likerId));
        }

        echo "{";
        echo '"Likes":';
        echo $db->query('SELECT likes FROM posts WHERE id=:postid', array(':postid' => $postId))[0]['likes'];
        echo "}";
    }


} else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
    if ($_GET['url'] == "auth") {
        if (isset($_GET['token'])) {
            if ($db->query("SELECT token FROM login_tokens WHERE token=:token", array(':token' => sha1($_GET['token'])))) {
                $db->query('DELETE FROM login_tokens WHERE token=:token', array(':token' => sha1($_GET['token'])));
                echo '{ "Status": "Success" }';
                http_response_code(200);
            } else {
                echo '{ "Error": "Invalid token" }';
                http_response_code(400);
            }
        } else {
            echo '{ "Error": "Malformed request" }';
            http_response_code(400);
        }
    }
} else {
    http_response_code(405);
}
?>