<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = "";

// Fetch current info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $profile_pic = $user['profile_pic'] ?? ''; 

    // Handle Image Upload
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $file_name = "staff_" . $user_id . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            $profile_pic = $file_name;
            $_SESSION['profile_pic'] = $profile_pic; 
        }
    }

    // Update Database
    $update = $pdo->prepare("UPDATE users SET name = ?, phone = ?, address = ?, profile_pic = ? WHERE id = ?");
    if ($update->execute([$name, $phone, $address, $profile_pic, $user_id])) {
        $_SESSION['name'] = $name; 
        $success_msg = "Profile updated successfully!";
        // Refresh local user data
        $user['name'] = $name; $user['phone'] = $phone; $user['address'] = $address; $user['profile_pic'] = $profile_pic;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Settings | PMC</title>
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
        }

        /* Sidebar Styling */
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

        /* Profile Card Styling */
        .profile-container {
            max-width: 800px;
            margin: auto;
        }

        .glass-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: none;
        }

        .avatar-section {
            position: relative;
            width: 150px;
            margin: 0 auto 30px;
        }

        .avatar-preview {
            width: 150px;
            height: 150px;
            border-radius: 20px;
            object-fit: cover;
            border: 5px solid #f8f9fa;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .file-input-wrapper {
            position: absolute;
            bottom: -10px;
            right: -10px;
        }

        .btn-upload {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--pmc-red);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 3px solid white;
            transition: 0.3s;
        }

        .btn-upload:hover { transform: scale(1.1); }

        .form-label {
            font-weight: 700;
            color: var(--pmc-red);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .form-control {
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            background-color: #f8f9fa;
            padding: 12px 20px;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: var(--pmc-red);
            box-shadow: none;
            background-color: white;
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
        <div class="profile-container">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="staff_dashboard.php" class="text-danger text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Staff Settings</li>
                </ol>
            </nav>

            <div class="glass-card">
                <div class="text-center mb-4">
                    <h3 class="fw-bold mb-1">My Profile</h3>
                    <p class="text-muted small">Update your personal information and profile picture</p>
                </div>

                <?php if($success_msg): ?>
                    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i> <?= $success_msg ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="avatar-section">
                        <?php 
                            $display_pic = !empty($user['profile_pic']) ? 'uploads/'.$user['profile_pic'] : 'https://ui-avatars.com/api/?name='.urlencode($user['name']).'&size=150&background=212529&color=fff';
                        ?>
                        <img src="<?= $display_pic ?>" class="avatar-preview" id="imgPreview">
                        <div class="file-input-wrapper">
                            <label for="fileInput" class="btn-upload">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                            <input type="file" name="profile_image" id="fileInput" class="d-none" accept="image/*">
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Residential Address</label>
                            <textarea name="address" class="form-control" rows="3" placeholder="Enter your full address..."><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12 mt-5">
                            <button type="submit" class="btn btn-danger w-100 rounded-pill py-3 fw-bold shadow">
                                <i class="bi bi-cloud-arrow-up me-2"></i>Update Profile Information
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <footer class="mt-5 text-center text-muted small">
                Penawar Medical Group &copy; <?= date('Y') ?> | Staff Security Portal
            </footer>
        </div>
    </div>
</div>

<script>
    // Live preview of the uploaded image
    document.getElementById('fileInput').onchange = evt => {
        const [file] = document.getElementById('fileInput').files
        if (file) {
            document.getElementById('imgPreview').src = URL.createObjectURL(file)
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>