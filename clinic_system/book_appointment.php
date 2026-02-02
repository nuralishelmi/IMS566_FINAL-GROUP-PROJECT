<?php
session_start();
include 'db.php';

// Check if user is logged in as patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_SESSION['user_id'];
    $doctor     = $_POST['doctor_name'];
    $date        = $_POST['appointment_date'];
    $time        = $_POST['appointment_time'];
    $symptoms   = $_POST['symptoms'];

    // CRUD: Create Operation
    $sql = "INSERT INTO appointments (patient_id, doctor_name, appointment_date, appointment_time, symptoms, status) 
            VALUES (?, ?, ?, ?, ?, 'pending')";
    $stmt = $pdo->prepare($sql);
    
    if($stmt->execute([$patient_id, $doctor, $date, $time, $symptoms])) {
        echo "<script>alert('Appointment Booked Successfully!'); window.location='patient_dashboard.php';</script>";
    } else {
        echo "<script>alert('Booking failed. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment | PMC</title>
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
        }

        /* Form Visibility Fixes */
        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0,0,0,0.1);
            color: #212529;
        }

        [data-theme="dark"] .form-control, 
        [data-theme="dark"] .form-select {
            background: rgba(0,0,0,0.3);
            color: #f8f9fa;
            border: 1px solid rgba(255,255,255,0.1);
        }

        /* Dark Theme Instruction Box */
        [data-theme="dark"] .instruction-box {
            background: rgba(220, 53, 69, 0.15);
            color: #f8f9fa;
        }

        [data-theme="dark"] .text-muted {
            color: #adb5bd !important;
        }

        .instruction-box {
            border-left: 5px solid var(--pmc-red);
            background: rgba(220, 53, 69, 0.05);
            border-radius: 10px;
            padding: 15px;
            transition: 0.3s;
        }

        .btn-confirm {
            background: var(--pmc-red);
            color: white;
            border-radius: 50px;
            font-weight: 600;
            padding: 14px;
            border: none;
            transition: 0.3s;
        }

        .btn-confirm:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
            color: white;
        }
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
                <li class="nav-item"><a class="nav-link px-3 active" href="book_appointment.php">Appointments</a></li>
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
    <div class="row g-4 align-items-start">
        <div class="col-lg-4 order-2 order-lg-1">
            <div class="glass-card p-4 mb-4">
                <h5 class="fw-bold text-danger mb-4"><i class="bi bi-info-circle me-2"></i>Booking Guidelines</h5>
                <div class="instruction-box mb-3 small">
                    <strong class="d-block mb-1">Arrive Early</strong>
                    Please arrive 15 minutes before your slot for registration.
                </div>
                <div class="instruction-box mb-3 small">
                    <strong class="d-block mb-1">Cancellation</strong>
                    You can cancel or reschedule up to 2 hours before the time.
                </div>
                <div class="instruction-box small">
                    <strong class="d-block mb-1">Documents</strong>
                    Bring your Digital Medical ID found in your profile.
                </div>
            </div>
            
            <div class="glass-card p-4 text-center">
                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                    <i class="bi bi-telephone-plus text-danger fs-3"></i>
                </div>
                <h6 class="fw-bold">Need Help?</h6>
                <p class="small text-muted mb-3">Call our 24/7 helpdesk for emergency bookings or support.</p>
                <a href="tel:031234567" class="btn btn-dark btn-sm rounded-pill px-4 py-2">Call Now</a>
            </div>
        </div>

        <div class="col-lg-8 order-1 order-lg-2">
            <div class="glass-card p-4 p-md-5">
                <div class="mb-4">
                    <h2 class="fw-bold mb-1">Book Your <span class="text-danger">Visit</span></h2>
                    <p class="text-muted">Fill in your details below and a consultant will be assigned.</p>
                </div>
                
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase">Select Specialist</label>
                        <select name="doctor_name" class="form-select shadow-sm" required>
                            <option value="">-- Choose Doctor --</option>
                            <option value="Dr. Ahmad (General Physician)">Dr. Ahmad (General Physician)</option>
                            <option value="Dr. Anisah (Pediatrician)">Dr. Anisah (Pediatrician)</option>
                            <option value="Dr. Kim (Dentist)">Dr. Kim (Dentist)</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase">Preferred Date</label>
                            <input type="date" name="appointment_date" class="form-control shadow-sm" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase">Preferred Time</label>
                            <input type="time" name="appointment_time" class="form-control shadow-sm" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase">Symptoms / Reason for Visit</label>
                        <textarea name="symptoms" class="form-control shadow-sm" rows="4" placeholder="Briefly describe your health concerns..." required></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn btn-confirm w-100 fs-5 shadow-sm">Confirm Booking</button>
                        <div class="text-center mt-3">
                            <a href="patient_dashboard.php" class="text-muted text-decoration-none small">
                                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </form>
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