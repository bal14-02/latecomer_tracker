<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $roll_no = $_POST['student_id']; // Scanned QR contains roll_no

    try {
        // Fetch user_id using roll_no
        $stmt = $conn->prepare("SELECT id FROM users WHERE roll_no = :roll_no AND role = 'student'");
        $stmt->execute([':roll_no' => $roll_no]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user_id = $user['id'];

            // Insert into latecomers
            $insert = $conn->prepare("INSERT INTO latecomers (user_id) VALUES (:user_id)");
            $insert->execute([':user_id' => $user_id]);

            echo "Marked late!";
        } else {
            echo "Student not found.";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
