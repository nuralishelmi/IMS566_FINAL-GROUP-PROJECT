<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Penawar Medical Clinic</title>
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
            /* Same consistent background as other pages */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .glass-login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand-logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--pmc-dark);
            text-decoration: none;
            letter-spacing: -1px;
        }

        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 15px;
            border: 2px solid #eee;
            transition: 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--pmc-red);
            box-shadow: none;
            background: #fff;
        }

        .btn-login {
            background: var(--pmc-red);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #a71d2a;
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
            color: white;
        }

        .role-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 50px;
            background: rgba(220, 53, 69, 0.1);
            color: var(--pmc-red);
            font-weight: 600;
            font-size: 0.8rem;
            margin-bottom: 20px;
        }

        .register-link {
            color: var(--pmc-red);
            text-decoration: none;
            font-weight: 600;
        }

        .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="glass-login-card shadow-lg">
        <div class="text-center mb-4">
            <a href="#" class="brand-logo">PENAWAR <span class="text-danger">CLINIC</span></a>
            <p class="text-muted small">Healthcare Management System</p>
        </div>
        
        <form action="login_process.php" method="POST">
            <div class="text-center">
                <span class="role-badge" id="roleIndicator">PATIENT ACCESS</span>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Login As</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-person-badge"></i></span>
                    <select name="role" id="roleSelect" class="form-select border-start-0" onchange="toggleFields()" required>
                        <option value="patient">Patient</option>
                        <option value="staff">Medical Staff</option>
                    </select>
                </div>
            </div>

            <div class="mb-3" id="identifierField">
                <label class="form-label small fw-bold text-muted">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope" id="idIcon"></i></span>
                    <input type="email" name="identifier" class="form-control border-start-0" placeholder="name@example.com" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3 shadow">Sign In</button>
            
            <div class="text-center mt-2">
                <p class="small text-muted mb-0">Don't have an account?</p>
                <a href="register.php" class="register-link">Create a Account</a>
            </div>
        </form>
        
        <div class="mt-4 pt-3 border-top text-center">
            <small class="text-muted">Emergency? Call <strong>999</strong></small>
        </div>
    </div>
</div>

<script>
function toggleFields() {
    const role = document.getElementById('roleSelect').value;
    const label = document.querySelector('#identifierField label');
    const input = document.querySelector('#identifierField input');
    const indicator = document.getElementById('roleIndicator');
    const icon = document.getElementById('idIcon');

    if (role === 'staff') {
        indicator.innerText = 'STAFF ACCESS';
        indicator.style.color = '#212529';
        indicator.style.background = '#e9ecef';
        label.innerText = 'Staff ID';
        input.type = 'text';
        input.placeholder = 'e.g. STF-123';
        icon.className = 'bi bi-person-vcard';
    } else {
        indicator.innerText = 'PATIENT ACCESS';
        indicator.style.color = '#dc3545';
        indicator.style.background = 'rgba(220, 53, 69, 0.1)';
        label.innerText = 'Email Address';
        input.type = 'email';
        input.placeholder = 'name@example.com';
        icon.className = 'bi bi-envelope';
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>