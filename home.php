<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskMaster Pro - Management Simplified</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="css/manager_tasks.css">

    <style>
        /* Home Specific Styles */
        .hero-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #ffffff 0%, #fffde6 100%);
            border-bottom: 3px solid #ffcc00;
        }
        .feature-card {
            border: none;
            border-radius: 20px;
            transition: 0.3s;
            background: #fff;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(255, 204, 0, 0.2);
        }
        .icon-box {
            width: 60px;
            height: 60px;
            background: #fff9e6;
            color: #ffcc00;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="manager-tasks">

    <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-dark" href="#" style="font-size: 24px;">
                TaskMaster <span style="color: #ffcc00;">Pro</span>
            </a>
            <div class="ms-auto">
                <a href="index.php" class="nav-link nav-btn action-btn px-4">Sign In</a>
            </div>
        </div>
    </nav>

    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-3 fw-bold mb-3" style="color: #333;">
                Master Your Workflow <br>
                <span style="color: #ffcc00;">Effortlessly.</span>
            </h1>
            <p class="lead text-muted mb-5">
                The ultimate task management solution for teams. <br>
                Assign, track, and complete projects with real-time updates.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="index.php" class="btn btn-warning btn-lg px-5 py-3 shadow">Get Started Now</a>
                <a href="#features" class="btn btn-outline-dark btn-lg px-5 py-3">Learn More</a>
            </div>
        </div>
    </header>

    <section id="features" class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Everything you need in one place</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card text-center h-100">
                    <div class="icon-box mx-auto"><i class="bi bi-shield-check"></i></div>
                    <h3>Manager Control</h3>
                    <p class="text-muted">Easily assign tasks to workers, set strict deadlines, and oversee progress in real-time.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center h-100">
                    <div class="icon-box mx-auto"><i class="bi bi-rocket-takeoff"></i></div>
                    <h3>Worker Efficiency</h3>
                    <p class="text-muted">Workers receive clear instructions and can upload their files directly to the platform.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center h-100">
                    <div class="icon-box mx-auto"><i class="bi bi-file-earmark-arrow-up"></i></div>
                    <h3>Instant Reporting</h3>
                    <p class="text-muted">Automated status updates ensure that nothing ever falls through the cracks.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 text-center bg-white border-top">
        <p class="text-muted mb-0">&copy; 2026 TaskMaster Pro. Built for modern teams.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>