<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

// Statistics Logic
$total_apps = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$pending_apps = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();
$total_patients = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'patient'")->fetchColumn();

// Search & Filter Logic
$search = $_GET['search'] ?? '';
$query = "SELECT appointments.*, users.name, users.phone 
          FROM appointments 
          JOIN users ON appointments.patient_id = users.id 
          WHERE users.name LIKE ? OR users.phone LIKE ? 
          ORDER BY appointment_date DESC, appointment_time ASC";
$stmt = $pdo->prepare($query);
$stmt->execute(["%$search%", "%$search%"]);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | PMC</title>
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
            background: linear-gradient(rgba(248, 249, 250, 0.92), rgba(248, 249, 250, 0.92)), 
                        url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
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
            z-index: 1000;
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: 0.3s;
            border-bottom: 4px solid #eee;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-bottom-color: var(--pmc-red);
        }

        .glass-table-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }

        .table thead {
            background: #212529;
            color: white;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
        }

        .status-pill {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .queue-id {
            background: #f8f9fa;
            color: var(--pmc-red);
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 5px;
            font-size: 0.75rem;
            border: 1px solid #eee;
        }

        .profile-trigger:hover {
            transform: scale(1.02);
            background: #fdfdfd;
            border-left-color: #212529;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <nav id="sidebar" class="d-none d-md-block shadow">
        <div class="p-4 text-center border-bottom border-secondary border-opacity-25">
            <h4 class="fw-bold mb-0 text-white">PENAWAR <span class="text-danger">STAFF</span></h4>
            <small class="text-muted opacity-50">Management Portal</small>
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
                <li class="nav-item mt-5">
                    <a href="login.php" class="nav-link text-danger">
                        <i class="bi bi-door-open-fill me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="flex-grow-1 p-4 p-md-5">
        <header class="row align-items-center mb-5">
            <div class="col-md-7">
                <h2 class="fw-bold mb-0">Clinic Overview</h2>
                <p class="text-muted" id="currentDate"></p>
            </div>
            <div class="col-md-5">
                <a href="staff_profile.php" class="text-decoration-none">
                    <div class="staff-id-card ms-auto profile-trigger" style="max-width: 300px; cursor: pointer; transition: 0.3s;">
                        <?php 
                            $profile_pic = !empty($_SESSION['profile_pic']) ? 'uploads/'.$_SESSION['profile_pic'] : 'https://ui-avatars.com/api/?name='.urlencode($_SESSION['name']).'&background=212529&color=fff';
                        ?>
                        <img src="<?= $profile_pic ?>" class="rounded shadow-sm object-fit-cover" style="width:50px; height:50px;">
                        <div>
                            <div class="fw-bold small text-dark"><?= $_SESSION['name'] ?></div>
                            <div class="text-danger fw-bold" style="font-size: 0.65rem;">SENIOR CLINIC STAFF</div>
                            <div class="text-muted" style="font-size: 0.6rem;"><i class="bi bi-pencil-square me-1"></i>Edit Profile</div>
                        </div>
                    </div>
                </a>
            </div>
        </header>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="text-muted small fw-bold">TOTAL APPOINTMENTS</div>
                    <div class="h2 fw-bold mb-0"><?= $total_apps ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="text-warning small fw-bold">PENDING QUEUE</div>
                    <div class="h2 fw-bold mb-0 text-warning"><?= $pending_apps ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="text-primary small fw-bold">ACTIVE PATIENTS</div>
                    <div class="h2 fw-bold mb-0"><?= $total_patients ?></div>
                </div>
            </div>
        </div>

        <div class="glass-table-card shadow">
            <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Patient Queue</h5>
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3" placeholder="Search name..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-4">Filter</button>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Patient Profile</th>
                            <th>Contact</th>
                            <th>Schedule</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $row): ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="queue-id mb-1 d-inline-block">ID: #<?= $row['id'] ?></div>
                                <div class="fw-bold"><?= htmlspecialchars($row['name']) ?></div>
                                <div class="text-muted small text-truncate" style="max-width: 200px;"><?= htmlspecialchars($row['symptoms']) ?></div>
                            </td>
                            <td>
                                <div class="small fw-bold"><i class="bi bi-telephone text-danger me-2"></i><?= htmlspecialchars($row['phone']) ?></div>
                            </td>
                            <td>
                                <div class="fw-bold small"><?= date('d M Y', strtotime($row['appointment_date'])) ?></div>
                                <span class="badge bg-light text-dark border"><?= $row['appointment_time'] ?></span>
                            </td>
                            <td>
                                <?php 
                                    $statusClass = match($row['status']) {
                                        'pending' => 'bg-warning text-dark',
                                        'confirmed' => 'bg-success text-white',
                                        'cancelled' => 'bg-danger text-white',
                                        default => 'bg-secondary text-white'
                                    };
                                ?>
                                <span class="status-pill <?= $statusClass ?> text-uppercase"><?= $row['status'] ?></span>
                            </td>
                            <td class="text-center">
                                <a href="edit_appointment.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold">
                                    Update Status
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        document.getElementById('currentDate').textContent = now.toLocaleDateString('en-MY', options);
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>