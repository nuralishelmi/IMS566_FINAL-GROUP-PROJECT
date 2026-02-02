<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch latest user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $username = $_POST['username'];
    
    $update = $pdo->prepare("UPDATE users SET phone = ?, address = ?, username = ? WHERE id = ?");
    if($update->execute([$phone, $address, $username, $user_id])) {
        echo "<script>alert('Profile Updated Successfully!'); window.location='profile.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | PMC</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --pmc-red: #dc3545;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(0, 0, 0, 0.05);
            --text-main: #212529;
        }

        [data-theme="dark"] {
            --glass-bg: rgba(45, 45, 45, 0.95);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-main: #f8f9fa;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background: linear-gradient(rgba(253, 245, 230, 0.85), rgba(253, 245, 230, 0.85)), 
                        url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--text-main);
            transition: 0.3s ease;
        }

        [data-theme="dark"] body {
            background: linear-gradient(rgba(26, 26, 26, 0.92), rgba(26, 26, 26, 0.92)), 
                        url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
        }

        .navbar {
            background: rgba(33, 37, 41, 0.95) !important;
            backdrop-filter: blur(5px);
            border-bottom: 3px solid var(--pmc-red);
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: background 0.3s ease;
        }

        .medical-card {
            background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);
            color: white;
            border-radius: 20px;
            padding: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(220, 53, 69, 0.3);
        }

        .medical-card::after {
            content: "PMC";
            position: absolute;
            right: -10px;
            bottom: -10px;
            font-size: 7rem;
            opacity: 0.1;
            font-weight: 800;
        }

        .qr-placeholder {
            background: white;
            padding: 8px;
            border-radius: 12px;
            width: 90px;
            height: 90px;
        }

        /* Form Controls Alignment */
        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0,0,0,0.1);
            color: #212529;
        }

        [data-theme="dark"] .form-control {
            background: rgba(0,0,0,0.3);
            color: #f8f9fa;
            border-color: rgba(255,255,255,0.1);
        }

        [data-theme="dark"] .form-label { color: #f8f9fa; }
        [data-theme="dark"] .text-muted { color: #adb5bd !important; }
        [data-theme="dark"] .bg-light { background-color: #2c3034 !important; color: #fff; }

        .btn-save {
            background: var(--pmc-red);
            color: white;
            border-radius: 50px;
            padding: 12px 40px;
            font-weight: 600;
            border: none;
            transition: 0.3s;
        }

        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
            color: white;
        }

        /* Progress Bar Fix */
        .progress { background-color: rgba(0,0,0,0.05); }
        [data-theme="dark"] .progress { background-color: rgba(255,255,255,0.1); }
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
                <li class="nav-item"><a class="nav-link px-3 active" href="profile.php">My Profile</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="book_appointment.php">Appointments</a></li>
                <li class="nav-item px-3 py-2">
                    <div id="theme-toggle" class="btn btn-outline-danger btn-sm rounded-circle" style="cursor:pointer;">
                        <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
                    </div>
                </li>
                <li class="nav-item"><a class="btn btn-danger btn-sm px-4 rounded-pill fw-bold" href="login.php">LOGOUT</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row g-4 g-lg-5">
        <div class="col-lg-5">
            <div class="d-flex align-items-center mb-4">
                <i class="bi bi-person-badge text-danger fs-3 me-3"></i>
                <h4 class="fw-bold mb-0">Digital Identity</h4>
            </div>
            
            <div class="medical-card mb-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h5 class="mb-0 fw-bold">PENAWAR MEDICAL CLINIC</h5>
                        <small class="opacity-75 text-uppercase" style="letter-spacing: 1px;">Patient Access Card</small>
                    </div>
                    <i class="bi bi-shield-check fs-1"></i>
                </div>
                
                <div class="row align-items-center">
                    <div class="col-7">
                        <div class="mb-3">
                            <small class="opacity-75 d-block">Full Name</small>
                            <span class="fw-bold fs-5 text-uppercase"><?= htmlspecialchars($user['name'] ?? 'N/A') ?></span>
                        </div>
                        <div>
                            <small class="opacity-75 d-block">Unique ID</small>
                            <span class="fw-bold">PMC-P-<?= str_pad($user['id'] ?? 0, 5, '0', STR_PAD_LEFT) ?></span>
                        </div>
                    </div>
                    <div class="col-5 text-end">
                        <div class="qr-placeholder ms-auto d-flex align-items-center justify-content-center shadow">
                             <i class="bi bi-qr-code text-black fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top border-white border-opacity-25 d-flex justify-content-between">
                    <small>Member Since: <?= date('Y', strtotime($user['created_at'] ?? 'now')) ?></small>
                    <small><i class="bi bi-patch-check-fill me-1"></i>Verified</small>
                </div>
            </div>

            <div class="glass-card p-4">
                <h6 class="fw-bold mb-3">Profile Completion</h6>
                <div class="progress mb-3" style="height: 10px; border-radius: 50px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 85%"></div>
                </div>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Email Verified</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Identity Card (MyKad) Linked</li>
                    <li><i class="bi bi-exclamation-circle-fill text-warning me-2"></i> Emergency Contact (Missing)</li>
                </ul>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="glass-card p-4 p-md-5">
                <h4 class="fw-bold mb-4">Account Information</h4>
                <form method="POST">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Username</label>
                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Contact Number</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Registered Email (Disabled)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" class="form-control bg-light border-start-0" value="<?= $user['email'] ?? '' ?>" readonly>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="form-label small fw-bold">Primary Address</label>
                        <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                        <span class="small text-muted">Last update: <?= date('d M Y') ?></span>
                        <button type="submit" name="update_profile" class="btn btn-save shadow">Update Profile</button>
                    </div>
                </form>
            </div>
            
            <div class="glass-card mt-4 p-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="bi bi-shield-lock text-danger fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Password & Security</h6>
                        <small class="text-muted">Secure your account with 2FA</small>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-danger rounded-pill px-3">Manage</button>
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