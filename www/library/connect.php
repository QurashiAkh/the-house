<?php
$host = "INSERT_DB_HOST_HERE";
$username = "INSERT_DB_USERNAME_HERE";
$password = "INSERT_DB_PASSWORD_HERE";
$dbname = "thehousedatabase";

$connection = mysqli_connect($host, $username, $password, $dbname) or die(mysqli_error($connection));
