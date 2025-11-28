<?php
require_once 'db_connect.php';

$conn = getDatabaseConnection();
$sessions = [];

if ($conn !== null) {
    $sql = "SELECT * FROM attendance_sessions ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Sessions</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 1000px; margin: 50px auto;">
        <div class="card">
            <h2>All Attendance Sessions</h2>
            <table>
                <tr>
                    <th>ID</th><th>Course</th><th>Group</th><th>Date</th><th>Opened By</th><th>Status</th>
                </tr>
                <?php foreach ($sessions as $s): ?>
                <tr>
                    <td><?php echo $s['id']; ?></td>
                    <td><?php echo $s['course_id']; ?></td>
                    <td><?php echo $s['group_id']; ?></td>
                    <td><?php echo $s['date']; ?></td>
                    <td><?php echo $s['opened_by']; ?></td>
                    <td><?php echo $s['status']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>