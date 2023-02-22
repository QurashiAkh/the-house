<?php
session_start();
include_once('../library/connect.php');

if (isset($_POST['username'])) {
    $unsafe_username = mysqli_escape_string($connection, $_POST['username']);
    $pattern = '/(((https?:\/\/)|(www\.))[^\s]+)/';
    $replace = '<a href="$1" target="_blank">$1</a>';
    $username = htmlspecialchars($unsafe_username, ENT_QUOTES);

    $password = mysqli_escape_string($connection, $_POST['password']);

    if ($username == "" || $password == "") {
        echo "No Username/Password given.";
        exit();
    }

    $sql = "SELECT * FROM users WHERE username='" . $username . "' AND password='" . $password . "' LIMIT 1;";
    $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    if (mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
        echo "<p>That account already exists. Did you mean to <b>login</b> instead?</p>";
        exit();
    } else {
        if ($username == "" || $password == "") {
            echo "No Information given!";
            exit();
        }

        $sql2 = "INSERT INTO users (id, username, password, role, joined_at) VALUES (uuid(), '" . $username . "', '" . $password . "', 'user', now());";
        $res2 = mysqli_query($connection, $sql2) or die(mysqli_error($connection));

        $sql3 = "SELECT * FROM users WHERE username='" . $username . "' AND password='" . $password . "' LIMIT 1;";
        $res3 = mysqli_query($connection, $sql3) or die(mysqli_error($connection));
        $row3 = mysqli_fetch_assoc($res3);

        $_SESSION['uid'] = $row3['id'];
        $_SESSION['username'] = $row3['username'];
        header('Location: ' . $_POST['redirect']);
        exit();
    }
}
