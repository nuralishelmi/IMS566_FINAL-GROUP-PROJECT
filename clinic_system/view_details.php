<?php
session_start();
include 'db.php';

// 1. Check if ANYONE is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'] ?? null;
$current_user_id = $_SESSION['user_id'];
$current_role = $_SESSION['role'];

if (!$id) {
    header("Location: " . ($current_role === 'staff' ? "staff_dashboard.php" : "patient_dashboard.php"));
    exit();
}

// 2. Fetch data with security check
$query = "SELECT appointments.*, users.name, users.email, users.phone, users.address 
          FROM appointments 
          JOIN users ON appointments.patient_id = users.id 
          WHERE appointments.id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    die("Record not found.");
}

// 3. Privacy Filter
if ($current_role === 'patient' && $data['patient_id'] != $current_user_id) {
    echo "<script>alert('Unauthorized access!'); window.location='patient_dashboard.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details | PMC</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root { 
            --pmc-red: #dc3545; 
            --pmc-dark: #212529;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background: linear-gradient(rgba(248, 249, 250, 0.92), rgba(248, 249, 250, 0.92)), 
                        url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
        }

        .card-header-pmc {
            background: var(--pmc-dark);
            color: white;
            padding: 25px;
            border-bottom: 4px solid var(--pmc-red);
        }

        .info-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--pmc-red);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 1rem;
            color: var(--pmc-dark);
            margin-bottom: 20px;
            font-weight: 500;
        }

        .symptoms-box {
            background: #f8f9fa;
            border-left: 4px solid var(--pmc-red);
            padding: 15px;
            border-radius: 10px;
            font-style: italic;
        }

        .btn-pmc {
            background: var(--pmc-red);
            color: white;
            border-radius: 50px;
            padding: 8px 25px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-pmc:hover {
            background: #a71d2a;
            color: white;
            transform: translateY(-2px);
        }

        @media print {
            .no-print { display: none; }
            body { background: white; }
            .glass-card { box-shadow: none; border: 1px solid #eee; }
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            
            <div class="mb-4 no-print">
                <a href="<?= $current_role === 'staff' ? 'staff_dashboard.php' : 'patient_dashboard.php' ?>" class="text-decoration-none text-dark fw-bold">
                    <i class="bi bi-arrow-left-circle-fill me-2 text-danger"></i> Return to Dashboard
                </a>
            </div>

            <div class="glass-card">
                <div class="card-header-pmc d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">Medical Record</h4>
                        <small class="opacity-75">Ref: #PMC-<?= str_pad($data['id'], 5, '0', STR_PAD_LEFT) ?></small>
                    </div>
                    <i class="bi bi-hospital fs-2"></i>
                </div>

                <div class="p-4 p-md-5">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-label">Patient Name</div>
                            <div class="info-value h5"><?= htmlspecialchars($data['name']) ?></div>

                            <div class="info-label">Phone Number</div>
                            <div class="info-value"><?= htmlspecialchars($data['phone']) ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Status</div>
                            <div class="mb-3">
                                <?php 
                                    $st = strtolower($data['status']);
                                    $badge = ($st == 'confirmed') ? 'bg-success' : (($st == 'pending') ? 'bg-warning text-dark' : 'bg-danger');
                                ?>
                                <span class="badge <?= $badge ?> rounded-pill px-3 py-2 fw-bold text-uppercase" style="font-size: 0.7rem;">
                                    <?= htmlspecialchars($data['status']) ?>
                                </span>
                            </div>

                            <div class="info-label">Appointment Time</div>
                            <div class="info-value">
                                <i class="bi bi-calendar3 me-2"></i><?= date('d M Y', strtotime($data['appointment_date'])) ?><br>
                                <i class="bi bi-clock me-2"></i><?= $data['appointment_time'] ?>
                            </div>
                        </div>
                    </div>

                    <div class="info-label">Reason for Visit / Symptoms</div>
                    <div class="symptoms-box mb-4">
                        "<?= nl2br(htmlspecialchars($data['symptoms'])) ?>"
                    </div>

                    <hr class="my-4 opacity-25">

                    <div class="d-flex justify-content-between align-items-center no-print">
                        <button onclick="window.print()" class="btn btn-outline-dark rounded-pill px-4">
                            <i class="bi bi-printer me-2"></i> Print
                        </button>
                        
                        <?php if($current_role === 'staff'): ?>
                            <a href="edit_appointment.php?id=<?= $data['id'] ?>" class="btn btn-pmc shadow-sm">
                                Update Status
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="bg-light p-3 text-center">
                    <small class="text-muted" style="font-size: 0.65rem;">
                        This is an official record from Penawar Medical Clinic. Generated on <?= date('d/m/Y H:i') ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>