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
        if (isset($_POST['category_submit'])) {
            if ((($_POST['category_title'] == "") && ($_POST['category_content'] == "")) || (($_POST['category_title'] == "") || ($_POST['category_content'] == ""))) {
                echo "You did not fill both Fields.";
            } else {
                $title = htmlspecialchars(mysqli_escape_string($connection, $_POST['category_title']), ENT_QUOTES);

                $description = htmlspecialchars(mysqli_escape_string($connection, $_POST['category_content']), ENT_QUOTES);

                $sql = "INSERT INTO categories (category_title, category_description) VALUES ('" . $title . "', '" . $description . "');";
                $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));
                $new_category_id = mysqli_insert_id($connection);

                if ($res) {
                    header("Location: /index.php");
                } else {
                    echo "Something got messed up right here!";
                }
            }
        } else {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }
} else {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
