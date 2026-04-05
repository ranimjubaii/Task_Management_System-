<?php
session_start();

// Only allow managers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: index.php");
    exit();
}

include "db.php";

// Add new task logic
if (isset($_POST['add_task'])) {
    $title = $_POST['task_title'];
    $desc = $_POST['task_description'];
    $worker_id = $_POST['worker_id'];
    $deadline = $_POST['deadline'];

    $stmt = $conn->prepare("INSERT INTO tasks (title, description, worker_id, deadline) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $title, $desc, $worker_id, $deadline);
    $stmt->execute();

    header("Location: manager_tasks.php");
    exit();
}

// Fetch all tasks for the table
$tasks_query = $conn->query("
    SELECT t.id, t.title, t.description, t.deadline, t.status, u.username AS worker_name
    FROM tasks t
    JOIN users u ON t.worker_id = u.id
    ORDER BY t.deadline ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - Task Management</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="css/manager_tasks.css">

    <style>
        html { scroll-behavior: smooth; } /* Makes the jumping look smooth */
        .navbar { 
            background-color: #ffffff !important; 
            border-bottom: 3px solid #ffcc00 !important; 
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }
        .nav-link:hover { color: #ffcc00 !important; }
        .btn-warning { background-color: #ffcc00 !important; border: none !important; color: white !important; }
        .btn-warning:hover { background-color: #e6b800 !important; }
        .btn-outline-warning { border-color: #ffcc00 !important; color: #333 !important; }
        .btn-outline-warning:hover { background-color: #ffcc00 !important; color: white !important; }
    </style>
</head>
<body class="manager-tasks">

    <nav class="navbar navbar-expand-lg sticky-top shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold text-dark" href="#addTaskForm" style="font-size: 22px;">
            TaskMaster <span style="color: #ffcc00;">Pro</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navContent">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link nav-btn" href="#addTaskForm">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-btn active-task" href="#taskTable">Tasks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-btn action-btn" href="add_employee.php">Add Employee</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-btn action-btn" href="view_submissions.php">View Submissions</a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="nav-link nav-btn logout-btn" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <div class="manager-container">
        <h1 class="manager-title">Task Management</h1>
        
        <div class="manager-form-box" id="addTaskForm">
            <h2 class="manager-form-title">Add New Task</h2>
            <form method="POST">
                <label for="task_title">Task Title</label>
                <input type="text" name="task_title" id="task_title" placeholder="Enter task title" required>

                <label for="task_description">Task Description</label>
                <textarea name="task_description" id="task_description" placeholder="Enter task details and requirements..."></textarea>

                <label for="worker_id">Assign to Worker</label>
                <select name="worker_id" id="worker_id" required>
                    <option value="">-- Select a Worker --</option>
                    <?php
                    $workers = $conn->query("SELECT id, username FROM users WHERE role='worker'");
                    while ($w = $workers->fetch_assoc()) {
                        echo "<option value='{$w['id']}'>" . htmlspecialchars($w['username']) . "</option>";
                    }
                    ?>
                </select>

                <label for="deadline">Deadline Date</label>
                <input type="date" name="deadline" id="deadline" required>

                <button type="submit" name="add_task" class="manager-btn" style="width: 100%;">Create Task</button>
            </form>
        </div>

        <div class="manager-table-box" id="taskTable">
            <h2>Current Task Overview</h2>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Worker</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($tasks_query->num_rows > 0): ?>
                        <?php while ($task = $tasks_query->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($task['title']) ?></strong></td>
                            <td><?= htmlspecialchars($task['description']) ?></td>
                            <td><?= htmlspecialchars($task['worker_name']) ?></td>
                            <td><?= date("M d, Y", strtotime($task['deadline'])) ?></td>
                            <td>
                                <span class="status <?= htmlspecialchars($task['status']) ?>">
                                    <?= ucfirst(htmlspecialchars($task['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="update_task.php?id=<?= $task['id'] ?>" class="update-btn">Update</a>
                                    <a href="delete_task.php?id=<?= $task['id'] ?>" 
                                       class="delete-btn" 
                                       onclick="return confirm('Are you sure you want to delete this task?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No tasks found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="manager-back-container">
            <a class="manager-back-link" href="dashboard.php">⬅ Back to Main Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>