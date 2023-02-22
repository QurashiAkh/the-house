<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if (!isset($_SESSION['theme'])) {
        $_SESSION['theme'] = "light";
    };
    if ($_SESSION['theme'] == "light") {
        echo '<link rel="stylesheet" type="text/css" href="static/css/light-theme.css">';
    } else {
        echo '<link rel="stylesheet" type="text/css" href="static/css/dark-theme.css">';
    } ?>
    <link rel="icon" type="image/png" href="static/images/favicon.png" />
    <title>User • The House</title>
</head>

<body>
    <table cellpadding="0" cellspacing="0" width="100%" class="top-bar">
        <tr>
            <td>
                <p><b><a href="index.php">The House</a><a href='about.php'>¹</a></b>
                    <?php
                    include_once('library/connect.php');

                    if (!isset($_SESSION['uid'])) {
                        echo "</p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <a href='login.php'>login</a></td>";
                    } else {
                        $sql6 = "SELECT * FROM users WHERE id='" . $_SESSION['uid'] . "' LIMIT 1;";
                        $res6 = mysqli_query($connection, $sql6) or die(mysqli_error($connection));
                        $row6 = mysqli_fetch_assoc($res6);

                        echo " | <b><a href='user.php?username=" . $_SESSION['username'] . "'>" . $_SESSION['username'] . "</a></b></p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <img class='tiny-pfp' src='" . $row6['picture_url'] . "' alt='User Profile Picture' /> <a href='account.php'>account</a> | <a href='actions/logout_process.php'>logout</a></td>";
                    }
                    ?>
        </tr>
    </table>
    <div class="main">
        <?php
        $username = mysqli_escape_string($connection, $_GET['username']);
        $sql = "SELECT * FROM users WHERE username='" . $username . "' LIMIT 1;";
        $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));

        if (mysqli_num_rows($res) == 1) {
            $user = mysqli_fetch_assoc($res);

            $sql2 = "SELECT * FROM posts WHERE post_author='" . $user['id'] . "' ORDER BY post_date DESC;";
            $res2 = mysqli_query($connection, $sql2) or die(mysqli_error($connection));
            $numberOfPosts = mysqli_num_rows($res2);
            $postOrWhat = "No posts yet";

            if ($numberOfPosts == 0) {
                $postOrWhat = "No posts yet.";
            } elseif ($numberOfPosts == 1) {
                $postOrWhat = $numberOfPosts . " post";
            } else {
                $postOrWhat = $numberOfPosts . " posts";
            }

            $sql5 = "SELECT * FROM users WHERE username='" . $_GET['username'] . "' LIMIT 1;";
            $res5 = mysqli_query($connection, $sql5) or die(mysqli_error($connection));
            $row5 = mysqli_fetch_assoc($res5);

            $joined_at = $row5['joined_at'];

            if ($user['role'] == 'admin') {
                $role = "The Owner of the House";
            } elseif ($user['role'] == 'moderator') {
                $role = "Moderator";
            } else {
                $role = "User";
            }

            echo "
            <p>User: <b>" . $user['username'] . "</b> | " . $role . "
            <p style='color: #808080; font-size: 13px;'>" . $postOrWhat . "</p></p>
            <p style='color: #808080; font-size: 13px; margin-bottom: 12px;'>joined the House at: " . $joined_at . "</p>";

            if ($user['bio'] == "") {
                echo "<p><b>Bio not set!</b></p>";
            } else {
                echo "<p style='color: #808080; font-size: 13px;'>Bio:</p><p>" . $user['bio'] . "</p>";
            }

            echo "<br />";

            if ($user['picture_url'] == "") {
                echo "<img src='/uploads/profiles/default/user.png' style='max-width: 300px;' />";
            } else {
                echo "<img src='" . $user['picture_url'] . "' style='max-width: 300px;' />";
            }

            if ($numberOfPosts > 0) {
                echo "<br /><br />Recent Posts:<br /><hr />";

                while ($post = mysqli_fetch_assoc($res2)) {
                    echo "
                    <div class='comment'>
                        <div class='top-comment'>
                            <div class='tooltip-wrap'>
                                <a style='color: #808080' href='user.php?username=" . $user['username'] . "'>" . $user['username'] . "</a>
                                <div class='tooltip-content'>
                                    <p><a href='user.php?username=" . $user['username'] . "'>" . $user['username'] . "</a> | " . $user['role'] . " | " . $postOrWhat . "</p>";

                    if ($user['bio'] != "") {
                        echo "<p style='color: #808080; font-size: 13px;'>Bio:</p><p class='bio'>" . $user['bio'] . "</p>";
                    }

                    if ($user['picture_url'] == "") {
                        echo "<img src='/uploads/profiles/default/user.png' style='max-width: 160px;' />";
                    } else {
                        echo "<img src='" . $user['picture_url'] . "' style='max-width: 160px;' />";
                    }

                    echo "</div>
                            </div>
                            <p class='comment-ts'>
                                <a style='color: #808080' href='view_thread.php?cid=" . $post['category_id'] . "&tid=" . $post['thread_id'] . "&pid=" . $post['id'] . "#" . $post['id'] . "'>
                                    " . $post['post_date'] . "
                                </a>
                            </p>";

                    if (isset($_SESSION['uid'])) {
                        $sql4 = "SELECT * FROM users WHERE id='" . $_SESSION['uid'] . "' LIMIT 1;";
                        $res4 = mysqli_query($connection, $sql4) or die(mysqli_error($connection));
                        $row4 = mysqli_fetch_assoc($res4);

                        if ($row4['role'] == "user") {
                            if ($user['id'] == $_SESSION['uid']) {
                                echo "<p class='comment-delete'><a href='delete_post.php?cid=" . $post['category_id'] . "&tid=" . $post['thread_id'] . "&pid=" . $post['id'] . "'>[delete]</a></p>";
                                echo "</div><div class='comment-content'>" . $post['post_content'] . "</div>";
                            } else {
                                echo "</div><div class='comment-content'>" . $post['post_content'] . "</div>";
                            }
                        } elseif ($row4['role'] == "moderator") {
                            if ($user['role'] == "admin") {
                                echo "</div><div class='comment-content'>" . $post['post_content'] . "</div>";
                            } else {
                                echo "<p class='comment-delete'><a href='delete_post.php?cid=" . $post['category_id'] . "&tid=" . $post['thread_id'] . "&pid=" . $post['id'] . "'>[delete]</a></p>";
                                echo "</div><div class='comment-content'>" . $post['post_content'] . "</div>";
                            }
                        } elseif ($row4['role'] == "admin") {
                            echo "<p class='comment-delete'><a href='delete_post.php?cid=" . $post['category_id'] . "&tid=" . $post['thread_id'] . "&pid=" . $post['id'] . "'>[delete]</a></p>";
                            echo "</div><div class='comment-content'>" . $post['post_content'] . "</div>";
                        }
                    } else {
                        echo "</div><div class='comment-content'>" . $post['post_content'] . "</div>";
                    }

                    if ($post['post_file_link'] != "") {
                        // if (str_ends_with($post['post_file_link'], ".png") || str_ends_with($post['post_file_link'], ".jpg") || str_ends_with($post['post_file_link'], ".jpeg") || str_ends_with($post['post_file_link'], ".gif") || str_ends_with($post['post_file_link'], ".webp")) {
                        echo "<a href='" . $post['post_file_link'] . "' target='_blank'><img style='max-width: 300px; height: auto; margin-top: 5px;' src='" . $post['post_file_link'] . "' alt='File URL' /></a>";
                        echo "</div><hr />";
                        // } elseif (str_ends_with($post['post_file_link'], '.mp4')) {
                        //     echo "
                        //     <video style='margin-top: 5px;' width='300px' controls>
                        //         <source src='".$post['post_file_link']."' type='video/mp4'>
                        //     <video/>";
                        //     echo "</div>";
                        // }
                    } else {
                        echo "</div><hr />";
                    }
                }
            }
        } else {
            echo "<p style='margin-top: 5px; margin-bottom: 5px;'>That user doesn't exist</p>";
        }
        ?>
    </div>
</body>

</html>