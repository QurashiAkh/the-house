<?php
require '../library/nsfw.php';
session_start();
if (isset($_SESSION['uid'])) {
    if (isset($_POST['reply_submit'])) {
        include_once('../library/connect.php');

        $author = mysqli_escape_string($connection, $_SESSION['uid']);
        $cid = mysqli_escape_string($connection, $_POST['cid']);
        $tid = mysqli_escape_string($connection, $_POST['tid']);

        if (isset($_POST['pid'])) {
            $pid = mysqli_escape_string($connection, $_POST['pid']);
        }

        if (isset($_POST['latest'])) {
            $latest = mysqli_escape_string($connection, $_POST['latest']);
        }

        $unsafe_reply_content = mysqli_escape_string($connection, $_POST['reply_content']);
        $pattern = '/(((https?:\/\/)|(www\.))[^\s]+)/';
        $replace = '<a href="$1" target="_blank">$1</a>';
        $reply_content = preg_replace($pattern, $replace, htmlspecialchars($unsafe_reply_content), ENT_QUOTES);

        $uploadOk = 0;
        $fileSaved = 0;

        if ($_FILES['fileToUpload']['name'] != "") {
            $target_dir = "../uploads/posts/";
            $bytes = random_bytes(12);
            $file_hex = bin2hex($bytes);
            $extension = end(explode(".", $_FILES['fileToUpload']['name']));
            $target_file = $target_dir . basename($file_hex . "." . $extension);
            $uploadOk = 1;
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if ($_FILES["fileToUpload"]["size"] > 8000000) {
                echo "Your File is too fat, choose something lighter than 8 Megabytes";
                $uploadOk = 0;
            }

            if (
                $fileType != "jpg" && $fileType != "png" && $fileType != "jpeg"
                && $fileType != "gif" && $fileType != "webp" /* && $fileType != "mp4" */
            ) {
                echo "only JPG, JPEG, PNG, WEBP & GIF Files are allowed";
                $uploadOk = 0;
            }

            if ($uploadOk == 0) {
                echo "Your file can't be posted.";
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
            if ($reply_content == "" && $uploadOk == 0) {
                echo "we don't accept empty messages";
                exit();
            } else {
                if ($fileSaved == 1) {
                    unlink("../uploads/posts/" . $file_hex . "." . $extension);
                }

                $sql = "INSERT INTO posts (category_id, thread_id, post_author, post_content, post_date) VALUES ('" . $cid . "', '" . $tid . "', '" . $author . "', '" . $reply_content . "', now());";
                $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));
            }
        } else {
            $sql2 = "INSERT INTO posts (category_id, thread_id, post_author, post_content, post_file_link, post_date) VALUES ('" . $cid . "', '" . $tid . "', '" . $author . "', '" . $reply_content . "', 'uploads/posts/" . $file_hex . "." . $extension . "', now());";
            $res2 = mysqli_query($connection, $sql2) or die(mysqli_error($connection));
        }


        $sql3 = "UPDATE categories SET last_post_date=now(), last_user_posted='" . $author . "' WHERE id='" . $cid . "' LIMIT 1;";
        $res3 = mysqli_query($connection, $sql3) or die(mysqli_error($connection));

        $sql4 = "UPDATE threads SET thread_reply_date=now(), thread_last_user='" . $author . "' WHERE id='" . $tid . "' LIMIT 1;";
        $res4 = mysqli_query($connection, $sql4) or die(mysqli_error($connection));

        if (($res) || ($res2) && ($res3) && ($res4)) {
            if (isset($pid)) {
                if (isset($latest)) {
                    header("Location: /view_thread.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $pid . "&latest=" . $latest . "");
                } else {
                    header("Location: /view_thread.php?cid=" . $cid . "&tid=" . $tid . "&pid=" . $pid . "");
                }
            } else {
                if (isset($latest)) {
                    header("Location: /view_thread.php?cid=" . $cid . "&tid=" . $tid . "&latest=" . $latest . "");
                } else {
                    header("Location: /view_thread.php?cid=" . $cid . "&tid=" . $tid . "");
                }
            }
        } else {
            echo "<p>You can't reply.</p>";
        }


        // Email Sending would look something like:

        // $sql4 = "SELECT post_author FROM posts WHERE category_id='".$cid."' AND thread_id='".$tid."' GROUP BY post_author;";
        // $res4 = mysqli_query($connection, $sql4) or die(mysqli_error($connection));
        // while (row4 = mysqli_fetch_assoc($res4)) {
        //     $userids[] .= $row4['post_author'];
        // }
        // foreach ($userids as $key) {
        //     $sql5 = "SELECT id, email FROM users WHERE id='".$key."'AND forum_notification='1' LIMIT 1;";
        //     $res5 = mysqli_query($connection, $sql5) or die(mysqli_error($connection));
        //     if (mysqli_num_rows($res5) > 0) {
        //         $row5 = mysqli_fetch_assoc($res5);
        //         if ($row5['id'] != $author) {
        //             $email .= $row5['email'].", ";
        //         }
        //     }
        // }
        // $email = substr($email, 0, (strlen($email) - 2));
        // $to = "noreply@somewhere.com";
        // $from = "yesyesmyemail";
        // $bcc = $email;
        // $subject = "Forum Reply";
        // $message = "Someone replied to a thread you've talked in";
        // $headers = "From: The House\r\nReply-To: The House";
        // $headers .= "\r\nBcc: {$bcc}";
        // mail($to, $subject, $message, $headers)
    } else {
        exit();
    }
} else {
    echo "You're not logged in, try logging out & in back";
    exit();
}
