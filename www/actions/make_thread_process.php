<?php
session_start();
require '../library/nsfw.php';

if ($_SESSION['uid'] == "") {
    header("Location: /index.php");
    exit();
}

if (isset($_POST['thread_submit'])) {
    if (($_POST['thread_title'] == "") && ($_POST['thread_content'] == "")) {
        echo "You did not fill both Fields.";
        exit();
    } else {
        include_once('../library/connect.php');

        $cid = mysqli_escape_string($connection, $_POST['cid']);
        $title = htmlspecialchars(mysqli_escape_string($connection, $_POST['thread_title']), ENT_QUOTES);

        $thread_content = mysqli_escape_string($connection, $_POST['thread_content']);
        $pattern = '/(((https?:\/\/)|(www\.))[^\s]+)/';
        $replace = '<a href="$1" target="_blank">$1</a>';
        $content = preg_replace($pattern, $replace, htmlspecialchars($thread_content, ENT_QUOTES));

        $author = mysqli_escape_string($connection, $_SESSION['uid']);

        $uploadOk = 0;
        $fileSaved = 0;

        if ($_FILES['fileToUpload']['name'] != "") {
            $target_dir = "../uploads/posts/";
            $bytes = random_bytes(12);
            $file_hex = bin2hex($bytes);
            $filename = $_FILES['fileToUpload']['name'];
            $exploded = explode(".", $filename);
            $extension = end($exploded);
            $target_file = $target_dir . basename($file_hex . "." . $extension);
            $uploadOk = 1;
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if ($_FILES["fileToUpload"]["size"] > 8000000) {
                echo "Your File is too fat, choose something lighter than 8 Megabytes";
                exit();
                $uploadOk = 0;
            }

            if (
                $fileType != "jpg" && $fileType != "png" && $fileType != "jpeg"
                && $fileType != "gif" && $fileType != "webp" /* && $fileType != "mp4" */
            ) {
                echo "Only JPG, JPEG, PNG, WEBP & GIF Files are allowed";
                exit();
                $uploadOk = 0;
            }

            if ($uploadOk == 0) {
                echo "We can't post your file.";
                exit();
            } else {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    $fileSaved = 1;

                    $url = "uploads/posts/" . $file_hex . "." . $extension;
                    $safe = is_safe($url);

                    if ($safe == true) {
                        $uploadOk = 1;
                    } else {
                        $uploadOk = 0;
                    }
                } else {
                    $uploadOk = 0;
                }
            }
        }

        if ($uploadOk == 0) {
            if ($thread_content == "" && $uploadOk == 0) {
                echo "we don't accept empty messages";
                exit();
            } else {
                if ($fileSaved == 1) {
                    unlink("../uploads/posts/" . $file_hex . "." . $extension);
                }

                $sql = "INSERT INTO threads (category_id, thread_title, thread_creator, thread_content, thread_date, thread_reply_date) VALUES ('" . $cid . "', '" . $title . "', '" . $author . "', '" . $content . "', now(), now());";
                $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));
                $new_thread_id = mysqli_insert_id($connection);
            }
        } else {
            $sql = "INSERT INTO threads (category_id, thread_title, thread_creator, thread_content, thread_file_url, thread_date) VALUES ('" . $cid . "', '" . $title . "', '" . $author . "', '" . $content . "', 'uploads/posts/" . $file_hex . "." . $extension . "', now());";
            $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));
            $new_thread_id = mysqli_insert_id($connection);
        }

        $sql3 = "UPDATE categories SET last_post_date=now(), last_user_posted='" . $author . "' WHERE id='" . $cid . "' LIMIT 1;";
        $res3 = mysqli_query($connection, $sql3) or die(mysqli_error($connection));

        if (($res) && ($res3)) {
            header("Location: /view_thread.php?cid=" . $cid . "&tid=" . $new_thread_id . "");
        } else {
            echo "Crap! Error!";
        }
    }
}
