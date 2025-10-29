<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "quiz_system";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
  die("خطا در اتصال به پایگاه داده: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
