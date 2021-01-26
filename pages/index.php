<?php

include $_SERVER['DOCUMENT_ROOT'] . '/php/connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';
$showTimeLine = False;

if (Login::isLoggedIn($link)) {
    $showTimeLine = True;
} else {
    echo "Not logged in";
}

$followingPosts = mysqli_query($link, 'SELECT posts.body, posts.likes, users.`username` FROM users, posts, followers 
                                             WHERE posts.user_id = followers.user_id
                                             AND users.id = posts.user_id
                                             AND follower_id = 2
                                             ORDER BY posts.likes DESC;');

foreach ($followingPosts as $post) {
    echo $post['body'] . " ~ " . $post['username'] . "<hr>";
}

?>
