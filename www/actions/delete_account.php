<?php
session_start();
include_once("../library/connect.php");

if (isset($_SESSION['uid'])) {
    if (isset($_POST['delete_account'])) {

        // Delete all of his threads & posts in them
        $sql8 = "SELECT * FROM threads WHERE thread_creator='" . $_SESSION['uid'] . "';";
        $res8 = mysqli_query($connection, $sql8) or die(mysqli_error($connection));
        $amount_of_threads = mysqli_num_rows($res8);

        if ($amount_of_threads > 0) {
            while ($current_thread = mysqli_fetch_assoc($res8)) {
                $tid = mysqli_escape_string($connection, $current_thread['id']);

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
            }
        }

        // Delete all of his other posts
        $sql9 = "SELECT * FROM posts WHERE post_author='" . $_SESSION['uid'] . "'";
        $res9 = mysqli_query($connection, $sql9) or die(mysqli_error($connection));
        $amount_of_posts = mysqli_num_rows($res9);

        if ($amount_of_posts > 0) {
            while ($post = mysqli_fetch_assoc($res9)) {
                $sql10 = "DELETE FROM posts WHERE id='" . $post['id'] . "';";
                $res10 = mysqli_query($connection, $sql10) or die(mysqli_error($connection));

                if ($post['post_file_link'] != "") {
                    unlink("../" . $post['post_file_link']);
                }
            }
        }

        // Delete him himself
        $sql7 = "DELETE FROM users WHERE id='" . $_SESSION['uid'] . "';";
        $res7 = mysqli_query($connection, $sql7) or die(mysqli_error($connection));

        // logout
        unset($_SESSION['uid']);
        unset($_SESSION['username']);

        // Redirect
        header("Location: /index.php");
    } else {
        echo "You shouldn't have been here!";
    }
} else {
    echo "Where's your UID?";
}
