<?php
session_start();

// 1. Security: Only allow logged-in workers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    header("Location: index.php");
    exit();
}

include "db.php";
$worker_id = $_SESSION['user_id'];

/* 2. LOGIC: Toggle Status */
if (isset($_GET['toggle_status'])) {
    $task_id = intval($_GET['toggle_status']);
    $current = $_GET['current'];
    $new_status = ($current === 'pending') ? 'done' : 'pending';
    
    $stmt = $conn->prepare("UPDATE tasks SET status=? WHERE id=? AND worker_id=?");
    $stmt->bind_param("sii", $new_status, $task_id, $worker_id);
    
    if ($stmt->execute()) {
        header("Location: worker_tasks.php");
        exit();
    }
}

/* 3. LOGIC: Upload work/files */
if (isset($_POST['upload_work'])) {
    $task_id = intval($_POST['task_id']);
    $comment = htmlspecialchars($_POST['comment']);
    $file_path = null;

    if (!empty($_FILES['work_file']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES["work_file"]["name"]);
        $file_path = $target_dir . $file_name;
        move_uploaded_file($_FILES["work_file"]["tmp_name"], $file_path);
    }

    $stmt = $conn->prepare("UPDATE tasks SET comment=?, file_path=?, status='done' WHERE id=? AND worker_id=?");
    $stmt->bind_param("ssii", $comment, $file_path, $task_id, $worker_id);
    $stmt->execute();
    
    header("Location: worker_tasks.php?upload=success");
    exit();
}

/* 4. Fetch tasks */
$stmt = $conn->prepare("SELECT id, title, description, deadline, status, comment, file_path FROM tasks WHERE worker_id = ? ORDER BY deadline ASC");
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$tasks_query = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Dashboard - TaskMaster Pro</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="css/manager_tasks.css">
    
    <style>
        html { scroll-behavior: smooth; }
        /* Professional touches for the worker table */
        .status-link { text-decoration: none; transition: 0.2s; }
        .status-link:hover { opacity: 0.8; transform: scale(1.05); display: inline-block; }
        .form-control-sm { border-radius: 8px !important; border: 1px solid #ddd; }
        .worker-table { background: white; border-radius: 12px; overflow: hidden; }
    </style>
</head>
<body class="manager-tasks"> <nav class="navbar navbar-expand-lg sticky-top shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold text-dark" href="worker_tasks.php" style="font-size: 22px;">
                TaskMaster <span style="color: #ffcc00;">Pro</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link nav-btn" href="worker_tasks.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-btn active-task" href="#taskTable">My Tasks</a>
                    </li>
                    <li class="nav-item ms-lg-4">
                        <a class="nav-link nav-btn logout-btn" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="manager-container"> <h1 class="manager-title">Worker Dashboard</h1>
        
        <?php if(isset($_GET['upload']) && $_GET['upload'] == 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 10px;">
                <i class="bi bi-check-circle-fill me-2"></i> Task updated and work submitted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="manager-table-box" id="taskTable">
            <h2 class="mb-4">Assigned Tasks Overview</h2>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Task Title</th>
                            <th>Description</th>
                            <th>Deadline</th>
                            <th class="text-center">Status</th>
                            <th>Your Notes</th>
                            <th>File Upload</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($tasks_query->num_rows > 0): ?>
                            <?php while ($task = $tasks_query->fetch_assoc()): ?>
                                <tr>
                                    <form method="POST" enctype="multipart/form-data">
                                        <td><strong><?= htmlspecialchars($task['title']) ?></strong></td>
                                        <td style="max-width: 200px; font-size: 0.9rem;"><?= htmlspecialchars($task['description']) ?></td>
                                        <td class="<?= (strtotime($task['deadline']) < time() && $task['status'] !== 'done') ? 'text-danger fw-bold' : '' ?>">
                                            <i class="bi bi-calendar3 me-1"></i> <?= date("M d, Y", strtotime($task['deadline'])) ?>
                                        </td>
                                        
                                        <td class="text-center">
                                            <a href="?toggle_status=<?= $task['id'] ?>&current=<?= $task['status'] ?>" class="status-link">
                                                <?php if ($task['status'] == 'done'): ?>
                                                    <span class="status done"><i class="bi bi-check-circle"></i> Done</span>
                                                <?php else: ?>
                                                    <span class="status pending"><i class="bi bi-hourglass-split"></i> Pending</span>
                                                <?php endif; ?>
                                            </a>
                                        </td>

                                        <td>
                                            <textarea name="comment" class="form-control form-control-sm" rows="1" placeholder="Add notes..."><?= htmlspecialchars($task['comment'] ?? '') ?></textarea>
                                        </td>
                                        <td>
                                            <?php if ($task['file_path']): ?>
                                                <div class="small text-success mb-1"><i class="bi bi-file-earmark-check"></i> File Uploaded</div>
                                            <?php endif; ?>
                                            <input type="file" name="work_file" class="form-control form-control-sm">
                                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                        </td>
                                        <td>
                                            <button type="submit" name="upload_work" class="btn btn-warning btn-sm fw-bold px-3" style="border-radius: 8px;">
                                                Update
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">No tasks assigned to you at the moment.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>