<?php
session_start();
include_once('../library/connect.php');

if (isset($_SESSION['uid'])) {
    if (isset($_POST['update_bio'])) {
        $unsafe_new_bio = mysqli_escape_string($connection, $_POST['newbio']);
        $pattern = '/(((https?:\/\/)|(www\.))[^\s]+)/';
        $replace = '<a href="$1" target="_blank">$1</a>';
        $new_bio = preg_replace($pattern, $replace, htmlspecialchars($unsafe_new_bio), ENT_QUOTES);

        $sql = "UPDATE users SET bio='" . $new_bio . "' WHERE id='" . $_SESSION['uid'] . "' LIMIT 1;";
        $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));

        $sql2 = "SELECT username FROM users WHERE id='" . $_SESSION['uid'] . "' LIMIT 1;";
        $res2 = mysqli_query($connection, $sql2) or die(mysqli_error($connection));

        if (($res) && ($res2)) {
            $user = mysqli_fetch_assoc($res2);
            $username = $user['username'];
            header("Location: /user.php?username=" . $username);
        } else {
            echo "error";
        }
    } else {
        echo "what are you here for?";
    }
} else {
    echo "serious?";
}
