<?php
session_start();
// Only allow managers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: index.php");
    exit();
}

include "db.php";

// Fetch tasks that are 'done' and have comments or files
$query = "SELECT t.title, t.comment, t.file_path, t.deadline, u.username AS worker_name 
          FROM tasks t 
          JOIN users u ON t.worker_id = u.id 
          WHERE t.status = 'done' 
          ORDER BY t.deadline DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Review Submissions</title>
    <link rel="stylesheet" href="css/view_submissions.css">
    <style>
        .file-link { color: #fbc02d; text-decoration: none; font-weight: bold; }
        .file-link:hover { text-decoration: underline; }
        .no-file { color: #bbb; font-style: italic; }
    </style>
</head>
<body class="manager-tasks">

<div class="manager-container">
    <h1 class="manager-title">Review Work Submissions</h1>
    
    <div class="table-box manager-table-box">
        <table>
            <thead>
                <tr>
                    <th>Worker</th>
                    <th>Task Title</th>
                    <th>Worker's Comment</th>
                    <th>Attached File</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['worker_name']) ?></strong></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['comment']) ?></td>
                        <td>
                            <?php if (!empty($row['file_path'])): ?>
                                <a href="<?= $row['file_path'] ?>" class="file-link" target="_blank">
                                   📂 View File
                                </a>
                            <?php else: ?>
                                <span class="no-file">No file uploaded</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">No completed work found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="manager-back-container">
        <a class="manager-back-link" href="manager_tasks.php">⬅ Back to Dashboard</a>
    </div>
</div>

</body>
</html>