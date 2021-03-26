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
    die("Not logged in");
}

if (isset($_POST['like']) or isset($_POST['unlike'])) {
    Post::likePost($_GET['postId'], $userId);
}
if (isset($_POST['comment'])) {
    Comment::postComment($_POST['comment-body'], $_GET['postId'], $userId);
}

if (isset($_POST['searchbox'])) {
    $toSearch = explode(" ", $_POST['searchbox']);
    if (count($toSearch) == 1) {
        $toSearch = str_split($toSearch[0], 2);
    }

    $whereClause = "";
    $paramsArray = array(':username' => '%' . $_POST['searchbox'] . '%');
    for ($i = 0; $i < count($toSearch); $i++) {
        $whereClause .= " OR username LIKE :u$i ";
        $paramsArray[":u$i"] = $toSearch[$i];
    }

    $users = DB::query("SELECT users.username FROM users WHERE users.username LIKE :username $whereClause", $paramsArray);
    print_r($users);

    $whereClause = "";
    $paramsArray = array(':body' => '%' . $_POST['searchbox'] . '%');
    for ($i = 0; $i < count($toSearch); $i++) {
        if ($i % 2) {
            $whereClause .= " OR body LIKE :p$i ";
            $paramsArray[":p$i"] = $toSearch[$i];
        }
    }
    $posts = DB::query("SELECT posts.body FROM posts WHERE posts.body LIKE :body $whereClause", $paramsArray);
    echo '<pre>';
    print_r($posts);
    echo '</pre>';
}


?>

    <form action="index.php" method="post">
        <input type="text" name="searchbox" value="">
        <input type="submit" name="search" value="Search">
    </form>

<?php

$followingPosts = DB::query('SELECT posts.id, posts.body, posts.likes, users.`username` FROM users, posts, followers
WHERE posts.user_id = followers.user_id
AND users.id = posts.user_id
AND follower_id = :userid', array(':userid' => $userId));

foreach ($followingPosts as $post) {

    echo $post['body'] . " ~ " . $post['username'];
    echo "<form action='index.php?postId=" . $post['id'] . "' method='post'>";

    if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid' => $post['id'], ':userid' => $userId))) {

        echo "<input type='submit' name='like' value='Like'>";
    } else {
        echo "<input type='submit' name='unlike' value='Unlike'>";
    }
    echo "<span>" . $post['likes'] . " likes</span>
        </form>
        <form action='index.php?postId=" . $post['id'] . "' method='post'>
        <textarea name='comment-body' rows='3' cols='50'></textarea>
        <input type='submit' name='comment' value='Comment'>
        </form>
        ";
    echo "<div>";
    Comment::displayComments($post['id']);
    echo "</div>";
    echo "<hr /></br />";


}
