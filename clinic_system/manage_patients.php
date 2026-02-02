<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

// Search Logic for Patients
$search = $_GET['search'] ?? '';
$query = "SELECT id, name, email, phone, created_at FROM users 
          WHERE role = 'patient' 
          AND (name LIKE ? OR phone LIKE ? OR email LIKE ?)
          ORDER BY name ASC";
$stmt = $pdo->prepare($query);
$stmt->execute(["%$search%", "%$search%", "%$search%"]);
$patients = $stmt->fetchAll();

// Statistics for Patients
$total_patients = count($patients);
$new_today = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'patient' AND DATE(created_at) = CURDATE()")->fetchColumn();

// NEW: Fetch all completed appointments to show history
$completed_query = "SELECT appointments.*, users.name 
                    FROM appointments 
                    JOIN users ON appointments.patient_id = users.id 
                    WHERE status = 'completed' OR status = 'confirmed'
                    ORDER BY appointment_date DESC";
$completed_cases = $pdo->query($completed_query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Directory | PMC Staff</title>
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
            /* ALIGNED BACKGROUND: Noticeable gradient from dashboard */
            background: radial-gradient(circle at top right, #fdfbfb 0%, #ebedee 100%);
            background-attachment: fixed;
            min-height: 100vh;
            color: #333;
        }

        #sidebar {
            min-width: 280px;
            max-width: 280px;
            background: var(--pmc-sidebar);
            min-height: 100vh;
            color: white;
            position: sticky;
            top: 0;
            transition: 0.3s;
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

        .staff-id-card {
            background: white;
            border-radius: 15px;
            padding: 15px;
            border-left: 5px solid var(--pmc-red);
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .stat-card {
            background: white;
            border: none;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            border-bottom: 4px solid #eee;
            transition: 0.3s;
        }

        .glass-table-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: 1px solid #eee;
            margin-bottom: 30px;
        }

        .table thead {
            background: #212529;
            color: white;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
        }

        .patient-avatar {
            width: 40px;
            height: 40px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--pmc-red);
            border: 1px solid #dee2e6;
        }

        .medical-badge {
            background: #e9ecef;
            color: #495057;
            font-size: 0.7rem;
            padding: 4px 10px;
            border-radius: 50px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <nav id="sidebar" class="d-none d-md-block shadow">
        <div class="p-4 text-center border-bottom border-secondary border-opacity-25">
            <h4 class="fw-bold mb-0 text-white">PENAWAR <span class="text-danger">STAFF</span></h4>
            <small class="text-muted">Control Panel v2.0</small>
        </div>
        
        <div class="p-3">
            <ul class="nav flex-column mt-3">
                <li class="nav-item mb-2"><a href="staff_dashboard.php" class="nav-link"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a></li>
                <li class="nav-item mb-2"><a href="manage_patients.php" class="nav-link active shadow-sm"><i class="bi bi-people-fill me-2"></i> Patients</a></li>
                <li class="nav-item mb-2"><a href="inventory.php" class="nav-link"><i class="bi bi-box-seam-fill me-2"></i> Inventory</a></li>
                <li class="nav-item mb-2"><a href="reports.php" class="nav-link"><i class="bi bi-file-bar-graph-fill me-2"></i> Reports</a></li>
                <li class="nav-item mt-5 pt-5"><a href="login.php" class="nav-link text-danger"><i class="bi bi-door-open-fill me-2"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="flex-grow-1 p-4 p-md-5">
        <header class="row align-items-center mb-5">
            <div class="col-md-7">
                <h2 class="fw-bold mb-0">Patient Directory</h2>
                <p class="text-muted">Archive of all registered members and clinical records</p>
            </div>
            <div class="col-md-5">
                <div class="staff-id-card ms-auto" style="max-width: 300px;">
                    <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['name'] ?>&background=212529&color=fff" class="rounded shadow-sm" style="width:50px;">
                    <div>
                        <div class="fw-bold small"><?= $_SESSION['name'] ?></div>
                        <div class="text-danger fw-bold" style="font-size: 0.65rem;">SENIOR CLINIC STAFF</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="text-muted small fw-bold">TOTAL REGISTERED</div>
                    <div class="h2 fw-bold mb-0 text-dark"><?= $total_patients ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card border-danger border-bottom border-4">
                    <div class="text-danger small fw-bold">NEW MEMBERS TODAY</div>
                    <div class="h2 fw-bold mb-0 text-danger"><?= $new_today ?></div>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-3"><i class="bi bi-journal-medical text-danger me-2"></i>Recent Clinical Archives</h5>
        <div class="glass-table-card">
            <div class="p-4 bg-white border-bottom">
                <p class="small text-muted mb-0">List of patients with finalized medical notes and diagnosis.</p>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Patient Name</th>
                            <th>Diagnosis/Notes</th>
                            <th>Treatment Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($completed_cases, 0, 5) as $case): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-dark"><?= htmlspecialchars($case['name']) ?></td>
                            <td>
                                <div class="text-truncate" style="max-width: 300px;">
                                    <span class="medical-badge">Note</span> 
                                    <small><?= htmlspecialchars($case['clinical_notes'] ?? 'No notes provided') ?></small>
                                </div>
                            </td>
                            <td><small><i class="bi bi-calendar-event me-1"></i><?= date('d M Y', strtotime($case['appointment_date'])) ?></small></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light rounded-pill border shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#viewRecord<?= $case['id'] ?>">
                                    <i class="bi bi-eye"></i> View Full
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="viewRecord<?= $case['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow">
                                    <div class="modal-header border-0 bg-light rounded-top-4">
                                        <h6 class="modal-title fw-bold">Medical Archive #<?= $case['id'] ?></h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="text-muted small text-uppercase fw-bold">Patient Name</label>
                                            <p class="fw-bold mb-0"><?= htmlspecialchars($case['name']) ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small text-uppercase fw-bold">Reported Symptoms</label>
                                            <p class="small bg-light p-2 rounded"><?= htmlspecialchars($case['symptoms']) ?></p>
                                        </div>
                                        <hr>
                                        <div>
                                            <label class="text-danger small text-uppercase fw-bold">Clinical Diagnosis & Treatment</label>
                                            <p class="mb-0" style="white-space: pre-wrap;"><?= htmlspecialchars($case['clinical_notes'] ?? 'Pending doctor input...') ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <h5 class="fw-bold mb-3"><i class="bi bi-people-fill text-dark me-2"></i>Full Directory</h5>
        <div class="glass-table-card">
            <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Registered Patient List</h6>
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3 shadow-sm" placeholder="Search records..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm">Filter</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Patient Profile</th>
                            <th>Contact Info</th>
                            <th>Reg. Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="patient-avatar shadow-sm"><?= strtoupper(substr($patient['name'], 0, 1)) ?></div>
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($patient['name']) ?></div>
                                        <small class="text-muted">ID: #<?= str_pad($patient['id'], 5, '0', STR_PAD_LEFT) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small"><i class="bi bi-envelope text-muted me-2"></i><?= htmlspecialchars($patient['email']) ?></div>
                                <div class="small fw-bold"><i class="bi bi-phone text-danger me-2"></i><?= htmlspecialchars($patient['phone']) ?></div>
                            </td>
                            <td><div class="small fw-bold text-dark"><?= date('d M Y', strtotime($patient['created_at'])) ?></div></td>
                            <td class="text-center">
                                <a href="view_medical_record.php?id=<?= $patient['id'] ?>" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-bold shadow-sm">View File</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>