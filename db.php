<?php
$host = 'localhost';
$db_user = 'root'; // Change to your DB user
$db_pass = '';     // Change to your DB password
$db_name = 'user_management';

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>