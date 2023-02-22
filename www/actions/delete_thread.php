<?php
session_start();
if (isset($_SESSION['uid'])) {
    if (isset($_GET['tid'])) {
        include_once('../library/connect.php');

        $tid = mysqli_escape_string($connection, $_GET['tid']);

        $sql = "SELECT * FROM users WHERE id='" . $_SESSION['uid'] . "' LIMIT 1;";
        $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));
        $user = mysqli_fetch_assoc($res);

        $sql2 = "SELECT * FROM threads WHERE id='" . $tid . "' LIMIT 1;";
        $res2 = mysqli_query($connection, $sql2) or die(mysqli_error($connection));
        $thread = mysqli_fetch_assoc($res2);

        $sql3 = "SELECT * FROM users WHERE id='" . $thread['thread_creator'] . "' LIMIT 1;";
        $res3 = mysqli_query($connection, $sql3) or die(mysqli_error($connection));
        $thread_creator = mysqli_fetch_assoc($res3);

        $sql4 = "SELECT * FROM posts WHERE thread_id='" . $tid . "'";
        $res4 = mysqli_query($connection, $sql4) or die(mysqli_error($connection));
        $amount_of_posts = mysqli_num_rows($res4);

        $yesDelete = 0;

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

        if ($yesDelete == 1) {
            if ($thread['thread_file_url'] != "") {
                unlink($thread['thread_file_url']);
            }

            if ($amount_of_posts > 0) {
                while ($post = mysqli_fetch_assoc($res4)) {
                    $sql5 = "DELETE FROM posts WHERE id='" . $post['id'] . "';";
                    $res5 = mysqli_query($connection, $sql5) or die(mysqli_error($connection));

                    if ($post['post_file_link'] != "") {
                        unlink("../" . $post['post_file_link']);
                    }
                }
            }

            $sql6 = "DELETE FROM threads WHERE id='" . $tid . "';";
            $res6 = mysqli_query($connection, $sql6) or die(mysqli_error($connection));

            if ($res6) {
                header("Location: /view_category.php?cid=" . $thread['category_id'] . "");
            } else {
                echo "<p>Something got messed up up while replying</p>";
            }
        } else {
            echo "It can't get deleted!";
        }
    } else {
        echo "No thread ID given.";
        exit();
    }
} else {
    exit();
}
