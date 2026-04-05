<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: index.php");
    exit();
}

include "db.php";

if (isset($_GET['id'])) {
    $task_id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id=?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
}

header("Location: manager_tasks.php");
exit();
?>