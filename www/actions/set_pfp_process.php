<?php
session_start();
require '../library/nsfw.php';
include_once('../library/connect.php');

if (isset($_SESSION['uid'])) {
    if ($_FILES['fileToUpload']['name'] != "") {
        $tmpfile = $_FILES['fileToUpload']['tmp_name'];
        $target_dir = "../uploads/profiles/";
        $bytes = random_bytes(12);
        $file_hex = bin2hex($bytes);
        $extension = end(explode(".", $_FILES['fileToUpload']['name']));
        $target_file = $target_dir . basename($file_hex . "." . $extension);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($_FILES["fileToUpload"]["size"] > 10000000) {
            echo "Your File is too fat, choose something lighter than 10 Megabytes";
            $uploadOk = 0;
        } else {
            switch ($fileType) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($tmpfile);
                    break;
                case 'png':
                    $image = imagecreatefrompng($tmpfile);
                    break;
                default:
                    echo ("Only JPG, JPEG and PNG Extensions are supported.");
                    $uploadOk = 0;
            }

            list($w, $h) = getimagesize($tmpfile);

            if ($w < $h) {
                $image = imagecrop($image, array(
                    "x" => 0,
                    "y" => ($h - $w) / 2,
                    "width" => $w,
                    "height" => $w
                ));
            } else if ($h < $w) {
                $image = imagecrop($image, array(
                    "x" => ($w - $h) / 2,
                    "y" => 0,
                    "width" => $h,
                    "height" => $h
                ));
            }

            if ($uploadOk == 0) {
                echo "Your file can't get posted.";
            } else {
                if ($fileType == "jpg" || $fileType == "jpeg") {
                    if (imagejpeg($image, $target_file)) {
                        $fileSaved = 1;

                        $url = "../uploads/posts/" . $file_hex . "." . $extension;
                        $safe = is_safe($url);

                        if ($safe == true) {
                            $uploadOk = 1;
                        } else {
                            $uploadOk = 0;
                        }
                    } else {
                        $uploadOk = 0;
                    }
                } else if ($fileType == "png") {
                    if (imagepng($image, $target_file)) {
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
        }
    }

    if ($uploadOk == 0) {
        if ($fileSaved == 1) {
            unlink("../uploads/profiles/" . $file_hex . "." . $extension);
            echo "That won't work out!";
        }

        echo "Error!";
    } else {
        $sql = "SELECT * FROM users WHERE id='" . $_SESSION['uid'] . "';";
        $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));
        $user = mysqli_fetch_assoc($res);

        if ($user['picture_url'] == "uploads/profiles/default/user.png") {
            $sql2 = "UPDATE users SET picture_url='uploads/profiles/" . $file_hex . "." . $extension . "' WHERE id='" . $_SESSION['uid'] . "';";
            $res2 = mysqli_query($connection, $sql2) or die(mysqli_error($connection));
        } else {
            unlink("../" . $user['picture_url']);

            $sql2 = "UPDATE users SET picture_url='uploads/profiles/" . $file_hex . "." . $extension . "' WHERE id='" . $_SESSION['uid'] . "';";
            $res2 = mysqli_query($connection, $sql2) or die(mysqli_error($connection));
        }


        header("Location: /user.php?username=" . $_SESSION['username'] . "");
    }
} else {
    exit();
}
