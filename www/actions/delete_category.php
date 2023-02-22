<?php
session_start();

if (isset($_SESSION['uid'])) {
    include_once('../library/connect.php');

    $sql4 = "SELECT * FROM users WHERE id='" . $_SESSION['uid'] . "' LIMIT 1;";
    $res4 = mysqli_query($connection, $sql4) or die(mysqli_error($connection));
    $row4 = mysqli_fetch_assoc($res4);
    $role = $row4['role'];

    if ($role != "admin") {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        if (isset($_GET['cid'])) {
            if ($_GET['cid'] == "") {
                echo "Nothing here to delete.";
            } else {
                $cid = mysqli_escape_string($connection, $_GET['cid']);

                $sql7 = "SELECT * FROM threads WHERE category_id='" . $cid . "'";
                $res8 = mysqli_query($connection, $sql7) or die(mysqli_error($connection));
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

                $sql6 = "DELETE FROM categories WHERE id='" . $cid . "';";
                $res6 = mysqli_query($connection, $sql6) or die(mysqli_error($connection));

                header('Location: /index.php');
            }
        } else {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }
} else {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
