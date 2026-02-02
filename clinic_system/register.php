<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['name'];
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $phone    = $_POST['phone'];
    $address  = $_POST['address'];
    
    // REVERTED: Saving password as plain text as requested
    $password = $_POST['password']; 
    
    $role     = $_POST['role'];
    $staff_id = ($role == 'staff') ? $_POST['staff_id'] : null;

    $sql = "INSERT INTO users (name, username, email, phone, address, password, role, staff_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    try {
        if($stmt->execute([$name, $username, $email, $phone, $address, $password, $role, $staff_id])) {
            echo "<script>alert('Registration Successful! Please login.'); window.location='index.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: Email or Username already exists.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration | PMC Portal</title>
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
            background: linear-gradient(rgba(248, 249, 250, 0.95), rgba(248, 249, 250, 0.95)), 
                        url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        /* Sidebar Branding to match inventory */
        #sidebar-branding {
            min-width: 320px;
            background: var(--pmc-sidebar);
            min-height: 90vh;
            color: white;
            border-radius: 20px 0 0 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            padding: 40px;
        }

        .main-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #eee;
        }

        .form-panel { padding: 50px; }

        .form-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--pmc-dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
            font-size: 0.9rem;
        }

        .form-control:focus {
            border-color: var(--pmc-red);
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.1);
            background-color: #fff;
        }

        .btn-register {
            background: var(--pmc-red);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 700;
            width: 100%;
            transition: 0.3s;
        }

        .btn-register:hover {
            background: var(--pmc-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .staff-box {
            background: #fff5f5;
            border-radius: 12px;
            padding: 15px;
            border: 1px dashed var(--pmc-red);
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-link { color: var(--pmc-red); font-weight: 700; text-decoration: none; }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">
            <div class="main-card">
                <div class="row g-0">
                    <div class="col-md-5 col-lg-4" id="sidebar-branding">
                        <div class="mb-4">
                            <i class="bi bi-heart-pulse-fill text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="fw-bold mb-0">PENAWAR <span class="text-danger">MEDICAL</span></h2>
                        <p class="text-white-50 small mb-4">Portal v2.0</p>
                        <hr class="border-secondary opacity-25">
                        <p class="small px-3">Modernized healthcare management for staff and patients.</p>
                        <div class="mt-5">
                            <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-4 mt-3">
                                <i class="bi bi-arrow-left me-2"></i>Back to Login
                            </a>
                        </div>
                    </div>

                    <div class="col-md-7 col-lg-8 form-panel">
                        <div class="mb-5">
                            <h3 class="fw-bold text-dark mb-1">Create Account</h3>
                            <p class="text-muted">Fill in the details to join the PMC network.</p>
                        </div>

                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Ariff Rahman" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" placeholder="ariff_p" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" placeholder="name@email.com" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="012-XXXXXXX" required>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Home Address</label>
                                    <textarea name="address" class="form-control" rows="2" placeholder="Street, City, Postal Code" required></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Account Role</label>
                                    <select name="role" id="role" class="form-select" onchange="toggleStaffField()" required>
                                        <option value="patient">Patient User</option>
                                        <option value="staff">Clinic Staff</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Security Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                                </div>

                                <div class="col-12 d-none mb-4" id="staff_id_container">
                                    <div class="staff-box">
                                        <label class="form-label text-danger">Verification Required</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="bi bi-shield-lock text-danger"></i></span>
                                            <input type="text" name="staff_id" id="staff_id" class="form-control" placeholder="Enter Staff ID (e.g., PMC-STAFF-XX)">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-register py-3">Register Now</button>
                                </div>
                            </div>

                            <div class="text-center mt-5">
                                <span class="text-muted small">Already part of PMC?</span> 
                                <a href="login.php" class="login-link small ms-1">Sign In</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleStaffField() {
    const role = document.getElementById('role').value;
    const container = document.getElementById('staff_id_container');
    const input = document.getElementById('staff_id');
    
    if (role === 'staff') {
        container.classList.remove('d-none');
        input.setAttribute('required', 'required');
    } else {
        container.classList.add('d-none');
        input.removeAttribute('required');
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>