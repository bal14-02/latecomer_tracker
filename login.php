<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $isPasswordCorrect = false;

            // Handle admin: password is hashed
            if ($user['role'] === 'admin') {
                $isPasswordCorrect = password_verify($password, $user['password']);
            }
            // Handle student: password is plain text
            elseif ($user['role'] === 'student') {
                $isPasswordCorrect = ($password === $user['password']);
            }

            if ($isPasswordCorrect) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect to respective dashboard
                if ($user['role'] === 'admin') {
                    header("Location: admindash.html");
                } elseif ($user['role'] === 'student') {
                    header("Location: profile.php");
                }
                exit();
            } else {
                echo "<script>alert('Incorrect password.'); window.location.href='login.html';</script>";
            }
        } else {
            echo "<script>alert('Username not found.'); window.location.href='login.html';</script>";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
