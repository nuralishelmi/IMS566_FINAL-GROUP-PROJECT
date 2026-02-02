<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penawar Medical Clinic | Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root { 
            --pmc-red: #dc3545; 
            --pmc-dark: #212529;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.4);
        }

        /* Enforcing Poppins for all elements */
        * {
            font-family: 'Poppins', sans-serif;
        }

        body { 
            background: linear-gradient(rgba(253, 245, 230, 0.8), rgba(253, 245, 230, 0.8)), 
                        url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-attachment: fixed;
            color: var(--pmc-dark);
            scroll-behavior: smooth;
        }

        /* --- NAVIGATION --- */
        .navbar {
            background: #212529 !important;
            border-bottom: 3px solid var(--pmc-red);
            padding: 12px 0;
        }

        /* --- HERO SECTION --- */
        .hero-section {
            padding: 120px 0 60px;
            text-align: center;
        }

        /* --- GLASS CARDS --- */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            padding: 2.5rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .glass-card:hover {
            transform: translateY(-10px);
            background: white;
            box-shadow: 0 20px 40px rgba(220, 53, 69, 0.15);
        }

        /* --- FEATURE SPECIFIC --- */
        .icon-box {
            width: 60px;
            height: 60px;
            background: var(--pmc-red);
            color: white;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--pmc-red);
        }

        /* --- EMERGENCY STRIP --- */
        .emergency-strip {
            background: var(--pmc-red);
            color: white;
            padding: 10px 0;
            font-weight: 600;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
        }

        /* --- BUTTONS --- */
        .btn-pmc {
            background: var(--pmc-red);
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 700;
            border: none;
            box-shadow: 0 10px 20px rgba(220, 53, 69, 0.2);
            transition: 0.3s;
        }

        .btn-pmc:hover { background: #212529; color: white; transform: translateY(-3px); }

        .section-title { font-weight: 800; margin-bottom: 3rem; position: relative; }
        .section-title::after {
            content: ''; width: 60px; height: 5px; background: var(--pmc-red);
            position: absolute; bottom: -15px; left: 50%; transform: translateX(-50%);
        }

        /* Vision & Mission Sub-styling */
        .vm-card {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            padding: 20px;
            border-left: 4px solid var(--pmc-red);
        }
    </style>
</head>
<body>

<div class="emergency-strip">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> 24/7 Emergency Line: +60 3-1234 5678
</div>

<nav class="navbar navbar-expand-lg sticky-top shadow">
    <div class="container">
        <a class="navbar-brand fw-bold text-white" href="#">
            <i class="bi bi-hospital-fill text-danger me-2"></i>PENAWAR <span class="text-danger">CLINIC</span>
        </a>
        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link px-3 text-white fw-500" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link px-3 text-white fw-500" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link px-3 text-white fw-500" href="#hours">Hours</a></li>
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-danger btn-sm px-4 rounded-pill fw-bold" href="login.php">LOGIN/SIGN UP</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<header class="hero-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <span class="badge bg-danger px-3 py-2 rounded-pill mb-3 fw-600">TRUSTED SINCE 2020</span>
                <h1 class="display-2 fw-800 mb-4">We Care for Your <br><span class="text-danger">Whole Family.</span></h1>
                <p class="lead mb-5 px-lg-5 text-muted fw-500">Penawar Medical Clinic provides integrated healthcare services, combining the latest medical technology with expert professionals to ensure your recovery.</p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="login.php" class="btn btn-pmc"><i class="bi bi-calendar-check me-2"></i>Book Your Appointment Now</a>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container py-5">
    
    <div class="row g-4 mb-5 text-center">
        <div class="col-md-3">
            <div class="glass-card">
                <div class="stat-number">25k+</div>
                <div class="fw-bold text-muted small">Satisfied Patients</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card">
                <div class="stat-number">15+</div>
                <div class="fw-bold text-muted small">Specialist Doctors</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card">
                <div class="stat-number">100%</div>
                <div class="fw-bold text-muted small">Quality Assurance</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card">
                <div class="stat-number">24/7</div>
                <div class="fw-bold text-muted small">Emergency Support</div>
            </div>
        </div>
    </div>

    <section id="about" class="my-5 py-5">
        <h2 class="section-title text-center">About Our Clinic</h2>
        <div class="glass-card mb-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-12 mb-4">
                    <p class="text-muted text-center lead fw-400">Penawar Medical Clinic is a premier healthcare provider dedicated to delivering excellence in medical services. We combine a patient-first philosophy with modern diagnostic capabilities to ensure the best health outcomes for our community.</p>
                </div>
                <div class="col-md-6">
                    <div class="vm-card">
                        <h4 class="fw-bold text-danger mb-3"><i class="bi bi-eye-fill me-2"></i>Our Vision</h4>
                        <p class="text-muted mb-0 small">To be the most trusted healthcare partner in the region, recognized for our commitment to medical innovation and compassionate patient care.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="vm-card">
                        <h4 class="fw-bold text-danger mb-3"><i class="bi bi-target me-2"></i>Our Mission</h4>
                        <p class="text-muted mb-0 small">To provide high-quality, accessible, and ethical medical treatment through a dedicated team of professionals focused on long-term community wellness.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="my-5 py-5">
        <h2 class="section-title text-center">Our Medical Services</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="glass-card h-100">
                    <div class="icon-box"><i class="bi bi-heart-pulse"></i></div>
                    <h4 class="fw-bold">General Checkup</h4>
                    <p class="text-muted small">Routine screenings and comprehensive physical exams to keep you healthy.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card h-100">
                    <div class="icon-box"><i class="bi bi-lungs"></i></div>
                    <h4 class="fw-bold">Internal Medicine</h4>
                    <p class="text-muted small">Specialized care for chronic conditions and complex physiological health.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card h-100">
                    <div class="icon-box"><i class="bi bi-virus"></i></div>
                    <h4 class="fw-bold">Vaccinations</h4>
                    <p class="text-muted small">Stay protected against seasonal illnesses and travel-related viruses.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="hours" class="mt-5 pt-5">
        <div class="glass-card overflow-hidden p-0">
            <div class="row g-0">
                <div class="col-lg-6 bg-dark text-white p-5">
                    <h3 class="fw-bold mb-4">Opening Hours</h3>
                    <div class="d-flex justify-content-between border-bottom border-secondary py-3">
                        <span>Monday - Friday</span>
                        <span class="text-danger fw-bold">8:00 AM - 10:00 PM</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom border-secondary py-3">
                        <span>Saturday - Sunday</span>
                        <span class="text-danger fw-bold">9:00 AM - 8:00 PM</span>
                    </div>
                    <div class="d-flex justify-content-between py-3">
                        <span>Public Holidays</span>
                        <span class="text-muted">Closed</span>
                    </div>
                </div>
                <div class="col-lg-6 p-5">
                    <h3 class="fw-bold mb-4">Direct Contact</h3>
                    <div class="mb-4">
                        <label class="text-danger fw-bold small">ADDRESS</label>
                        <p class="fw-medium">123 Health Street, Medical Plaza,<br>Kuala Lumpur, Malaysia</p>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="text-danger fw-bold small">PHONE</label>
                            <p class="fw-medium">+60 3-1234 5678</p>
                        </div>
                        <div class="col-6">
                            <label class="text-danger fw-bold small">EMAIL</label>
                            <p class="fw-medium">info@penawar.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<footer class="bg-dark text-white py-5 mt-5">
    <div class="container text-center">
        <div class="mb-4">
            <i class="bi bi-facebook fs-4 me-3"></i>
            <i class="bi bi-instagram fs-4 me-3"></i>
            <i class="bi bi-twitter-x fs-4"></i>
        </div>
        <p class="small opacity-50 mb-0">&copy; 2024 Penawar Medical Clinic. All rights reserved.</p>
        <p class="small opacity-25">Professional Healthcare Solutions</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>