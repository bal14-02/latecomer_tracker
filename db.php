<?php
$host = 'localhost';
$user = 'root';
$password = ''; // default XAMPP password is empty
$database = 'latecomer_tracker'; 

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
