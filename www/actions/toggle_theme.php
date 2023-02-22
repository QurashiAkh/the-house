<?php
session_start();

if ($_SESSION['theme'] == "light") {
    $_SESSION['theme'] = "dark";
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    $_SESSION['theme'] = "light";
    header("Location: " . $_SERVER['HTTP_REFERER']);
}
