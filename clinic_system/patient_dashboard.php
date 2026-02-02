<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$patient_name = $_SESSION['name'];

$stmt = $pdo->prepare("SELECT * FROM appointments WHERE patient_id = ? ORDER BY appointment_date DESC");
$stmt->execute([$user_id]);
$appointments = $stmt->fetchAll();

$health_tips = [
    "Drink at least 8 glasses of water a day to stay hydrated.",
    "A 30-minute walk can boost your immune system significantly.",
    "Include more green leafy vegetables in your daily meals.",
    "Ensure you get at least 7-8 hours of restful sleep every night."
];
$daily_tip = $health_tips[array_rand($health_tips)];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard | PMC</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { 
            --pmc-red: #dc3545; 
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.4);
            --text-main: #212529;
            --text-muted: #6c757d;
        }

        [data-theme="dark"] {
            --glass-bg: rgba(33, 37, 41, 0.95);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-main: #f8f9fa;
            --text-muted: #adb5bd;
            background: #121212 !important;
            color: var(--text-main);
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background: #f8f9fa;
            background: linear-gradient(rgba(253, 245, 230, 0.85), rgba(253, 245, 230, 0.85)), 
                        url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--text-main);
        }

        /* Visibility Fixes for Dark Mode */
        [data-theme="dark"] .text-dark, 
        [data-theme="dark"] .text-muted,
        [data-theme="dark"] h1, 
        [data-theme="dark"] h4 { color: var(--text-main) !important; }
        
        [data-theme="dark"] .tip-section { background: #2c3034; color: white; border-color: var(--pmc-red); }
        [data-theme="dark"] .table { color: var(--text-main); }
        [data-theme="dark"] .table thead th { color: var(--text-muted); }

        .navbar {
            background: #212529 !important;
            border-bottom: 3px solid var(--pmc-red);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2rem;
        }

        .action-card { 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-decoration: none !important;
        }

        .action-card:hover { transform: translateY(-8px); }
        .bg-pmc { background: var(--pmc-red); color: white !important; }
        .bg-dark-pmc { background: #212529; color: white !important; }

        .tip-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            border-left: 6px solid var(--pmc-red);
            height: 100%;
            transition: background 0.3s;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .table-container { overflow-x: auto; }
        .table align-middle { color: inherit; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#"><i class="bi bi-hospital me-2"></i>PENAWAR <span class="text-danger">CLINIC</span></a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link px-3" href="patient_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="profile.php">My Profile</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="book_appointment.php">Appointments</a></li>
                <li class="nav-item px-3 py-2">
                    <div id="theme-toggle" class="btn btn-outline-danger btn-sm rounded-circle">
                        <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
                    </div>
                </li>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-danger btn-sm px-4 rounded-pill fw-bold" href="login.php">LOGOUT</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-lg-8">
            <h1 class="display-6 fw-bold mb-2">Welcome back, <span class="text-danger"><?= htmlspecialchars(explode(' ', $patient_name)[0]) ?>!</span></h1>
            <p class="text-muted">Manage your health and upcoming appointments in one place.</p>
        </div>
        <div class="col-lg-4">
            <div class="tip-section shadow-sm">
                <div class="d-flex align-items-start">
                    <i class="bi bi-lightbulb-fill text-warning fs-3 me-3"></i>
                    <div>
                        <small class="text-danger fw-bold">HEALTH TIP</small>
                        <p class="mb-0 small fw-medium">"<?= $daily_tip ?>"</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <a href="book_appointment.php" class="action-card bg-pmc shadow">
            <i class="bi bi-calendar-plus fs-1 mb-2"></i>
            <span class="fw-bold">Book Appointment</span>
        </a>
        <a href="profile.php" class="action-card bg-dark-pmc shadow">
            <i class="bi bi-person-badge fs-1 mb-2"></i>
            <span class="fw-bold">My Medical ID</span>
        </a>
        <div class="action-card bg-white shadow border">
            <i class="bi bi-headset text-danger fs-1 mb-2"></i>
            <span class="fw-bold text-dark">Emergency: 999</span>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="glass-card shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h4 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-danger"></i>Appointment History</h4>
                    <span class="badge bg-danger rounded-pill px-3"><?= count($appointments) ?> Records</span>
                </div>
                
                <?php if (empty($appointments)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-clipboard2-x display-1 text-muted"></i>
                        <p class="mt-3 text-muted">No medical records found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Consultant / Case</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $row): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold">Dr. <?= htmlspecialchars($row['doctor_name']) ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($row['symptoms']) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= date('D, d M Y', strtotime($row['appointment_date'])) ?></div>
                                        <div class="text-muted small"><i class="bi bi-clock me-1"></i><?= $row['appointment_time'] ?></div>
                                    </td>
                                    <td>
                                        <?php 
                                            $st = strtolower($row['status']);
                                            $cls = ($st == 'confirmed') ? 'bg-success text-white' : (($st == 'pending') ? 'bg-warning text-dark' : 'bg-danger text-white');
                                        ?>
                                        <span class="status-badge <?= $cls ?>"><?= strtoupper($row['status']) ?></span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <?php if($st == 'pending'): ?>
                                                <a href="reschedule.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3">Reschedule</a>
                                            <?php endif; ?>
                                            <a href="view_details.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-dark rounded-pill px-3">View</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;

    themeToggle.addEventListener('click', () => {
        const isDark = body.getAttribute('data-theme') === 'dark';
        if (isDark) {
            body.removeAttribute('data-theme');
            themeIcon.className = 'bi bi-moon-stars-fill';
            localStorage.setItem('theme', 'light');
        } else {
            body.setAttribute('data-theme', 'dark');
            themeIcon.className = 'bi bi-sun-fill';
            localStorage.setItem('theme', 'dark');
        }
    });

    if (localStorage.getItem('theme') === 'dark') {
        body.setAttribute('data-theme', 'dark');
        themeIcon.className = 'bi bi-sun-fill';
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>