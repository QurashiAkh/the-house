<?php session_start() ?>
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
    <title>The House</title>
</head>

<body>
    <table cellpadding="0" cellspacing="0" width="100%" class="top-bar">
        <tr>
            <td>
                <p><b><a href="index.php">The House</a><a href='about.php'>¹</a></b>
                    <?php
                    include_once('library/connect.php');

                    $sql2 = "SELECT * FROM users WHERE id='" . $_SESSION['uid'] . "' LIMIT 1;";
                    $res2 = mysqli_query($connection, $sql2) or die(mysqli_error($connection));
                    $row2 = mysqli_fetch_assoc($res2);

                    if (!isset($_SESSION['uid'])) {
                        echo "</p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <a href='login.php'>login</a></td>";
                    } else {
                        if ($row2['picture_url'] != "") {
                            echo " | <b><a href='user.php?username=" . $_SESSION['username'] . "'>" . $_SESSION['username'] . "</a></b></p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <img class='tiny-pfp' src='" . $row2['picture_url'] . "' alt='User Profile Picture' /> <a href='account.php'>account</a> | <a href='actions/logout_process.php'>logout</a></td>";
                        } else {
                            echo " | <b><a href='user.php?username=" . $_SESSION['username'] . "'>" . $_SESSION['username'] . "</a></b></p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <a href='account.php'>account</a> | <a href='actions/logout_process.php'>logout</a></td>";
                        }
                    }
                    ?>
        </tr>
    </table>
    <div class='main'>
        <?php
        if (isset($_SESSION['uid'])) {
            $sql = "SELECT * FROM users WHERE id='" . $_SESSION['uid'] . "';";
            $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));
            $user = mysqli_fetch_assoc($res);

            if ($user['picture_url'] != "") {
                $picture_url = $user['picture_url'];
            } else {
                $picture_url = "/uploads/profiles/default/user.png";
            }

            echo "<h4 style='margin-top: 5px;'>Account Settings:</h4><hr />";

            echo "<p>Set your profile picture:</p>
                      <img style='max-width: 300px' src='" . $picture_url . "' />
                      <form action='actions/set_pfp_process.php' method='post' enctype='multipart/form-data'>
                          <input type='file' name='fileToUpload' id='fileToUpload'>
                          <br />
                          <br />
                          <input type='submit' name='submit' value='set picture' />
                      </form><hr />";

            echo "<form action='actions/update_bio_process.php' method='post'>";

            if ($user['bio'] == "") {
                echo "<p><b>Bio not set!</b></p>";
            } else {
                echo "<p>Current Bio: " . $user['bio'] . "</p>";
            }

            echo "Set Bio: <input type='bio' name='newbio' />
                          <br />
                          <br />
                          <input type='submit' name='update_bio' value='update bio' />
                      </form><hr />";

            // To be implemented later:

            // echo "<form action='change_username.php' method='post'>
            //               Set Username: <input type='username' name='newusername' />
            //               <br />
            //               <br />
            //               <input type='submit' name='submit' value='update username' />
            //           </form><hr />";

            // echo "<p>Change password:</p>
            //           <form action='change_password.php' method='post'>
            //               Old Password: <input type='password' name='oldpassword' />
            //               <br />
            //               New Password: <input type='password' name='newpassword' />
            //               <br />
            //               <br />
            //               <input type='submit' name='submit' value='change password' />
            //           </form><hr />";

            echo "<form action='actions/delete_account.php' method='post'>
            <p class='delete-account'>Delete Account:</p>
            <br />
            <input onClick=\"javascript: return confirm('Are you sure that you want to delete your account? You can not undo this action.');\" type='submit' name='delete_account' value='delete my account' />
            </form><hr />";
        } else {
            echo "You're not logged in.";
        }
        ?>
    </div>
</body>

</html>