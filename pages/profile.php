<?php

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Post.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Image.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Notify.php';

$test = DB::query('SELECT comments.comment, users.username FROM comments, users WHERE post_id = :postid AND comments.user_id = users.id', array('postid'=>6));

if (count($test) != 0) {
    echo 'is empty';
}

$username = "";
$verified = False;
$isFollowing = False;

if (isset($_GET['username'])) {

    if (DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $_GET['username']))) {

        $username = DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['username'];
        $userId = DB::query('SELECT id FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['id'];
        $verified = DB::query('SELECT verified FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['verified'];
        $followerId = Login::isLoggedIn();

        if (isset($_POST['follow'])) {
            if ($userId != $followerId) {
                if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userId, ':followerid' => $followerId))) {
                    if ($followerId == 1) {
                        DB::query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid' => $userId));
                    }
                    DB::query('INSERT INTO followers VALUES (id, :userid, :followerid)', array(':userid' => $userId, ':followerid' => $followerId));
                } else {
                    echo "Already following";
                }
                $isFollowing = True;
            }
        }

        if (isset($_POST['unfollow'])) {
            if ($userId != $followerId) {
                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userId, ':followerid' => $followerId))) {
                    if ($followerId == 1) {
                        DB::query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid' => $userId));
                    }
                    DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userId, ':followerid' => $followerId));
                }
                $isFollowing = False;
            }

        }

        if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userId, ':followerid' => $followerId))) {
//            echo "Already following";
            $isFollowing = True;
        }

        if (isset($_POST['delete-post'])) {
            if (DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid' => $_GET['postId'], ':userid' => $followerId))) {
                DB::query('DELETE FROM posts WHERE id=:postid AND user_id=:userid', array(':postid' => $_GET['postId'], ':userid' => $followerId));
                DB::query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid' => $_GET['postId']));
                echo 'Post deleted';
            }
        }

        if (isset($_POST['post'])) {
            if ($_FILES['postimg']['size'] == 0) {
                Post::createPost($_POST['post-body'], Login::isLoggedIn(), $userId);
            } else {
                $postId = Post::createImagePost($_POST['post-body'], Login::isLoggedIn(), $userId);
                Image::uploadImage('postimg', "UPDATE posts SET postimg=:postimg WHERE id=:postid", array(':postid' => $postId));
            }
        }

        if (isset($_GET['postId']) && !isset($_POST['delete-post'])) {
            Post::likePost($_GET['postId'], $followerId);
        }

        $posts = Post::displayPosts($userId, $username, $followerId);

    } else {
        die("Username not found!");
    }

}

?>

<!-- <form action="profile.php?username=<?php echo $username ?>" method="post" enctype="multipart/form-data">
    <textarea name="post-body" cols="30" rows="8"></textarea>
    <br> Upload an image:
    <input type="file" name="postimg">
    <input type="submit" name="post" value="Post">
</form> -->

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Network</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/Footer-Dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Clean.css">
    <link rel="stylesheet" href="assets/css/Navigation-Clean1.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/untitled.css">
</head>

<body>
<header class="hidden-sm hidden-md hidden-lg">
    <div class="searchbox">
        <form>
            <h1 class="text-left">Social Network</h1>
            <div class="searchbox"><i class="glyphicon glyphicon-search"></i>
                <input class="form-control" type="text">
            </div>
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button">
                    MENU <span class="caret"></span></button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <li role="presentation"><a href="#">My Profile</a></li>
                    <li class="divider" role="presentation"></li>
                    <li role="presentation"><a href="#">Timeline </a></li>
                    <li role="presentation"><a href="#">Messages </a></li>
                    <li role="presentation"><a href="#">Notifications </a></li>
                    <li role="presentation"><a href="#">My Account</a></li>
                    <li role="presentation"><a href="#">Logout </a></li>
                </ul>
            </div>
        </form>
    </div>
    <hr>
</header>
<div>
    <nav class="navbar navbar-default hidden-xs navigation-clean">
        <div class="container">
            <div class="navbar-header"><a class="navbar-brand navbar-link" href="#"><i
                            class="icon ion-ios-navigate"></i></a>
                <button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navcol-1"><span
                            class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span
                            class="icon-bar"></span><span class="icon-bar"></span></button>
            </div>
            <div class="collapse navbar-collapse" id="navcol-1">
                <form class="navbar-form navbar-left">
                    <div class="searchbox"><i class="glyphicon glyphicon-search"></i>
                        <input class="form-control" type="text">
                    </div>
                </form>
                <ul class="nav navbar-nav hidden-md hidden-lg navbar-right">
                    <li role="presentation"><a href="#">My Timeline</a></li>
                    <li class="dropdown open"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"
                                                 href="#">User <span class="caret"></span></a>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li role="presentation"><a href="#">My Profile</a></li>
                            <li class="divider" role="presentation"></li>
                            <li role="presentation"><a href="#">Timeline </a></li>
                            <li role="presentation"><a href="#">Messages </a></li>
                            <li role="presentation"><a href="#">Notifications </a></li>
                            <li role="presentation"><a href="#">My Account</a></li>
                            <li role="presentation"><a href="#">Logout </a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav hidden-xs hidden-sm navbar-right">
                    <li class="active" role="presentation"><a href="#">Timeline</a></li>
                    <li role="presentation"><a href="#">Messages</a></li>
                    <li role="presentation"><a href="#">Notifications</a></li>
                    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"
                                            href="#">User <span class="caret"></span></a>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li role="presentation"><a href="#">My Profile</a></li>
                            <li class="divider" role="presentation"></li>
                            <li role="presentation"><a href="#">Timeline </a></li>
                            <li role="presentation"><a href="#">Messages </a></li>
                            <li role="presentation"><a href="#">Notifications </a></li>
                            <li role="presentation"><a href="#">My Account</a></li>
                            <li role="presentation"><a href="#">Logout </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
<div class="container">
    <h1><?php echo $username ?>'s Profile <?php if ($verified) {
            echo '<i class="glyphicon glyphicon-ok-sign verified" data-toggle="tooltip"
                title="Verified User"
                style="font-size:28px;color:#da052b;"></i>';
        } ?> </h1></div>
<div>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <ul class="list-group">
                    <li class="list-group-item"><span><strong>About Me</strong></span>
                        <p>Welcome to my profile Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium alias dolore eligendi enim eos est et explicabo, impedit nostrum numquam officiis possimus quasi quia quibusdam repudiandae saepe, ut velit vero!</p>
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-group">
                    <div class="timeline-posts">

                    </div>
                </ul>
            </div>
            <div class="col-md-3">
                <button class="btn btn-default" type="button"
                        style="width:100%;background-image:url(&quot;none&quot;);background-color:#da052b;color:#fff;padding:16px 32px;margin:0px 0px 6px;border:none;box-shadow:none;text-shadow:none;opacity:0.9;text-transform:uppercase;font-weight:bold;font-size:13px;letter-spacing:0.4px;line-height:1;outline:none;">
                    NEW POST
                </button>
                <ul class="list-group"></ul>
            </div>
        </div>
    </div>
</div>
<div class="footer-dark">
    <footer>
        <div class="container">
            <p class="copyright">FOOTER</p>
        </div>
    </footer>
</div>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/bs-animation.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $.ajax({

            type: "GET",
            url: "../api/profileposts?username=<?php echo $username?>",
            processData: false,
            contentType: "application/json",
            data: '',
            success: function (r) {
                var posts = JSON.parse(r);
                $.each(posts, function (index) {
                    $('.timeline-posts').html(
                        $('.timeline-posts').html() +

                        '<li class="list-group-item">\n' +
                        '    <blockquote>\n' +
                        '        <p>' + posts[index].PostBody + '</p>\n' +
                        '        <footer>Posted by ' + posts[index].PostedBy + ' on ' + posts[index].PostDate + '\n' +
                        '            <button class="btn btn-default" type="button"\n' +
                        '                    style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;" data-id="' + posts[index].PostId + '">\n' +
                        '            <span><i class="glyphicon glyphicon-heart" data-aos="flip-right"></i>' + posts[index].Likes + '</span>\n' +
                        '            </button>\n' +
                        '            <button class="btn btn-default comment comment-btn" type="button"\n' +
                        '                    data-postid="' + posts[index].PostId + '" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;">\n' +
                        '                <i class="glyphicon glyphicon-flash" style="color:#f9d616;"></i><span\n' +
                        '                    style="color:#f9d616;">Comments</span></button>\n' +
                        '        </footer>\n' +
                        '        <div style="display: none;"></div>' +
                        '    </blockquote>\n' +
                        '</li>'
                    )

                    $('[data-postid]').click(function () {
                        var buttonId = $(this).attr('data-postid');
                        var commentElement = $(this).parent().parent().children().last();

                        $.ajax({

                            type: "GET",
                            url: "../api/comments?postId=" + $(this).attr('data-postid'),
                            processData: false,
                            contentType: "application/json",
                            data: '',
                            success: function (r) {
                                var res = JSON.parse(r)

                                showCommentsSection(res, commentElement);
                            },
                            error: function (r) {
                                console.log(r)
                            }
                        });
                    })

                    $('[data-id]').click(function () {
                        var buttonId = $(this).attr('data-id');
                        $.ajax({

                            type: "POST",
                            url: "../api/likes?id=" + $(this).attr('data-id'),
                            processData: false,
                            contentType: "application/json",
                            data: '',
                            success: function (r) {
                                var res = JSON.parse(r)
                                $("[data-id='" + buttonId + "']").html('<span><i class="glyphicon glyphicon-heart" data-aos="flip-right"></i>' + res.Likes + '</span>')
                            },
                            error: function (r) {
                                console.log(r)
                            }
                        });
                    })

                    $('.comment-btn').click(function () {
                        $(this).parent().parent().children().last().toggle();
                    })
                })
            },
            error: function (r) {
                console.log(r)
            }

        });

        function showCommentsSection(res, commentElement) {
            var output = ""
            for (let i = 0; i < res.length; i++) {
                output += res[i].Comment;
                output += " ~ ";
                output += res[i].CommentedBy;
                output += "<hr>"
            }

            $(commentElement).html(output);
            console.log(res);
        }

    });
</script>
</body>

</html>