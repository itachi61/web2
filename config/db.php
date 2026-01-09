<?php
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "techsmart";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>