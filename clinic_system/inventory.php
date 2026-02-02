<?php
session_start();
include 'db.php';

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // For logging

// --- LOGIC: HANDLE ADDING NEW STOCK ---
if (isset($_POST['add_item'])) {
    $name = $_POST['item_name'];
    $cat = $_POST['category'];
    $qty = $_POST['stock_quantity'];
    $price = $_POST['unit_price'];

    // Original Insert
    $stmt = $pdo->prepare("INSERT INTO inventory (item_name, category, stock_quantity, unit_price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $cat, $qty, $price]);
    
    // NEW FEATURE: Log initial stock to inventory_logs
    $new_id = $pdo->lastInsertId();
    $log_stmt = $pdo->prepare("INSERT INTO inventory_logs (inventory_id, staff_id, change_amount, reason) VALUES (?, ?, ?, 'Initial Registration')");
    $log_stmt->execute([$new_id, $user_id, $qty]);

    header("Location: inventory.php?status=success_added");
    exit();
}

// --- LOGIC: HANDLE UPDATING EXISTING STOCK QUANTITY ---
if (isset($_POST['update_stock'])) {
    $id = $_POST['item_id'];
    $new_qty = $_POST['new_quantity'];
    $reason = $_POST['log_reason'] ?? 'Manual adjustment'; // NEW FEATURE: Reason from modal

    // Fetch current quantity to calculate change for the log
    $current_stmt = $pdo->prepare("SELECT stock_quantity FROM inventory WHERE id = ?");
    $current_stmt->execute([$id]);
    $old_qty = $current_stmt->fetchColumn();
    $change = $new_qty - $old_qty;

    // Original Update
    $stmt = $pdo->prepare("UPDATE inventory SET stock_quantity = ? WHERE id = ?");
    $stmt->execute([$new_qty, $id]);

    // NEW FEATURE: Log the update
    $log_stmt = $pdo->prepare("INSERT INTO inventory_logs (inventory_id, staff_id, change_amount, reason) VALUES (?, ?, ?, ?)");
    $log_stmt->execute([$id, $user_id, $change, $reason]);

    header("Location: inventory.php?status=success_updated");
    exit();
}

// --- LOGIC: FETCH STATISTICS ---
$total_items = $pdo->query("SELECT COUNT(*) FROM inventory")->fetchColumn();
$low_stock = $pdo->query("SELECT COUNT(*) FROM inventory WHERE stock_quantity < 10 AND stock_quantity > 0")->fetchColumn();
$out_of_stock = $pdo->query("SELECT COUNT(*) FROM inventory WHERE stock_quantity = 0")->fetchColumn();

// --- LOGIC: SEARCH & LISTING ---
$search = $_GET['search'] ?? '';
$query = "SELECT * FROM inventory WHERE item_name LIKE ? OR category LIKE ? ORDER BY item_name ASC";
$stmt = $pdo->prepare($query);
$stmt->execute(["%$search%", "%$search%"]);
$inventory = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management | PMC Staff</title>
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
        }

        #sidebar {
            min-width: 280px;
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
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-bottom: 4px solid #eee;
            transition: 0.3s;
        }

        .stat-card:hover { transform: translateY(-5px); border-bottom-color: var(--pmc-red); }

        .glass-table-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }

        .table thead { background: var(--pmc-dark); color: white; text-transform: uppercase; font-size: 0.75rem; }
        .progress { height: 8px; border-radius: 10px; background: #eee; }

        .modal-content { border-radius: 20px; border: none; }
        .modal-header { background: var(--pmc-dark); color: white; border-top-left-radius: 20px; border-top-right-radius: 20px; }
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
        <header class="row align-items-center mb-5">
            <div class="col-md-7">
                <h2 class="fw-bold mb-0 text-dark">Medical Inventory</h2>
                <p class="text-muted mb-0" id="currentDate"></p>
            </div>
            <div class="col-md-5">
                <div class="staff-id-card ms-auto" style="max-width: 320px;">
                    <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['name'] ?>&background=212529&color=fff" width="50" height="50" class="rounded shadow-sm">
                    <div>
                        <div class="fw-bold small text-dark"><?= $_SESSION['name'] ?></div>
                        <div class="text-danger fw-bold" style="font-size: 0.65rem;">SENIOR CLINIC STAFF</div>
                        <span class="badge bg-success p-1" style="font-size: 0.5rem;">ONLINE</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="row g-4 mb-5 text-center">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="text-muted small fw-bold">TOTAL ITEMS</div>
                    <div class="h2 fw-bold mb-0"><?= $total_items ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="border-bottom-color: var(--pmc-red);">
                    <div class="text-danger small fw-bold">LOW STOCK</div>
                    <div class="h2 fw-bold mb-0 text-danger"><?= $low_stock ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-dark text-white">
                    <div class="text-white-50 small fw-bold">OUT OF STOCK</div>
                    <div class="h2 fw-bold mb-0"><?= $out_of_stock ?></div>
                </div>
            </div>
        </div>

        <div class="glass-table-card">
            <div class="p-4 bg-white border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
                <h5 class="mb-0 fw-bold">Stock List</h5>
                <div class="d-flex gap-2">
                    <form method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control form-control-sm rounded-pill px-3" placeholder="Search item..." value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3">Filter</button>
                    </form>
                    <button class="btn btn-danger btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        <i class="bi bi-plus-lg me-1"></i> Add New Item
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-dark">
                    <thead>
                        <tr>
                            <th class="ps-4">Item Details</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Status Bar</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventory as $item): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?= htmlspecialchars($item['item_name']) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;">Price: RM <?= number_format($item['unit_price'], 2) ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= $item['category'] ?></span></td>
                            <td class="fw-bold"><?= $item['stock_quantity'] ?> Units</td>
                            <td style="width: 180px;">
                                <?php 
                                    $qty = $item['stock_quantity'];
                                    $color = ($qty <= 0) ? 'bg-dark' : (($qty < 10) ? 'bg-danger' : 'bg-success');
                                ?>
                                <div class="progress"><div class="progress-bar <?= $color ?>" style="width: <?= min(($qty/50)*100, 100) ?>%"></div></div>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#updateModal<?= $item['id'] ?>">
                                    Update Stock
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($inventory)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No inventory records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header shadow-sm">
                <h5 class="modal-title fw-bold">Register New Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Item Name</label>
                        <input type="text" name="item_name" class="form-control" required placeholder="e.g. Paracetamol">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Category</label>
                        <select name="category" class="form-select">
                            <option value="Medicine">Medicine</option>
                            <option value="Supplies">Supplies</option>
                            <option value="Antibiotics">Antibiotics</option>
                            <option value="Equipment">Equipment</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="form-control" value="0" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Price (RM)</label>
                            <input type="number" step="0.01" name="unit_price" class="form-control" value="0.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_item" class="btn btn-danger rounded-pill px-4">Add to Inventory</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach ($inventory as $item): ?>
<div class="modal fade" id="updateModal<?= $item['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-danger text-white border-0">
                <h6 class="modal-title fw-bold">Update Level & Log</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <small class="text-muted d-block mb-1">Editing Stock for:</small>
                        <h6 class="fw-bold mb-0"><?= htmlspecialchars($item['item_name']) ?></h6>
                    </div>
                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Set New Quantity</label>
                        <input type="number" name="new_quantity" class="form-control form-control-lg text-center fw-bold border-dark rounded-3" value="<?= $item['stock_quantity'] ?>" required>
                    </div>

                    <div class="mb-2">
                        <label class="small fw-bold mb-1">Reason for Update</label>
                        <select name="log_reason" class="form-select form-select-sm rounded-3">
                            <option value="Restock">Restock / Shipment</option>
                            <option value="Correction">Manual Correction</option>
                            <option value="Damaged">Damaged / Expired</option>
                            <option value="Clinic Use">Internal Clinic Use</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" name="update_stock" class="btn btn-dark w-100 rounded-pill py-2">Confirm & Log Change</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

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