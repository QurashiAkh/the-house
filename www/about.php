<?php session_start(); ?>
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
    <title>About • The House</title>
</head>

<body>
    <table cellpadding="0" cellspacing="0" width="100%" class="top-bar">
        <tr>
            <td>
                <p><b><a href="index.php">The House</a><a href='about.php'>¹</a></b>
                    <?php
                    include_once('library/connect.php');

                    $sql = "SELECT * FROM users WHERE id='" . $_SESSION['uid'] . "' LIMIT 1;";
                    $res = mysqli_query($connection, $sql) or die(mysqli_error($connection));
                    $row = mysqli_fetch_assoc($res);

                    if (!isset($_SESSION['uid'])) {
                        echo "</p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <a href='login.php'>login</a></td>";
                    } else {
                        if ($row['picture_url'] != "") {
                            echo " | <b><a href='user.php?username=" . $_SESSION['username'] . "'>" . $_SESSION['username'] . "</a></b></p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <img class='tiny-pfp' src='" . $row['picture_url'] . "' alt='User Profile Picture' /> <a href='account.php'>account</a> | <a href='actions/logout_process.php'>logout</a></td>";
                        } else {
                            echo " | <b><a href='user.php?username=" . $_SESSION['username'] . "'>" . $_SESSION['username'] . "</a></b></p></td><td style='text-align: right;'><a href='actions/toggle_theme.php'>toggle theme</a> | <a href='account.php'>account</a> | <a href='actions/logout_process.php'>logout</a></td>";
                        }
                    }
                    ?>
        </tr>
    </table>
    <div class='main'>
        <h4 style='margin-top: 5px;'>Salam,</h4>
        <br />
        <p style='margin-bottom: 10px;'>
            The House is a Small and light-weight Forum to enjoy talking on.
            <br />
            <br />
            See the Project on <a href="https://github.com/QurashiAkh/the-house/" style="text-decoration: underline;">GitHub</a>.
        </p>
    </div>
</body>

</html>