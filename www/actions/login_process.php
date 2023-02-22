<?php
session_start();
include_once('../library/connect.php');

if (isset($_POST['username'])) {
    $username = mysqli_escape_string($connection, $_POST['username']);
    $password = mysqli_escape_string($connection, $_POST['password']);
    $sql = "SELECT * FROM users WHERE username='" . $username . "' AND password='" . $password . "' LIMIT 1;";
    $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    if (mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
        $_SESSION['uid'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        header('Location: ' . $_POST['redirect']);
        exit();
    } else {
        echo "Invalid login information.";
        exit();
    }
}
