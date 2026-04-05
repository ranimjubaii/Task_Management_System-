<?php
session_start();
include(__DIR__ . '/db.php');

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Secure Prepared Statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
 
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Match plain-text password (Note: password_verify() is recommended for production)
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Role-Based Redirection
            if ($user['role'] === 'manager') {
                header("Location: manager_tasks.php");
            } else {
                header("Location: worker_tasks.php");
            }
            exit();

        } else {
            $error = "Wrong password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TaskMaster Pro</title>
    <link rel="stylesheet" href="css/login.css?v=<?php echo time(); ?>">
</head>
<body class="login-page">

    <div class="login-container">
        <h2>Login</h2>

        <?php if($error): ?>
            <p class="login-error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required autocomplete="username">
            <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
            
            <button type="submit" name="login">Login</button>
        </form>

        <a href="home.php" class="manager-back-link">⬅ Back to Home</a>
    </div>

</body>
</html>