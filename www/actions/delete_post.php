<?php
session_start();
if (isset($_SESSION['uid'])) {
    if (isset($_GET['pid'])) {
        include_once('../library/connect.php');

        $cid = mysqli_escape_string($connection, $_GET['cid']);
        $tid = mysqli_escape_string($connection, $_GET['tid']);
        $pid = mysqli_escape_string($connection, $_GET['pid']);

        if (isset($_GET['highlighted'])) {
            $highlighted = mysqli_escape_string($connection, $_GET['highlighted']);
        }

        if (isset($_GET['latest'])) {
            $latest = mysqli_escape_string($connection, $_GET['latest']);
        }

        $sql = "SELECT * FROM users WHERE id='" . $_SESSION['uid'] . "' LIMIT 1;";
        $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));
        $row = mysqli_fetch_assoc($res);

        $sql2 = "SELECT * FROM posts WHERE id='" . $pid . "' LIMIT 1;";
        $res2 = mysqli_query($connection, $sql2) or die(mysqli_error($connection));
        $row2 = mysqli_fetch_assoc($res2);

        $sql3 = "SELECT * FROM users WHERE id='" . $row2['post_author'] . "' LIMIT 1;";
        $res3 = mysqli_query($connection, $sql3) or die(mysqli_error($connection));
        $row3 = mysqli_fetch_assoc($res3);

        $yesDelete = 0;

        if ($row['role'] == "user") {
            if ($row3['id'] == $_SESSION['uid']) {
                $yesDelete = 1;
            } else {
                $yesDelete = 0;
            }
        } elseif ($row['role'] == "moderator") {
            if ($row3['role'] == "admin") {
                $yesDelete = 0;
            } else {
                $yesDelete = 1;
            }
        } elseif ($row['role'] == "admin") {
            $yesDelete = 1;
        }

        if ($yesDelete == 1) {
            $sql4 = "DELETE FROM posts WHERE id='" . $pid . "';";
            $res4 = mysqli_query($connection, $sql4) or die(mysqli_error($connection));

            if ($row2['post_file_link'] != "") {
                unlink("../" . $row2['post_file_link']);
            }

            if ($res4) {
                if (isset($highlighted)) {
                    if (isset($latest)) {
                        header("Location: /view_thread.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $highlighted . "&latest=" . $latest . "");
                    } else {
                        header("Location: /view_thread.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $highlighted . "");
                    }
                } else {
                    if (isset($latest)) {
                        header("Location: /view_thread.php?cid=" . $cid . "&tid=" . $tid . "&latest=" . $latest . "");
                    } else {
                        header("Location: /view_thread.php?cid=" . $cid . "&tid=" . $tid . "");
                    }
                }
            } else {
                echo "<p>Something got fridged up while replying</p>";
            }
        } else {
            exit();
        }
    } else {
        exit();
    }
} else {
    exit();
}
