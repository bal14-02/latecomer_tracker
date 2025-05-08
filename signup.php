<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $year_of_study = $_POST['year'];
    $department = $_POST['department'];
    $role = 'student'; // Default role is student

    try {
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, username, password, year_of_study, department, role) 
                                VALUES (:fullname, :email, :username, :password, :year_of_study, :department, :role)");
        $stmt->execute([
            ':fullname' => $fullname,
            ':email' => $email,
            ':username' => $username,
            ':password' => $password,
            ':year_of_study' => $year_of_study,
            ':department' => $department,
            ':role' => $role
        ]);
        echo "Registration successful!";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "Username or email already exists!";
        } else {
            die("Error: " . $e->getMessage());
        }
    }
}
?>
