<?php
session_start();
include 'db.php';

// Staff Authentication Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

// 1. Fetch the specific appointment
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT appointments.*, users.name, users.email FROM appointments JOIN users ON appointments.patient_id = users.id WHERE appointments.id = ?");
    $stmt->execute([$id]);
    $appointment = $stmt->fetch();

    if (!$appointment) {
        header("Location: staff_dashboard.php");
        exit;
    }
}

// 2. Handle the Update Request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doctor = $_POST['doctor_name'];
    $date   = $_POST['appointment_date'];
    $status = $_POST['status'];
    $id     = $_POST['id'];

    $sql = "UPDATE appointments SET doctor_name = ?, appointment_date = ?, status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if($stmt->execute([$doctor, $date, $status, $id])) {
        echo "<script>alert('Record Updated Successfully!'); window.location='staff_dashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Record | PMC Staff</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root { 
            --pmc-red: #dc3545; 
            --pmc-dark: #1a1a1a;
            --pmc-sidebar: #212529;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            /* ALIGNED BACKGROUND: Noticeable gradient from dashboard */
            background: radial-gradient(circle at top right, #fdfbfb 0%, #ebedee 100%);
            background-attachment: fixed;
            color: #333;
        }
        
        /* ALIGNED SIDEBAR */
        #sidebar {
            min-width: 280px;
            max-width: 280px;
            background: var(--pmc-sidebar);
            min-height: 100vh;
            color: white;
            transition: all 0.3s;
            position: sticky;
            top: 0;
        }

        .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 25px;
            margin: 5px 15px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--pmc-red);
            color: white !important;
        }

        .glass-card {
            background: white;
            border-radius: 25px;
            border: none;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .form-label { 
            font-size: 0.75rem; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            color: #666; 
            font-weight: 700;
        }

        .form-control, .form-select { 
            border-radius: 12px; 
            padding: 12px 15px; 
            border: 1px solid #eee; 
            background-color: #f8f9fa;
        }

        .form-control:focus { 
            border-color: var(--pmc-red); 
            box-shadow: none; 
            background-color: #fff;
        }

        .patient-info-tile {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 25px;
            border-left: 5px solid var(--pmc-red);
        }

        .breadcrumb-item a {
            color: var(--pmc-red);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <nav id="sidebar" class="d-none d-md-block shadow">
        <div class="p-4 text-center border-bottom border-secondary border-opacity-25">
            <h4 class="fw-bold mb-0">PENAWAR <span class="text-danger">STAFF</span></h4>
            <small class="text-muted">Control Panel v2.0</small>
        </div>
        
        <div class="p-3">
            <ul class="nav flex-column mt-3">
                <li class="nav-item mb-2">
                    <a href="staff_dashboard.php" class="nav-link active shadow-sm">
                        <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="manage_patients.php" class="nav-link">
                        <i class="bi bi-people-fill me-2"></i> Patients
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="inventory.php" class="nav-link">
                        <i class="bi bi-box-seam-fill me-2"></i> Inventory
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="reports.php" class="nav-link">
                        <i class="bi bi-file-bar-graph-fill me-2"></i> Reports
                    </a>
                </li>
                <li class="nav-item mt-5 pt-5">
                    <a href="login.php" class="nav-link text-danger">
                        <i class="bi bi-door-open-fill me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="flex-grow-1 p-4 p-md-5">
        <header class="mb-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="staff_dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Update Appointment</li>
                </ol>
            </nav>
            <h2 class="fw-bold">Modify <span class="text-danger">Record</span></h2>
            <p class="text-muted">Update patient schedule and clinical status</p>
        </header>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="glass-card p-4 p-md-5">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $appointment['id'] ?>">

                        <div class="mb-4">
                            <label class="form-label">Assigned Medical Officer</label>
                            <div class="input-group shadow-sm rounded-3">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-person-badge text-danger"></i></span>
                                <input type="text" name="doctor_name" class="form-control border-start-0" value="<?= htmlspecialchars($appointment['doctor_name']) ?>" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Scheduled Date</label>
                                <input type="date" name="appointment_date" class="form-control shadow-sm" value="<?= $appointment['appointment_date'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Current Status</label>
                                <select name="status" class="form-select shadow-sm">
                                    <option value="pending" <?= $appointment['status'] == 'pending' ? 'selected' : '' ?>>Pending Approval</option>
                                    <option value="confirmed" <?= $appointment['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="completed" <?= $appointment['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= $appointment['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3">
                            <button type="submit" class="btn btn-danger px-5 py-3 rounded-pill fw-bold shadow">
                                <i class="bi bi-check-circle me-2"></i>Save Changes
                            </button>
                            <a href="staff_dashboard.php" class="btn btn-light px-4 py-3 rounded-pill border">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="glass-card p-4 mb-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-person-circle me-2 text-danger"></i>Patient Summary</h5>
                    <div class="patient-info-tile">
                        <div class="fw-bold fs-5 text-uppercase"><?= htmlspecialchars($appointment['name']) ?></div>
                        <div class="text-muted small mb-3"><?= htmlspecialchars($appointment['email']) ?></div>
                        
                        <div class="p-3 bg-white rounded-3 border shadow-sm">
                            <span class="form-label d-block mb-1" style="font-size: 0.65rem;">Symptoms Reported:</span>
                            <p class="mb-0 text-dark" style="font-style: italic;">"<?= htmlspecialchars($appointment['symptoms']) ?>."</p>
                        </div>
                    </div>
                </div>

                <div class="glass-card p-4 border-start border-danger border-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-square me-2 text-danger"></i>System Metadata</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Record ID:</span>
                        <span class="fw-bold small">PMC-APP-<?= $appointment['id'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Last Modified:</span>
                        <span class="fw-bold small"><?= date('d M Y, H:i') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>