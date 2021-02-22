<?php

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Post.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Comment.php';
$showTimeLine = False;

if (Login::isLoggedIn()) {
    $userId = Login::isLoggedIn();
    $showTimeLine = True;
} else {
    echo "Not logged in";
}

if (isset($_GET['postId'])) {
    Post::likePost($_GET['postId'], $userId);
}
if (isset($_POST['comment'])) {
    Comment::postComment($_POST['comment-body'], $_GET['postId'], $userId);
}

$followingPosts = DB::query('SELECT posts.id, posts.body, posts.likes, users.`username` FROM users, posts, followers
WHERE posts.user_id = followers.user_id
AND users.id = posts.user_id
AND follower_id = :userid
ORDER BY posts.likes DESC;', array(':userid' => $userId));

foreach ($followingPosts as $post) {

    echo $post['body'] . " ~ " . $post['username'];
    echo "<form action=\"index.php?postId=" . $post['id'] . "\" method=\"post\">";

    if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid' => $post['id'], ':userid' => $userId))) {
        echo "<input type=\"submit\" name=\"like\" value =\"Like\">";
    } else {
        echo "<input type=\"submit\" name=\"unlike\" value =\"Unlike\">";
    }
    echo "<span> " . $post['likes'] . " likes </span>
          </form>
            <form action=\"index.php?postId=" . $post['id'] . "\" method=\"post\">
                <textarea name=\"comment-body\" cols=\"30\" rows=\"3\"></textarea>
                <input type=\"submit\" name=\"comment\" value=\"Comment\">
            </form>
            ";

    Comment::displayComments($post['id']);

    echo "
          <hr><br>";

}
