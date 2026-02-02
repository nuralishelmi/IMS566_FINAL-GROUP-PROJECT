<?php
session_start();
include 'db.php';

// Staff Authentication Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$app_id = $_GET['id'];

// Fetch Appointment, Patient, and existing Medical Record Data
$stmt = $pdo->prepare("
    SELECT a.*, u.name, u.email, u.phone, u.address, mr.diagnosis, mr.prescription 
    FROM appointments a 
    JOIN users u ON a.patient_id = u.id 
    LEFT JOIN medical_records mr ON a.id = mr.appointment_id
    WHERE a.id = ?
");
$stmt->execute([$app_id]);
$data = $stmt->fetch();

if (!$data) {
    header("Location: staff_dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $diagnosis = $_POST['diagnosis'];
    $prescription = $_POST['prescription'];
    
    $check = $pdo->prepare("SELECT id FROM medical_records WHERE appointment_id = ?");
    $check->execute([$app_id]);
    
    if ($check->fetch()) {
        $update = $pdo->prepare("UPDATE medical_records SET diagnosis = ?, prescription = ? WHERE appointment_id = ?");
        $update->execute([$diagnosis, $prescription, $app_id]);
    } else {
        $insert = $pdo->prepare("INSERT INTO medical_records (appointment_id, diagnosis, prescription) VALUES (?, ?, ?)");
        $insert->execute([$app_id, $diagnosis, $prescription]);
    }
    header("Location: view_medical_record.php?id=" . $app_id . "&success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinical Record | PMC Staff</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root { 
            --pmc-red: #dc3545; 
            --pmc-dark: #212529;
            --pmc-sidebar: #212529;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            /* ALIGNED BACKGROUND IMAGE */
            background: linear-gradient(rgba(248, 249, 250, 0.92), rgba(248, 249, 250, 0.92)), 
                        url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
        }

        /* Sidebar Alignment */
        #sidebar {
            min-width: 280px;
            max-width: 280px;
            background: var(--pmc-sidebar);
            min-height: 100vh;
            color: white;
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

        /* Report Box Styling */
        .report-box { 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: none;
            position: relative;
            overflow: hidden;
            max-width: 1000px;
            margin: auto;
        }

        .clinic-header { border-bottom: 2px solid #eee; padding-bottom: 20px; }
        
        .form-label { 
            font-weight: 700; 
            color: var(--pmc-red); 
            font-size: 0.75rem; 
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-control { 
            border: 1px solid #f0f0f0; 
            border-radius: 12px; 
            background-color: #f8f9fa;
            padding: 15px;
            transition: 0.3s;
        }

        .form-control:focus { 
            border-color: var(--pmc-red); 
            box-shadow: none; 
            background-color: white; 
        }

        .info-strip {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            border-left: 5px solid var(--pmc-red);
        }

        .staff-id-card {
            background: white;
            border-radius: 15px;
            padding: 10px 15px;
            border-left: 5px solid var(--pmc-red);
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        @media print {
            .no-print, #sidebar, .breadcrumb { display: none !important; }
            body { background: white !important; }
            .report-box { box-shadow: none; padding: 0; width: 100%; border-radius: 0; }
            .form-control { border: none !important; background: transparent !important; padding: 0; }
            .flex-grow-1 { padding: 0 !important; }
        }
    </style>
</head>
<body>

<div class="d-flex">
    <nav id="sidebar">
        <div class="p-4 text-center border-bottom border-secondary border-opacity-25">
            <h4 class="fw-bold mb-0 text-white">PENAWAR <span class="text-danger">STAFF</span></h4>
            <small class="text-muted">Control Panel v2.0</small>
        </div>
        <div class="p-3">
            <ul class="nav flex-column mt-3">
                <li class="nav-item mb-2"><a href="staff_dashboard.php" class="nav-link"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a></li>
                <li class="nav-item mb-2"><a href="manage_patients.php" class="nav-link"><i class="bi bi-people-fill me-2"></i> Patients</a></li>
                <li class="nav-item mb-2"><a href="inventory.php" class="nav-link active shadow-sm"><i class="bi bi-box-seam-fill me-2"></i> Inventory</a></li>
                <li class="nav-item mb-2"><a href="reports.php" class="nav-link"><i class="bi bi-file-bar-graph-fill me-2"></i> Reports</a></li>
                <li class="nav-item mt-5"><a href="login.php" class="nav-link text-danger"><i class="bi bi-door-open-fill me-2"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="flex-grow-1 p-4 p-md-5">
        <div class="no-print d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="manage_patients.php" class="text-danger">Directory</a></li>
                        <li class="breadcrumb-item active">Update Record</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0">Medical Consultation</h3>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 shadow-sm">
                    <i class="bi bi-printer me-2"></i>Print / PDF
                </button>
                <div class="staff-id-card d-none d-lg-flex">
                    <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['name'] ?>&background=212529&color=fff" class="rounded shadow-sm" style="width:35px;">
                    <div class="small fw-bold"><?= $_SESSION['name'] ?></div>
                </div>
            </div>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 no-print mb-4">
                <i class="bi bi-check-circle-fill me-2"></i> Medical record synchronized and saved successfully.
            </div>
        <?php endif; ?>

        <div class="report-box">
            <div class="clinic-header d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="fw-bold mb-0 text-dark">PENAWAR <span class="text-danger">CLINIC</span></h2>
                    <p class="text-muted small mb-0">
                        Penawar Medical Group Sdn. Bhd. (12345-X)<br>
                        123 Medical Avenue, Kuala Lumpur, 50450<br>
                        <strong>T:</strong> 03-1234 5678 | <strong>E:</strong> records@penawarclinic.com.my
                    </p>
                </div>
                <div class="text-end">
                    <div class="bg-light p-2 rounded border mb-2 d-inline-block">
                        <i class="bi bi-qr-code" style="font-size: 30px;"></i>
                    </div>
                    <div class="small fw-bold d-block text-uppercase text-muted" style="font-size: 0.6rem;">Reference No.</div>
                    <div class="fw-bold">PMC-<?= date('Y') ?>-<?= str_pad($data['id'], 4, '0', STR_PAD_LEFT) ?></div>
                </div>
            </div>

            <div class="text-center my-4 py-2 border-top border-bottom" style="background: #fcfcfc;">
                <h5 class="fw-bold mb-0" style="letter-spacing: 2px; font-size: 0.9rem;">OFFICIAL MEDICAL CONSULTATION REPORT</h5>
            </div>

            <div class="info-strip mb-4">
                <div class="row g-0">
                    <div class="col-md-6 border-end pe-4">
                        <label class="form-label">Patient Information</label>
                        <div class="fw-bold fs-5 text-uppercase text-dark"><?= htmlspecialchars($data['name']) ?></div>
                        <div class="text-muted small">
                            <i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($data['phone']) ?><br>
                            <i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($data['email']) ?>
                        </div>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <label class="form-label">Appointment Details</label>
                        <div class="small">
                            <strong>Date:</strong> <?= date('d F Y', strtotime($data['appointment_date'])) ?><br>
                            <strong>Physician:</strong> <?= htmlspecialchars($data['doctor_name']) ?><br>
                            <strong>Outcome:</strong> <span class="text-success fw-bold">COMPLETED</span>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST">
                <div class="mb-4">
                    <label class="form-label"><i class="bi bi-activity me-2"></i>Clinical Diagnosis</label>
                    <textarea name="diagnosis" class="form-control" rows="4" required placeholder="Describe clinical findings..."><?= htmlspecialchars($data['diagnosis'] ?? '') ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label"><i class="bi bi-capsule me-2"></i>Prescription & Medication</label>
                    <textarea name="prescription" class="form-control" rows="4" required placeholder="List dosages and instructions..."><?= htmlspecialchars($data['prescription'] ?? '') ?></textarea>
                </div>

                <div class="row mt-5 pt-4 border-top">
                    <div class="col-6">
                        <div style="border-bottom: 2px solid #333; width: 180px; height: 50px;"></div>
                        <p class="small fw-bold mt-2 mb-0">Authorized Signature</p>
                        <p class="small text-muted text-uppercase"><?= htmlspecialchars($data['doctor_name']) ?></p>
                    </div>
                    <div class="col-6 text-end d-flex align-items-center justify-content-end">
                        <button type="submit" class="btn btn-danger no-print py-3 px-5 fw-bold rounded-pill shadow">
                            <i class="bi bi-save2 me-2"></i>Save Clinical Record
                        </button>
                    </div>
                </div>
            </form>

            <footer class="mt-5 text-center text-muted small border-top pt-3" style="font-size: 0.7rem;">
                This document is a computer-generated medical record from Penawar Clinic Management System. 
                All data is encrypted and handled in accordance with the Malaysian PDPA 2010.
            </footer>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>