<?php

include $_SERVER['DOCUMENT_ROOT'] . '/php/connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';

$username = "";
$verified = False;
$isFollowing = False;

if (isset($_GET['username'])) {

    $getUsername = $_GET['username'];
    $usernameMysqlResult = mysqli_query($link, "SELECT username FROM `users` WHERE username = '$getUsername'");


    if (mysqli_num_rows($usernameMysqlResult) > 0) {

        $username = mysqli_fetch_row($usernameMysqlResult)[0];
        $userIdResult = mysqli_query($link, "SELECT id FROM `users` WHERE username = '$getUsername'");
        $userId = mysqli_fetch_row($userIdResult)[0];
        $verifiedResult = mysqli_query($link, "SELECT verified FROM `users` WHERE username = '$username'");
        $verified = mysqli_fetch_row($verifiedResult)[0];
        $followerId = Login::isLoggedIn($link);


        if (isset($_POST['follow'])) {

            if ($userId != $followerId) {

                $followerIdResult = mysqli_query($link, "SELECT follower_id FROM `followers` WHERE user_id = '$userId' AND follower_id = '$followerId'");
                if (mysqli_num_rows($followerIdResult) == 0) {

                    if ($followerId == 1) {

                        mysqli_query($link, "UPDATE `users` SET verified = 1 WHERE id = $userId");
                    }
                    mysqli_query($link, "INSERT INTO `followers` VALUES (id, '$userId', '$followerId')");
                } else {
                    echo "Already following";
                }
                $isFollowing = True;
            }

        }

        if (isset($_POST['unfollow'])) {

            if ($userId != $followerId) {

                $followerIdResult = mysqli_query($link, "SELECT follower_id FROM `followers` WHERE user_id = '$userId' AND follower_id = '$followerId'");
                if (mysqli_num_rows($followerIdResult) > 0) {

                    if ($followerId == 1) {
                        mysqli_query($link, "UPDATE `users` SET verified = 0 WHERE id = $userId");
                    }
                    mysqli_query($link, "DELETE FROM `followers` WHERE user_id = '$userId' AND follower_id = '$followerId'");
                }
                $isFollowing = False;
            }

        }

        $followerIdResult = mysqli_query($link, "SELECT follower_id FROM `followers` WHERE user_id = '$userId'");
        if (mysqli_num_rows($followerIdResult) > 0) {
//            echo "Already following";
            $isFollowing = True;
        }

        if (isset($_POST['post'])) {
            $postBody = $_POST['post-body'];
            $userId = Login::isLoggedIn($link);

            if (strlen($postBody) < 1 || strlen($postBody) > 257) {
                die('Incorrect length!');
            }

            mysqli_query($link, "INSERT INTO `posts` VALUES (id, '$postBody', NOW(), '$userId', 0)");
        }

        $dbPostsResult = mysqli_query($link, "SELECT * FROM `posts` WHERE user_id = '$userId' ORDER BY id DESC");

        $posts = "";

        foreach ($dbPostsResult as $post) {
            $posts .= $post['body'] . "<hr> <br>";
        }

    } else {
        die("Username not found!");
    }

}

?>

<h1><?php echo $username ?>'s Profile <?php if ($verified) {
        echo "Verified";
    } ?></h1>

<form action="profile.php?username=<?php echo $username ?>" method="post">
    <?php

    if ($userId != $followerId && $followerId != null) {

        if ($isFollowing) {
            echo '<input type="submit" name="unfollow" value="Unfollow">';
        } else {
            echo '<input type="submit" name="follow" value="Follow">';
        }
    }
    ?>
</form>

<form action="profile.php?username=<?php echo $username ?>" method="post">
    <textarea name="post-body" cols="30" rows="8"></textarea>
    <input type="submit" name="post" value="Post">
</form>

<div class="posts">
    <?php echo $posts; ?>
</div>