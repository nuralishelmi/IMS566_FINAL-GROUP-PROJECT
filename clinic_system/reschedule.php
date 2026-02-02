<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') { 
    header("Location: login.php"); 
    exit(); 
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ? AND patient_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$app = $stmt->fetch();

// Redirect if appointment not found
if (!$app) {
    header("Location: patient_dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_date = $_POST['new_date'];
    $new_time = $_POST['new_time'];
    
    $update = $pdo->prepare("UPDATE appointments SET appointment_date = ?, appointment_time = ?, status = 'pending' WHERE id = ?");
    if($update->execute([$new_date, $new_time, $id])) {
        echo "<script>alert('Appointment rescheduled successfully!'); window.location='patient_dashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reschedule | PMC</title>
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
            display: flex;
            align-items: center;
            transition: 0.3s ease;
        }

        [data-theme="dark"] body {
            background: linear-gradient(rgba(26, 26, 26, 0.92), rgba(26, 26, 26, 0.92)), 
                        url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0,0,0,0.1);
            color: #212529;
        }

        [data-theme="dark"] .form-control {
            background: rgba(0,0,0,0.3);
            color: #f8f9fa;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .current-info {
            background: rgba(220, 53, 69, 0.08);
            border-radius: 15px;
            padding: 15px;
            border-left: 4px solid var(--pmc-red);
        }

        .btn-confirm {
            background: var(--pmc-red);
            color: white;
            border-radius: 50px;
            font-weight: 600;
            padding: 12px;
            border: none;
            transition: 0.3s;
        }

        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
            color: white;
        }

        #theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>

    <div id="theme-toggle" class="btn btn-outline-danger btn-sm rounded-circle shadow-sm bg-white" style="cursor:pointer; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
    </div>

    <div class="container d-flex justify-content-center">
        <div class="glass-card p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="bg-danger bg-opacity-10 d-inline-block p-3 rounded-circle mb-3">
                    <i class="bi bi-calendar2-range text-danger fs-2"></i>
                </div>
                <h3 class="fw-bold">Reschedule</h3>
                <p class="text-muted small">Update your visit to a more convenient time.</p>
            </div>

            <div class="current-info mb-4">
                <small class="text-uppercase fw-bold text-danger d-block mb-1" style="letter-spacing: 1px;">Current Slot</small>
                <div class="d-flex align-items-center">
                    <i class="bi bi-clock-history me-2"></i>
                    <span><?= date('D, d M', strtotime($app['appointment_date'])) ?> at <?= date('h:i A', strtotime($app['appointment_time'])) ?></span>
                </div>
                <small class="text-muted">With: <?= htmlspecialchars($app['doctor_name']) ?></small>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Select New Date</label>
                    <input type="date" name="new_date" class="form-control" value="<?= $app['appointment_date'] ?>" min="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Select New Time</label>
                    <input type="time" name="new_time" class="form-control" value="<?= $app['appointment_time'] ?>" required>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-confirm py-3 shadow-sm">Confirm New Schedule</button>
                    <a href="patient_dashboard.php" class="btn btn-link text-muted text-decoration-none small text-center mt-2">
                        <i class="bi bi-arrow-left me-1"></i> Nevermind, keep original
                    </a>
                </div>
            </form>
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
</body>
</html>