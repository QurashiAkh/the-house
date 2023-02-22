<?php session_start() ?>
<?php
if ((!isset($_SESSION['uid'])) || ($_GET['cid'] == "")) {
    header("Location: /index.php");
}

$cid = $_GET['cid'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if (!isset($_SESSION['theme'])) {
        $_SESSION['theme'] = "light";
    };
    if ($_SESSION['theme'] == "light") {
        echo '<link rel="stylesheet" type="text/css" href="static/css/light-theme.css">';
    } else {
        echo '<link rel="stylesheet" type="text/css" href="static/css/dark-theme.css">';
    } ?>
    <link rel="icon" type="image/png" href="static/images/favicon.png" />
    <title>Create Thread • The House</title>
</head>

<body>
    <table cellpadding="0" cellspacing="0" width="100%" class="top-bar">
        <tr>
            <td>
                <p><b><a href="index.php">The House</a><a href='about.php'>¹</a></b>
                    <?php
                    include_once('library/connect.php');

                    $sql3 = "SELECT * FROM users WHERE id='" . $_SESSION['uid'] . "' LIMIT 1;";
                    $res3 = mysqli_query($connection, $sql3) or die(mysqli_error($connection));
                    $row3 = mysqli_fetch_assoc($res3);

                    if (!isset($_SESSION['uid'])) {
                        echo "</p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <a href='login.php'>login</a></td>";
                    } else {
                        echo " | <b><a href='user.php?username=" . $_SESSION['username'] . "'>" . $_SESSION['username'] . "</a></b></p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <img class='tiny-pfp' src='" . $row3['picture_url'] . "' alt='User Profile Picture' /> <a href='account.php'>account</a> | <a href='actions/logout_process.php'>logout</a></td>";
                    }
                    ?>
        </tr>
    </table>
    <div class='main'>
        <form action='actions/make_thread_process.php' method='post' enctype="multipart/form-data">
            <p>Thread title</p>
            <input autocomplete="off" type='text' name='thread_title' size='43' maxlength='100' />
            <p>Thread content</p>
            <textarea name="thread_content" cols="45" rows="5"></textarea>
            <br />
            <input type='file' name='fileToUpload' id='fileToUpload'>
            <br />
            <br />
            <input type='hidden' name='cid' value='<?php echo $cid; ?>' />
            <input type="submit" name='thread_submit' value='Create your thread' />
    </div>
</body>

</html>