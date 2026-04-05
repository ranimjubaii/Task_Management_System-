<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: index.php");
    exit();
}

include "db.php";

$error = "";
$success = "";

if (isset($_POST['add_employee'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role']; // 'manager' or 'worker'

    // Hash password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashedPassword, $role);

    if ($stmt->execute()) {
        $success = "Employee added successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Employee</title>
    <link rel="stylesheet" href="css/manager_tasks.css">
    <link rel="stylesheet" href="css/add_employee.css">
</head>
<body class="manager-tasks">
<div class="manager-container">
    <h1>Add New Employee</h1>

    <?php if($error) echo "<p class='manager-notification'>{$error}</p>"; ?>
    <?php if($success) echo "<p class='manager-notification' style='background:#81c784;color:white;'>{$success}</p>"; ?>

    <form method="POST" class="employee-form">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Role</label>
        <select name="role" required>
            <option value="worker">Worker</option>
            <option value="manager">Manager</option>
        </select>

        <button type="submit" name="add_employee">Add Employee</button>
    </form>

    <a href="manager_tasks.php" class="manager-back-link">⬅ Back to Tasks</a>
</div>
</body>
</html>