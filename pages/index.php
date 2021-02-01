<?php

include $_SERVER['DOCUMENT_ROOT'] . '/php/connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Post.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Comment.php';
$showTimeLine = False;

if (Login::isLoggedIn($link)) {
    $userId = Login::isLoggedIn($link);
    $showTimeLine = True;
} else {
    echo "Not logged in";
}

if (isset($_GET['postId'])) {
    Post::likePost($link, $_GET['postId'], $userId);
}
if (isset($_POST['comment'])) {
    Comment::postComment($link, $_POST['comment-body'], $_GET['postCommentId'], $userId);

}

$followingPosts = mysqli_query($link, 'SELECT posts.id, posts.body, posts.likes, users.`username` FROM users, posts, followers 
                                             WHERE posts.user_id = followers.user_id
                                             AND users.id = posts.user_id
                                             AND follower_id = 2
                                             ORDER BY posts.likes DESC;');

foreach ($followingPosts as $post) {

    $postId = $post['id'];

    $postIdResult = mysqli_query($link, "SELECT post_id FROM post_likes WHERE post_id = '$postId' AND user_id = '$userId'");

    echo $post['body'] . " ~ " . $post['username'];
    echo "<form action=\"index.php?postId=" . $post['id'] . "\" method=\"post\">";


    if (mysqli_num_rows($postIdResult) == 0) {
        echo "<input type=\"submit\" name=\"like\" value =\"Like\">";
    } else {
        echo "<input type=\"submit\" name=\"unlike\" value =\"Unlike\">";
    }
    echo "<span> " . $post['likes'] . " likes </span>
          </form>
            <form action=\"index.php?postCommentId=" . $post['id'] . "\" method=\"post\">
                <textarea name=\"comment-body\" cols=\"30\" rows=\"3\"></textarea>
                <input type=\"submit\" name=\"comment\" value=\"Comment\">
            </form>
          <hr><br>";

}

?>
