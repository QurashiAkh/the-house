<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (!isset($_SESSION['theme'])) {
        $_SESSION['theme'] = "light";
    }

    if ($_SESSION['theme'] == "light") {
        echo '<link rel="stylesheet" type="text/css" href="static/css/light-theme.css">';
    } else {
        echo '<link rel="stylesheet" type="text/css" href="static/css/dark-theme.css">';
    } ?>
    <link rel="icon" type="image/png" href="static/images/favicon.png" />
    <title>The House</title>
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
                        $sql3 = "SELECT * FROM users WHERE id='" . $_SESSION['uid'] . "' LIMIT 1;";
                        $res3 = mysqli_query($connection, $sql3) or die(mysqli_error($connection));
                        $row3 = mysqli_fetch_assoc($res3);
                        $role = $row3['role'];

                        if ($role == "admin") {
                            echo " | <b><a href='user.php?username=" . $_SESSION['username'] . "'>" . $_SESSION['username'] . "</a></b> | <a href='make_category.php'>create category</a></p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <img class='tiny-pfp' src='" . $row3['picture_url'] . "' alt='User Profile Picture' /> <a href='account.php'>account</a> | <a href='actions/logout_process.php'>logout</a></td>";
                        } else {
                            if ($row3['picture_url'] != "") {
                                echo " | <b><a href='user.php?username=" . $_SESSION['username'] . "'>" . $_SESSION['username'] . "</a></b></p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <img class='tiny-pfp' src='" . $row3['picture_url'] . "' alt='User Profile Picture' /> <a href='account.php'>account</a> | <a href='actions/logout_process.php'>logout</a></td>";
                            } else {
                                echo " | <b><a href='user.php?username=" . $_SESSION['username'] . "'>" . $_SESSION['username'] . "</a></b></p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <a href='account.php'>account</a> | <a href='actions/logout_process.php'>logout</a></td>";
                            }
                        }
                    }
                    ?>
        </tr>
    </table>
    <div class='index_main'>
        <?php
        $sql = "SELECT * FROM categories ORDER BY id ASC;";
        $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));
        $categories = "<div class='categories'><ol>";

        if (mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
                $id = $row['id'];
                $title = $row['category_title'];
                $description = $row['category_description'];
                $last_posted_date = $row['last_post_date'];
                $last_active_user_id = $row['last_user_posted'];

                $categories .= "
                    <li class='row'>
                        <a href='view_category.php?cid=" . $id . "'>
                            <p class='title'>
                                " . $title . "
                            </p>
                        </a>
                        <div class='bottom'>";

                if ($last_active_user_id == "") {
                    $categories .= "<p class='category_description'>" . $description . " | Inactive</p></div></li>";
                } else {
                    $sql2 = "SELECT * FROM users WHERE id='" . $last_active_user_id . "' LIMIT 1;";
                    $res2 = mysqli_query($connection, $sql2) or die(mysqli_error($connection));
                    $row2 = mysqli_fetch_assoc($res2);
                    $last_active_user = $row2['username'];

                    $categories .= "<p class='category_description'>" . $description . " | last active user is <a href='user.php?username=" . $row2['username'] . "'>" . $last_active_user . "</a> at " . $last_posted_date . "</p></div></li>";
                }
            }
            $categories .= "</ol></div>";
            echo $categories;
        } else {
            echo "<p>There are no Categories available yet.</p>";
        }
        ?>
    </div>
</body>

</html>