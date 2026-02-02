<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

// --- PHP DATA LOGIC (UNTOUCHED) ---
$monthly_query = $pdo->query("SELECT MONTHNAME(appointment_date) as month, COUNT(*) as count FROM appointments GROUP BY MONTHNAME(appointment_date), MONTH(appointment_date) ORDER BY MONTH(appointment_date) ASC");
$months = []; $counts = [];
foreach($monthly_query as $row) { $months[] = $row['month']; $counts[] = $row['count']; }

$doc_query = $pdo->query("SELECT doctor_name, COUNT(*) as count FROM appointments GROUP BY doctor_name");
$doc_names = []; $doc_counts = [];
foreach($doc_query as $row) { $doc_names[] = $row['doctor_name']; $doc_counts[] = $row['count']; }

$time_query = $pdo->query("SELECT appointment_time, COUNT(*) as count FROM appointments GROUP BY appointment_time ORDER BY count DESC LIMIT 5");
$times = []; $time_counts = [];
foreach($time_query as $row) { $times[] = $row['appointment_time']; $time_counts[] = $row['count']; }

$total_patients = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'patient'")->fetchColumn();
$total_apps = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$cancelled = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'cancelled'")->fetchColumn();
$cancel_rate = ($total_apps > 0) ? round(($cancelled / $total_apps) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics | PMC Staff</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root { 
            --pmc-red: #dc3545; 
            --pmc-dark: #212529;
            --pmc-sidebar: #212529;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background: linear-gradient(rgba(248, 249, 250, 0.95), rgba(248, 249, 250, 0.95)), 
                        url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            color: #333;
        }

        /* Sidebar Styling (Matches Dashboard) */
        #sidebar {
            min-width: 280px;
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

        /* Staff ID Card (Matches Dashboard) */
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

        .staff-avatar {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            border: 2px solid #eee;
        }

        /* Analytics Cards */
        .glass-card { 
            background: white; 
            border-radius: 20px; 
            border: 1px solid #eee; 
            padding: 24px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            height: 100%;
        }

        .stat-icon { 
            width: 48px; height: 48px; border-radius: 12px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 1.5rem; margin-bottom: 15px;
        }

        h2, h5 { font-weight: 700; color: var(--pmc-dark); }
        .chart-container { position: relative; height: 300px; width: 100%; }
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
                <li class="nav-item mb-2">
                    <a href="staff_dashboard.php" class="nav-link"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="manage_patients.php" class="nav-link"><i class="bi bi-people-fill me-2"></i> Patients</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="inventory.php" class="nav-link"><i class="bi bi-box-seam-fill me-2"></i> Inventory</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="reports.php" class="nav-link active shadow-sm"><i class="bi bi-file-bar-graph-fill me-2"></i> Reports</a>
                </li>
                <li class="nav-item mt-5">
                    <a href="login.php" class="nav-link text-danger"><i class="bi bi-door-open-fill me-2"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="flex-grow-1 p-4 p-md-5">
        <header class="row align-items-center mb-5">
            <div class="col-md-7">
                <h2 class="fw-bold mb-0">Clinic Analytics</h2>
                <p class="text-muted">Data Insights for <?= date('F Y') ?></p>
            </div>
            <div class="col-md-5">
                <div class="staff-id-card ms-auto" style="max-width: 300px;">
                    <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['name'] ?>&background=212529&color=fff" class="staff-avatar">
                    <div>
                        <div class="fw-bold small"><?= $_SESSION['name'] ?></div>
                        <div class="text-danger fw-bold" style="font-size: 0.65rem;">SENIOR CLINIC STAFF</div>
                        <div class="badge bg-success p-1 px-2 mt-1" style="font-size: 0.5rem;">ONLINE</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="glass-card text-center">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger mx-auto"><i class="bi bi-people"></i></div>
                    <div class="h2 fw-bold mb-0"><?= $total_patients ?></div>
                    <div class="text-muted small fw-bold">TOTAL PATIENTS</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card text-center">
                    <div class="stat-icon bg-dark text-white mx-auto"><i class="bi bi-calendar-check"></i></div>
                    <div class="h2 fw-bold mb-0"><?= $total_apps ?></div>
                    <div class="text-muted small fw-bold">TOTAL APPOINTMENTS</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card text-center">
                    <div class="stat-icon bg-danger text-white mx-auto"><i class="bi bi-exclamation-triangle"></i></div>
                    <div class="h2 fw-bold mb-0"><?= $cancel_rate ?>%</div>
                    <div class="text-muted small fw-bold">CANCEL RATE</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="glass-card">
                    <h5 class="mb-4">Appointment Trends</h5>
                    <div class="chart-container">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="glass-card text-center">
                    <h5 class="mb-4">Doctor Load</h5>
                    <div class="chart-container">
                        <canvas id="caseChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="glass-card">
                    <h5 class="mb-4">Peak Appointment Hours</h5>
                    <div class="chart-container" style="height: 200px;">
                        <canvas id="peakChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Chart Configurations using Black, White, Red
const ctx1 = document.getElementById('growthChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Visits',
            data: <?= json_encode($counts) ?>,
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220, 53, 69, 0.05)',
            fill: true,
            tension: 0.4,
            borderWidth: 3
        }]
    },
    options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
});

const ctx2 = document.getElementById('caseChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($doc_names) ?>,
        datasets: [{
            data: <?= json_encode($doc_counts) ?>,
            backgroundColor: ['#212529', '#dc3545', '#adb5bd', '#6c757d'],
            borderWidth: 2
        }]
    },
    options: { maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom' } } }
});

const ctx3 = document.getElementById('peakChart').getContext('2d');
new Chart(ctx3, {
    type: 'bar',
    data: {
        labels: <?= json_encode($times) ?>,
        datasets: [{
            data: <?= json_encode($time_counts) ?>,
            backgroundColor: '#212529',
            borderRadius: 5
        }]
    },
    options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
});
</script>
</body>
</html>