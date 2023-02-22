<?php session_start(); ?>
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
    <title>View Thread • The House</title>
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
                        $tid = mysqli_escape_string($connection, $_GET['tid']);

                        $sql10 = "SELECT * FROM users WHERE id='" . $_SESSION['uid'] . "' LIMIT 1;";
                        $res10 = mysqli_query($connection, $sql10) or die(mysqli_error($connection));
                        $user = mysqli_fetch_assoc($res10);

                        $sql11 = "SELECT * FROM threads WHERE id='" . $tid . "' LIMIT 1;";
                        $res11 = mysqli_query($connection, $sql11) or die(mysqli_error($connection));
                        $thread = mysqli_fetch_assoc($res11);

                        $sql12 = "SELECT * FROM users WHERE id='" . $thread['thread_creator'] . "' LIMIT 1;";
                        $res3 = mysqli_query($connection, $sql12) or die(mysqli_error($connection));
                        $thread_creator = mysqli_fetch_assoc($res3);

                        if ($user['role'] == "user") {
                            if ($thread_creator['id'] == $_SESSION['uid']) {
                                $yesDelete = 1;
                            } else {
                                $yesDelete = 0;
                            }
                        } elseif ($user['role'] == "moderator") {
                            if ($thread_creator['role'] == "admin") {
                                $yesDelete = 0;
                            } else {
                                $yesDelete = 1;
                            }
                        } elseif ($user['role'] == "admin") {
                            $yesDelete = 1;
                        }

                        if ($yesDelete) {
                            echo " | <b><a href='user.php?username=" . $_SESSION['username'] . "'>" . $_SESSION['username'] . "</a></b> | <a onClick=\"javascript: return confirm('Are you sure you want to delete the Thread? We recommend archiving Threads.');\" href='actions/delete_thread.php?tid=" . $tid . "'>delete thread</a></p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <img class='tiny-pfp' src='" . $user['picture_url'] . "' alt='User Profile Picture' /> <a href='account.php'>account</a> | <a href='actions/logout_process.php'>logout</a></td>";
                        } else {
                            echo " | <b><a href='user.php?username=" . $_SESSION['username'] . "'>" . $_SESSION['username'] . "</a></b></p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <img class='tiny-pfp' src='" . $user['picture_url'] . "' alt='User Profile Picture' /> <a href='account.php'>account</a> | <a href='actions/logout_process.php'>logout</a></td>";
                        }
                    }
                    ?>
        </tr>
    </table>
    <div class="main">
        <?php
        include_once('library/connect.php');

        if (isset($_GET['pid'])) {
            $pid = mysqli_escape_string($connection, $_GET['pid']);
        }

        if (isset($_GET['latest'])) {
            $latest = mysqli_escape_string($connection, $_GET['latest']);
        }

        $cid = mysqli_escape_string($connection, $_GET['cid']);
        $tid = mysqli_escape_string($connection, $_GET['tid']);


        $sql = "SELECT * FROM threads WHERE category_id='" . $cid . "' AND id='" . $tid . "' LIMIT 1;";
        $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));

        if (mysqli_num_rows($res) == 1) {
            while ($row = mysqli_fetch_assoc($res)) {
                if (isset($_SESSION['uid'])) {
                    $sql4 = "SELECT * FROM users WHERE id='" . $_SESSION['uid'] . "' LIMIT 1;";
                    $res4 = mysqli_query($connection, $sql4) or die(mysqli_error($connection));
                    $row4 = mysqli_fetch_assoc($res4);
                }

                $sql7 = "SELECT * FROM users WHERE id='" . $row['thread_creator'] . "' LIMIT 1;";
                $res7 = mysqli_query($connection, $sql7) or die(mysqli_error($connection));
                $row7 = mysqli_fetch_assoc($res7);

                $sql13 = "SELECT * FROM categories WHERE id='" . $cid . "' LIMIT 1;";
                $res13 = mysqli_query($connection, $sql13) or die(mysqli_error($connection));
                $category = mysqli_fetch_assoc($res13);

                echo "<h4 style='margin-top: 5px;'><a href='/view_category?cid=" . $cid . "'>" . $category['category_title'] . "</a> -> Thread: " . $row['thread_title'] . "</h4>
                        <p style='font-size: 13px;'>Created by <a href='user.php?username=" . $row7['username'] . "'>" . $row7['username'] . "</a> at " . $row['thread_date'] . " | Viewed " . $row['thread_views'] . " Times so far</p>
                        <br />";

                if ($row['thread_content'] != "") {
                    echo "<p>" . nl2br($row['thread_content']) . "</p>
                        <br />";
                }

                if ($row['thread_file_url'] != "") {
                    echo "<a href='" . $row['thread_file_url'] . "' target='_blank'><img style='max-width: 250px; height: auto; margin-top: 5px;' src='" . $row['thread_file_url'] . "' alt='File URL' /></a><br />";
                }

                if (isset($_SESSION['uid'])) {
                    echo "<hr />
                        <form action='actions/post_reply_process.php' method='post' enctype='multipart/form-data'>
                            <textarea name='reply_content' rows='5' cols='45'></textarea>
                            <br />
                            <input type='file' name='fileToUpload' id='fileToUpload'>
                            <br />
                            <input type='hidden' name='cid' value=" . $cid . " />
                            <input type='hidden' name='tid' value=" . $tid . " />
                            ";

                    if (isset($pid)) {
                        echo "<input type='hidden' name='pid' value=" . $pid . " />";
                    }

                    if (isset($latest)) {
                        echo "<input type='hidden' name='latest' value=" . $latest . " />";
                    }

                    echo "
                            <input type='submit' name='reply_submit' value='Post your Reply' />
                        </form>
                        <br />";
                } else {
                    echo "<hr/><p style='margin-top: 5px;'>Login first to be able to post Replies.</p>";
                }

                if (isset($latest)) {
                    if ($latest == "true") {
                        $sql2 = "SELECT * FROM posts WHERE category_id='" . $cid . "' AND thread_id='" . $tid . "' ORDER BY post_date DESC;";
                    } elseif ($latest == "false") {
                        $sql2 = "SELECT * FROM posts WHERE category_id='" . $cid . "' AND thread_id='" . $tid . "';";
                    } else {
                        $sql2 = "SELECT * FROM posts WHERE category_id='" . $cid . "' AND thread_id='" . $tid . "';";
                    }
                } else {
                    $sql2 = "SELECT * FROM posts WHERE category_id='" . $cid . "' AND thread_id='" . $tid . "';";
                }

                $res2 = mysqli_query($connection, $sql2) or die(mysqli_error($connection));

                function buildCommentTree($comments, $parentId = null)
                {
                    global $connection, $cid, $tid, $pid, $latest, $row4;

                    // Adding to the HTML

                    $html = '';

                    for ($i = 0; $i < sizeof($comments); $i++) {
                        $comment = $comments[$i];

                        $sql3 = "SELECT * FROM users WHERE id='" . $comment['post_author'] . "' LIMIT 1;";
                        $res3 = mysqli_query($connection, $sql3) or die(mysqli_error($connection));
                        $row3 = mysqli_fetch_assoc($res3);
                        $author = $row3['username'];

                        $sql6 = "SELECT * FROM posts WHERE post_author='" . $row3['id'] . "' ORDER BY post_date DESC;";
                        $res6 = mysqli_query($connection, $sql6) or die(mysqli_error($connection));
                        $numberOfPosts = mysqli_num_rows($res6);
                        $postOrWhat = "No posts yet";

                        if ($numberOfPosts == 0) {
                            $postOrWhat = "No posts yet.";
                        } elseif ($numberOfPosts == 1) {
                            $postOrWhat = $numberOfPosts . " post";
                        } else {
                            $postOrWhat = $numberOfPosts . " posts";
                        }

                        if ($comment['replying_to'] === $parentId) {
                            if (isset($pid)) {
                                if ($comment['id'] == $pid) {
                                    $html .= "<div id='" . $comment['id'] . "' class='comment highlighted'>";
                                } else {
                                    $html .= "<div id='" . $comment['id'] . "' class='comment'>";
                                }
                            } else {
                                $html .= "<div id='" . $comment['id'] . "' class='comment'>";
                            }

                            $html .= "<div class='top-comment'>
                            <div class='tooltip-wrap'>
                                <a style='color: #808080' href='user.php?username=" . $row3['username'] . "'>" . $author . "</a>
                                <div class='tooltip-content'>
                                    <p><a href='user.php?username=" . $row3['username'] . "'>" . $author . "</a> | " . $row3['role'] . " | " . $postOrWhat . "</p>";

                            if ($row3['bio'] != "") {
                                $html .= "<p style='color: #808080; font-size: 13px;'>Bio:</p><p class='bio'>" . $row3['bio'] . "</p>";
                            }

                            if ($row3['picture_url'] == "") {
                                $html .= "<img src='/uploads/profiles/default/user.png' style='max-width: 160px;' />";
                            } else {
                                $html .= "<img src='" . $row3['picture_url'] . "' style='max-width: 160px;' />";
                            }

                            $html .= "</div>
                                </div>
                                <p class='comment-ts'>
                                    <a style='color: #808080' href='view_thread.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $comment['id'] . "#" . $comment['id'] . "'>
                                        " . $comment['post_date'] . "
                                    </a>
                                </p>";

                            $post_content = $comment['post_content'];

                            if (isset($_SESSION['uid'])) {
                                $html .= "<p class='comment-reply'><a href='#' id='post-" . $comment['id'] . "-reply-button' >[reply]</a></p>";

                                if ($row4['role'] == "user") {
                                    if ($row3['id'] == $_SESSION['uid']) {
                                        if (isset($pid)) {
                                            if (isset($latest)) {
                                                $html .= "<p class='comment-delete'><a href='delete_post.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $comment['id'] . "&highlighted=" . $pid . "&latest=" . $latest . "'>[delete]</a></p>";
                                            } else {
                                                $html .= "<p class='comment-delete'><a href='delete_post.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $comment['id'] . "&highlighted=" . $pid . "'>[delete]</a></p>";
                                            }
                                        } else {
                                            if (isset($latest)) {
                                                $html .= "<p class='comment-delete'><a href='delete_post.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $comment['id'] . "&latest=" . $latest . "'>[delete]</a></p>";
                                            } else {
                                                $html .= "<p class='comment-delete'><a href='delete_post.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $comment['id'] . "'>[delete]</a></p>";
                                            }
                                        }
                                    }
                                } elseif ($row4['role'] == "moderator") {
                                    if ($row3['role'] == "admin") {
                                    } else {
                                        if (isset($pid)) {
                                            if (isset($latest)) {
                                                $html .= "<p class='comment-delete'><a href='delete_post.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $comment['id'] . "&highlighted=" . $pid . "&latest=" . $latest . "'>[delete]</a></p>";
                                            } else {
                                                $html .= "<p class='comment-delete'><a href='delete_post.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $comment['id'] . "&highlighted=" . $pid . "'>[delete]</a></p>";
                                            }
                                        } else {
                                            if (isset($latest)) {
                                                $html .= "<p class='comment-delete'><a href='delete_post.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $comment['id'] . "&latest=" . $latest . "'>[delete]</a></p>";
                                            } else {
                                                $html .= "<p class='comment-delete'><a href='delete_post.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $comment['id'] . "'>[delete]</a></p>";
                                            }
                                        }
                                    }
                                } elseif ($row4['role'] == "admin") {
                                    if (isset($pid)) {
                                        if (isset($latest)) {
                                            $html .= "<p class='comment-delete'><a href='delete_post.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $comment['id'] . "&highlighted=" . $pid . "&latest=" . $latest . "'>[delete]</a></p>";
                                        } else {
                                            $html .= "<p class='comment-delete'><a href='delete_post.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $comment['id'] . "&highlighted=" . $pid . "'>[delete]</a></p>";
                                        }
                                    } else {
                                        if (isset($latest)) {
                                            $html .= "<p class='comment-delete'><a href='delete_post.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $comment['id'] . "&latest=" . $latest . "'>[delete]</a></p>";
                                        } else {
                                            $html .= "<p class='comment-delete'><a href='delete_post.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $comment['id'] . "'>[delete]</a></p>";
                                        }
                                    }
                                }
                            }

                            $html .= "</div><div class='comment-content'>" . nl2br($post_content) . "</div>";

                            if ($comment['post_file_link'] != "") {
                                // // Commenting these down here out since video support is yet to be implemented.
                                // if (str_ends_with($comment['post_file_link'], ".png") || str_ends_with($comment['post_file_link'], ".jpg") || str_ends_with($comment['post_file_link'], ".jpeg") || str_ends_with($comment['post_file_link'], ".gif") || str_ends_with($comment['post_file_link'], ".webp")) {
                                $html .= "<a href='" . $comment['post_file_link'] . "' target='_blank'><img style='max-width: 200px; height: auto; margin-top: 5px;' src='" . $comment['post_file_link'] . "' alt='File URL' /></a>";
                                // } elseif (str_ends_with($comment['post_file_link'], '.mp4')) {
                                //     $html .= "
                                //     <video style='margin-top: 5px;' max-width: 300px; controls>
                                //         <source src='".$comment['post_file_link']."' type='video/mp4'>
                                //     <video/>";
                                //     $html .= "</div>";
                                // }
                            }

                            $html .= "<form id='post-" . $comment['id'] . "-reply-form' action='actions/reply_to_post_process.php' method='post' style='display: none;' enctype='multipart/form-data'>
                                        <br/>
                                        <textarea name='reply_content' rows='3' cols=25'></textarea>
                                        <br />
                                        <input type='file' name='fileToUpload' id='fileToUpload'>
                                        <br />
                                        <input type='hidden' name='cid' value=" . $cid . " />
                                        <input type='hidden' name='tid' value=" . $tid . " />
                                        <input type='hidden' name='replying_to' value=" . $comment['id'] . " />
                                        ";

                            if (isset($pid)) {
                                $html .= "<input type='hidden' name='pid' value=" . $pid . " />";
                            }

                            if (isset($latest)) {
                                $html .= "<input type='hidden' name='latest' value=" . $latest . " />";
                            }

                            $html .= "<input type='submit' name='reply_submit' value='Post your Reply' />
                                    </form>";

                            $html .= '<script>
                                        let openFormButton' . $comment['id'] . ' = document.querySelector("#post-' . $comment['id'] . '-reply-button");
                                        let replyForm' . $comment['id'] . ' = document.querySelector("#post-' . $comment['id'] . '-reply-form");
            
                                        openFormButton' . $comment['id'] . '.addEventListener("click", function(event) {
                                            event.preventDefault();
            
                                            if (replyForm' . $comment['id'] . '.style.display === "none") {
                                                replyForm' . $comment['id'] . '.style.display = "block";
                                            } else {
                                                replyForm' . $comment['id'] . '.style.display = "none";
                                            }
                                        });
                                    </script>';

                            // Recursively call the function for any child comments
                            $childHtml = buildCommentTree($comments, $comment['id']);
                            if ($childHtml) {
                                $html .= '<div class="comment-children">';
                                $html .= $childHtml;
                                $html .= '</div>';
                            }
                            $html .= '</div>';
                        }
                    }

                    // Finally

                    return $html;
                }

                $posts = array();

                while ($current_row = mysqli_fetch_assoc($res2)) {
                    $posts[] = $current_row;
                }

                if (sizeof($posts) > 0) {
                    if (isset($latest)) {
                        if ($latest == "true") {
                            if (isset($pid)) {
                                echo "<a style='font-size: 13px;' href='view_thread.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $pid . "&latest=false'>[view oldest]</a>";
                            } else {
                                echo "<a style='font-size: 13px;' href='view_thread.php?cid=" . $cid . "&tid=" . $tid . "&latest=false'>[view oldest]</a>";
                            }
                        } else {
                            if (isset($pid)) {
                                echo "<a style='font-size: 13px;' href='view_thread.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $pid . "&latest=true'>[view latest]</a>";
                            } else {
                                echo "<a style='font-size: 13px;' href='view_thread.php?cid=" . $cid . "&tid=" . $tid . "&latest=true'>[view latest]</a>";
                            }
                        }
                    } else {
                        if (isset($pid)) {
                            echo "<a style='font-size: 13px;' href='view_thread.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $pid . "&latest=true'>[view latest]</a>";
                        } else {
                            echo "<a style='font-size: 13px;' href='view_thread.php?cid=" . $cid . "&tid=" . $tid . "&latest=true'>[view latest]</a>";
                        }
                    }

                    echo "<hr />";
                }


                echo buildCommentTree($posts);
                //   ^ The Function I regrest writing most.

                $old_views = $row['thread_views'];
                $latest_views = $old_views + 1;
                $sql5 = "UPDATE threads SET thread_views='" . $latest_views . "' WHERE category_id='" . $cid . "' AND id='" . $tid . "' LIMIT 1;";
                $res5 = mysqli_query($connection, $sql5) or die(mysqli_error($connection));
            }
        } else {
            echo "<p>This thread does not exist.</p>";
        }
        ?>
    </div>
</body>

</html>