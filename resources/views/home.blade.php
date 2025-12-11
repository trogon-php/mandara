<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LMS Home - Welcome</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Simple Bootstrap CDN for quick styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e3f0ff 0%, #f9f9f9 100%);
            min-height: 100vh;
        }
        .navbar {
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .hero-section {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 3rem 2rem;
            margin-bottom: 2rem;
        }
        .card {
            border: none;
            border-radius: 1rem;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .card:hover {
            transform: translateY(-5px) scale(1.03);
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 0.5rem;
        }
        .footer {
            margin-top: 4rem;
            color: #888;
        }
        .about-section {
            background: #f8fafc;
            border-radius: 1rem;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">LMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="hero-section text-center mb-5">
                    <h1 class="display-4 fw-bold mb-3">Welcome to the Learning Management System</h1>
                    <p class="lead mb-4">
                        A simple and effective platform for managing courses, assignments, and user progress.<br>
                        Whether you're an <span class="fw-semibold text-primary">Admin</span>, <span class="fw-semibold text-success">Tutor</span>, or <span class="fw-semibold text-warning">Student</span>, everything you need is just a click away.
                    </p>
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 shadow">Login</a>
                </div>
                <div class="row text-center g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="feature-icon mb-2">
                                    <i class="bi bi-shield-lock"></i>
                                </div>
                                <h5 class="card-title">Admin Panel</h5>
                                <p class="card-text">Manage users, courses, and oversee the entire LMS system. Ensure smooth operation and security for all users.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="feature-icon mb-2">
                                    <i class="bi bi-person-video2"></i>
                                </div>
                                <h5 class="card-title">Tutor Panel</h5>
                                <p class="card-text">Create and manage course content, assignments, and grades. Interact with students and track their progress.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="feature-icon mb-2">
                                    <i class="bi bi-mortarboard"></i>
                                </div>
                                <h5 class="card-title">Student Panel</h5>
                                <p class="card-text">Access your courses, submit assignments, and monitor your learning journey. Stay up-to-date with announcements and deadlines.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="about-section mt-5">
                    <h3 class="mb-3">About This LMS</h3>
                    <p>
                        Our Learning Management System is designed to simplify online education for all users. With a clean interface and role-based dashboards, you can:
                    </p>
                    <ul>
                        <li>Admins: Add or remove users, manage courses, and view system analytics.</li>
                        <li>Tutors: Create engaging course materials, assign homework, and grade submissions.</li>
                        <li>Students: Enroll in courses, complete assignments, and track your academic progress.</li>
                    </ul>
                    <p>
                        <strong>Get started by logging in above!</strong>
                    </p>
                </div>
                <footer class="footer text-center small mt-5">
                    &copy; {{ date('Y') }} LMS Application. All rights reserved.
                </footer>
            </div>
        </div>
    </div>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

