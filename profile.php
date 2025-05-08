<?php 
session_start();
include 'db.php'; // Include your DB connection

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] === 'admin') {
    // Admin will always view Aarav Kapoor (user_id = 1)
    $user_id = 1;
} else {
    // Students view their own profile
    $user_id = $_SESSION['user_id'];
}

// Step 1: Get user info
$sql = "SELECT fullname, roll_no, year_of_study, department FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error in user query: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($fullname, $roll_no, $year_of_study, $department);
$stmt->fetch();
$stmt->close();

// Step 2: Get latecomer timestamps
$sql2 = "SELECT timestamp FROM latecomers WHERE user_id = ?";
$stmt2 = $conn->prepare($sql2);
if (!$stmt2) {
    die("Error in latecomer query: " . $conn->error);
}
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result = $stmt2->get_result();
$late_records = $result->fetch_all(MYSQLI_ASSOC);
$stmt2->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Late Comer Tracker</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <header>
    <div class="container">
      <h1>Late Comer Tracker</h1>
      <p>Welcome, <span><?= htmlspecialchars($fullname) ?></span>!</p>
    </div>
  </header>

  <main>
    <div class="container">
      <section class="student-details">
        <h2>Student Details</h2>
        <div class="details">
          <p><strong>Name:</strong> <?= htmlspecialchars($fullname) ?></p>
          <p><strong>Roll No:</strong> <?= htmlspecialchars($roll_no) ?></p>
          <p><strong>Department:</strong> <?= htmlspecialchars($department) ?></p>
          <p><strong>Year:</strong> <?= htmlspecialchars($year_of_study) ?></p>
        </div>
      </section>

      <section class="late-attendance">
        <h2>Late Attendance Records</h2>
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($late_records)): ?>
              <?php foreach ($late_records as $record): 
                  $timestamp = strtotime($record['timestamp']);
                  $date = date("Y-m-d", $timestamp);
                  $time = date("H:i:s", $timestamp);
              ?>
                <tr>
                  <td><?= htmlspecialchars($date) ?></td>
                  <td><?= htmlspecialchars($time) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="2">No late attendance records found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <section class="additional-info">
        <h2>Important Notes</h2>
        <ul>
          <li>Attendance affects final grades.</li>
          <li>Excessive tardiness may result in warnings.</li>
          <li>Contact the department if there's a valid reason for being late.</li>
        </ul>
      </section>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 College Name. All Rights Reserved.</p>
  </footer>
</body>
</html>
