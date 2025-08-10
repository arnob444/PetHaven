<?php
$host = 'localhost';
$dbname = 'pethaven';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    echo "connection failed";
}
?>