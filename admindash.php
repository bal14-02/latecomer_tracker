<?php
session_start();
include 'db_connect.php';

// Restrict access to Admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all latecomers from the database (for display)
$current_month = date('Y-m');
$stmt = $conn->prepare("
    SELECT u.fullname, u.roll_number, l.late_date, l.late_time 
    FROM latecomers l 
    JOIN users u ON l.student_id = u.id 
    WHERE DATE_FORMAT(l.late_date, '%Y-%m') = :current_month
");
$stmt->execute([':current_month' => $current_month]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>

    <main>
        <h2>Scan QR Code to Mark Latecomer</h2>

        <!-- QR Scanner -->
        <div style="text-align: center;">
            <div id="reader" style="width: 300px; margin: auto;"></div>
            <p>Scanned Student ID: <span id="scanned-result"></span></p>
        </div>

        <h2>Latecomer Records (This Month)</h2>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Roll Number</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                <tr>
                    <td><?= htmlspecialchars($record['fullname']) ?></td>
                    <td><?= htmlspecialchars($record['roll_number']) ?></td>
                    <td><?= htmlspecialchars($record['late_date']) ?></td>
                    <td><?= htmlspecialchars($record['late_time']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function startScanner() {
            const scanner = new Html5Qrcode("reader");

            scanner.start(
                { facingMode: "environment" }, // Use back camera
                { fps: 10, qrbox: 250 },
                qrCodeMessage => {
                    document.getElementById("scanned-result").innerText = qrCodeMessage;

                    // Send scanned data to backend (scan_qr.php)
                    fetch('scan_qr.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'student_id=' + encodeURIComponent(qrCodeMessage)
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log("Server Response:", data);
                        alert("Student marked as late!");
                        location.reload(); // Reload to update latecomer table
                    })
                    .catch(error => console.error("Error:", error));
                },
                errorMessage => {
                    console.log("Scanning...");
                }
            ).catch(err => console.log("Camera error: ", err));
        }

        startScanner(); // Start the scanner on page load
    </script>
</body>
</html>
