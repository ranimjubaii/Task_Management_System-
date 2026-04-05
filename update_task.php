<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: index.php");
    exit();
}

include "db.php";

$task_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id=?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (isset($_POST['update_task'])) {
    $title = $_POST['task_title'];
    $desc = $_POST['task_description'];
    $worker_id = $_POST['worker_id'];
    $deadline = $_POST['deadline'];

    $stmt = $conn->prepare("UPDATE tasks SET title=?, description=?, worker_id=?, deadline=? WHERE id=?");
    $stmt->bind_param("ssisi", $title, $desc, $worker_id, $deadline, $task_id);
    $stmt->execute();

    header("Location: manager_tasks.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Task</title>
    <link rel="stylesheet" href="css/manager_tasks.css">
    <link rel="stylesheet" href="css/update_task.css">
</head>
<body>
<div class="manager-container">
    <h1>Update Task</h1>
    <form method="POST">
        <input type="text" name="task_title" value="<?= htmlspecialchars($task['title']) ?>" required>
        <textarea name="task_description"><?= htmlspecialchars($task['description']) ?></textarea>
        <input type="date" name="deadline" value="<?= $task['deadline'] ?>" required>
        <select name="worker_id" required>
            <?php
            $workers = $conn->query("SELECT id, username FROM users WHERE role='worker'");
            while ($w = $workers->fetch_assoc()) {
                $selected = ($w['id'] == $task['worker_id']) ? "selected" : "";
                echo "<option value='{$w['id']}' $selected>{$w['username']}</option>";
            }
            ?>
        </select>
        <button type="submit" name="update_task">Save Changes</button>
    </form>
    <a href="manager_tasks.php" class="manager-back-link">⬅ Back</a>
</div>
</body>
</html>