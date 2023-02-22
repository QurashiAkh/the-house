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
    <title>Login • The House</title>
</head>

<body>
    <table cellpadding="0" cellspacing="0" width="100%" class="top-bar">
        <tr>
            <td>
                <p><b><a href="index.php">The House</a><a href='about.php'>¹</a></b>
                    <?php
                    if (!isset($_SESSION['uid'])) {
                        echo "</p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <a href='login.php'>login</a></td>";
                    } else {
                        if ($row3['picture_url'] != "") {
                            echo " | <b><a href='user.php?username=" . $_SESSION['username'] . "'>" . $_SESSION['username'] . "</a></b></p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <img class='tiny-pfp' src='" . $row3['picture_url'] . "' alt='User Profile Picture' /> <a href='account.php'>account</a> | <a href='actions/logout_process.php'>logout</a></td>";
                        } else {
                            echo " | <b><a href='user.php?username=" . $_SESSION['username'] . "'>" . $_SESSION['username'] . "</a></b></p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <a href='account.php'>account</a> | <a href='actions/logout_process.php'>logout</a></td>";
                        }
                    }
                    ?>
        </tr>
    </table>
    <div class='main'>
        <?php
        if (!isset($_SESSION['uid'])) {
            echo "
                <h4 style='margin-top: 5px; margin-bottom: 5px;'>Login:</h4>
                <form action='actions/login_process.php' method='post'>
                    Username: <input type='text' name='username' />
                    <br />
                    Password: <input type='password' name='password' />
                    <br />
                    <input type='hidden' name='redirect' value='" . $_SERVER['HTTP_REFERER'] . "' />
                    <input type='submit' name='submit' value='login' />
                </form>
                <br />
                <h4 style='margin-top: 5px; margin-bottom: 5px;'>or Create an Account:</h4>
                <form style='margin-bottom: 5px;' action='actions/signup_process.php' method='post'>
                    Username: <input type='text' name='username' />
                    <br />
                    Password: <input type='password' name='password' />
                    <br />
                    <input type='hidden' name='redirect' value='" . $_SERVER['HTTP_REFERER'] . "' />
                    <input type='submit' name='submit' value='create' />
                </form>
                ";
        } else {
            header('Location: /index.php');
        }
        ?>
    </div>
</body>

</html>