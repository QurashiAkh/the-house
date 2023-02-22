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
    <title>View Category • The House</title>
</head>

<body>
    <table cellpadding="0" cellspacing="0" width="100%" class="top-bar">
        <tr>
            <td>
                <p><b><a href="index.php">The House</a><a href='about.php'>¹</a></b>
                    <?php
                    include_once('library/connect.php');
                    $cid = mysqli_escape_string($connection, $_GET['cid']);

                    if (!isset($_SESSION['uid'])) {
                        echo "</p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <a href='login.php'>login</a></td>";
                    } else {
                        $sql3 = "SELECT * FROM users WHERE id='" . $_SESSION['uid'] . "' LIMIT 1;";
                        $res3 = mysqli_query($connection, $sql3) or die(mysqli_error($connection));
                        $row3 = mysqli_fetch_assoc($res3);
                        $role = $row3['role'];

                        if ($role == "admin") {
                            echo " | <b><a href='user.php?username=" . $_SESSION['username'] . "'>" . $_SESSION['username'] . "</a></b> | <a onClick=\"javascript: return confirm('Are you sure you want to delete the Thread? We recommend archiving Threads.');\" href='actions/delete_category.php?cid=" . $cid . "'>delete category</a> | <a href='make_thread.php?cid=" . $cid . "'>create thread</a></p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <img class='tiny-pfp' src='" . $row3['picture_url'] . "' alt='User Profile Picture' /> <a href='account.php'>account</a> | <a href='actions/logout_process.php'>logout</a></td>";
                        } else {
                            echo " | <b><a href='user.php?username=" . $_SESSION['username'] . "'>" . $_SESSION['username'] . "</a></b> | <a href='make_thread.php?cid=" . $cid . "'>create thread</a></p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <img class='tiny-pfp' src='" . $row3['picture_url'] . "' alt='User Profile Picture' /> <a href='account.php'>account</a> | <a href='actions/logout_process.php'>logout</a></td>";
                        }
                    }
                    ?>
        </tr>
    </table>
    <div class="index_main">
        <?php
        $sql = "SELECT id FROM categories WHERE id='" . $cid . "' LIMIT 1;";
        $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));

        if (mysqli_num_rows($res) == 1) {
            $sql2 = "SELECT * FROM threads WHERE category_id='" . $cid . "' ORDER BY thread_reply_date DESC;";
            $res2 = mysqli_query($connection, $sql2) or die(mysqli_error($connection));

            if (mysqli_num_rows($res2) > 0) {
                $sql5 = "SELECT * FROM categories WHERE id='" . $cid . "' LIMIT 1;";
                $res5 = mysqli_query($connection, $sql5) or die(mysqli_error($connection));
                $category = mysqli_fetch_assoc($res5);

                echo "<h4 style='margin-top: 5px; margin-left: 20px; margin-bottom: 5px;'>Online Threads in " . $category['category_title'] .  " Category:</h4>";
                $threads = "<div class='categories'><ol>";

                while ($row2 = mysqli_fetch_assoc($res2)) {
                    $tid = $row2['id'];
                    $title = $row2['thread_title'];
                    $views = $row2['thread_views'];
                    $date = $row2['thread_date'];
                    $creatorid = $row2['thread_creator'];
                    $last_active_user_id = $row2['thread_last_user'];
                    $last_posted_date = $row2['thread_reply_date'];

                    $sql3 = "SELECT * FROM users WHERE id='" . $creatorid . "' LIMIT 1;";
                    $res3 = mysqli_query($connection, $sql3) or die(mysqli_error($connection));
                    $row3 = mysqli_fetch_assoc($res3);
                    $creator = $row3['username'];

                    if (isset($_SESSION['uid'])) {
                        $sql4 = "SELECT * FROM users WHERE id='" . $_SESSION['uid'] . "' LIMIT 1;";
                        $res4 = mysqli_query($connection, $sql4) or die(mysqli_error($connection));
                        $row4 = mysqli_fetch_assoc($res4);

                        if ($row4['role'] == "user") {
                            if ($row3['id'] == $_SESSION['uid']) {
                                $yesDelete = "<a class='delete-thread' style='font-size: 12px' onClick=\"javascript: return confirm('Are you sure you want to delete the Thread? We recommend archiving Threads.');\" href='actions/delete_thread.php?tid=" . $tid . "'> [delete]</a></p><div class='bottom'>";
                            } else {
                                $yesDelete = "</p><div class='bottom'>";
                            }
                        } elseif ($row4['role'] == "moderator") {
                            if ($row3['role'] == "admin") {
                                $yesDelete = "</p><div class='bottom'>";
                            } else {
                                $yesDelete = "<a class='delete-thread' style='font-size: 12px' onClick=\"javascript: return confirm('Are you sure you want to delete the Thread? We recommend archiving Threads.');\" href='actions/delete_thread.php?tid=" . $tid . "'> [delete]</a></p><div class='bottom'>";
                            }
                        } elseif ($row4['role'] == "admin") {
                            $yesDelete = "<a class='delete-thread' style='font-size: 12px' onClick=\"javascript: return confirm('Are you sure you want to delete the Thread? We recommend archiving Threads.');\" href='actions/delete_thread.php?tid=" . $tid . "'> [delete]</a></p><div class='bottom'>";
                        }
                    } else {
                        $yesDelete = "</p><div class='bottom'>";
                    }

                    $threads .= "
                    <li class='row'>
                        <p class='title'>        
                            <a href='view_thread.php?cid=" . $cid . "&tid=" . $tid . "'>
                                " . $title . "
                            </a>" . $yesDelete;

                    //     </p>
                    // <div class='bottom'>";

                    if ($last_active_user_id == "") {
                        $threads .= "<p class='category_description'>created by <a href='user.php?username=" . $row3['username'] . "'>" . $creator . "</a> at " . $date . " | viewed " . $views . " times | Inactive</p>";
                    } else {
                        $sql4 = "SELECT * FROM users WHERE id='" . $last_active_user_id . "' LIMIT 1;";
                        $res4 = mysqli_query($connection, $sql4) or die(mysqli_error($connection));
                        $row4 = mysqli_fetch_assoc($res4);
                        $last_active_user = $row4['username'];

                        $threads .= "<p class='category_description'>created by <a href='user.php?username=" . $row3['username'] . "'>" . $creator . "</a> at " . $date . " | viewed " . $views . " times | last active user is <a href='user.php?username=" . $row4['username'] . "'>" . $last_active_user . "</a> at " . $last_posted_date . "</p>";
                    }

                    $threads .= "</div></li>";
                }

                $threads .= "</ol></div>";
                echo $threads;
            } else {
                echo "<p style='margin-top: 5px; margin-left: 20px; margin-bottom: 5px;'>There are no threads here yet.</p>";
            }
        } else {
            echo "<p style='margin-top: 5px; margin-left: 20px; margin-bottom: 5px;'>You're trying to view a Non-existent Category.</p>";
        }
        ?>
    </div>
</body>

</html>