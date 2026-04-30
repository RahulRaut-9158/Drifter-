<?php
/**
 * Vehicle Owner Dashboard
 * Dashboard for vehicle owners to manage vehicles and bookings
 */

require_once 'includes/auth.php';
require_once 'includes/db.php';

Auth::requireRole('owner');
$user = Auth::getCurrentUser();

// Get owner statistics
$stats = ['total_vehicles' => 0, 'active_vehicles' => 0, 'total_bookings' => 0, 'pending_bookings' => 0];

try {
    $conn = DatabaseManager::getMainConnection();
    
    // Total vehicles
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM vehicles WHERE owner_name = ?");
    $stmt->bind_param("s", $user['username']);
    $stmt->execute();
    $stats['total_vehicles'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Active vehicles
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM vehicles WHERE owner_name = ? AND is_available = 1");
    $stmt->bind_param("s", $user['username']);
    $stmt->execute();
    $stats['active_vehicles'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Total bookings for owner's vehicles
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM booking b 
        JOIN vehicles v ON b.vehicle_id = v.id 
        WHERE v.owner_name = ?
    ");
    $stmt->bind_param("s", $user['username']);
    $stmt->execute();
    $stats['total_bookings'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Pending bookings
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM booking b 
        JOIN vehicles v ON b.vehicle_id = v.id 
        WHERE v.owner_name = ? AND b.status = 'Pending'
    ");
    $stmt->bind_param("s", $user['username']);
    $stmt->execute();
    $stats['pending_bookings'] = $stmt->get_result()->fetch_assoc()['count'];
    
} catch (Exception $e) {
    error_log("Owner dashboard stats error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Owner Dashboard - <?= APP_NAME ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8f9fa; }
        
        .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        
        .welcome-section {
            background: linear-gradient(135deg, #212529, #495057);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(33, 37, 41, 0.3);
        }
        
        .welcome-content h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        .welcome-content p { opacity: 0.9; font-size: 1.1rem; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        
        .stat-card:hover { transform: translateY(-2px); }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #FF6B00, #FFC107);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.5rem;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #212529;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .actions-section h2 {
            margin-bottom: 1.5rem;
            color: #212529;
            font-size: 1.5rem;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .action-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .action-card:hover {
            transform: translateY(-4px);
            border-color: #FF6B00;
            box-shadow: 0 8px 30px rgba(255, 107, 0, 0.2);
        }
        
        .action-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #FF6B00, #FFC107);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
        }
        
        .action-card h3 {
            margin-bottom: 1rem;
            color: #212529;
            font-size: 1.25rem;
        }
        
        .action-card p {
            color: #6c757d;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: #FF6B00;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background: #e55a00;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .quick-actions {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .quick-actions h3 {
            margin-bottom: 1.5rem;
            color: #212529;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .quick-action-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            text-decoration: none;
            color: #212529;
            transition: all 0.3s;
        }
        
        .quick-action-btn:hover {
            border-color: #FF6B00;
            background: #fff5f0;
            color: #FF6B00;
        }
        
        .quick-action-btn i {
            font-size: 1.25rem;
        }
        
        @media (max-width: 768px) {
            .dashboard-container { padding: 1rem; }
            .welcome-content h1 { font-size: 1.5rem; }
            .stats-grid { grid-template-columns: 1fr; }
            .actions-grid { grid-template-columns: 1fr; }
            .quick-actions-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-content">
                <h1>Welcome, <?= htmlspecialchars($user['username']) ?>!</h1>
                <p>Manage your vehicles, track bookings, and grow your transport business with Drifter.</p>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="stat-number"><?= $stats['total_vehicles'] ?></div>
                <div class="stat-label">Total Vehicles</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number"><?= $stats['active_vehicles'] ?></div>
                <div class="stat-label">Active Vehicles</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-number"><?= $stats['total_bookings'] ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?= $stats['pending_bookings'] ?></div>
                <div class="stat-label">Pending Bookings</div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
            <div class="quick-actions-grid">
                <a href="transport/add_vehicle.php" class="quick-action-btn">
                    <i class="fas fa-plus"></i>
                    <span>Add Transport Vehicle</span>
                </a>
                <a href="travel/add_vehicle.php" class="quick-action-btn">
                    <i class="fas fa-plus"></i>
                    <span>Add Travel Vehicle</span>
                </a>
                <a href="front/your_vehicle_info.php" class="quick-action-btn">
                    <i class="fas fa-list"></i>
                    <span>View All Vehicles</span>
                </a>
                <a href="front/your_vehicle_travel.php" class="quick-action-btn">
                    <i class="fas fa-car"></i>
                    <span>Travel Vehicles</span>
                </a>
            </div>
        </div>
        
        <!-- Main Actions -->
        <div class="actions-section">
            <h2><i class="fas fa-cogs"></i> Manage Your Business</h2>
            <div class="actions-grid">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Transport Vehicles</h3>
                    <p>Register and manage your goods transport vehicles. Set rates, availability, and track bookings.</p>
                    <a href="transport/add_vehicle.php" class="btn">
                        <i class="fas fa-plus"></i> Add Transport Vehicle
                    </a>
                </div>
                
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3>Travel Vehicles</h3>
                    <p>List your passenger vehicles for travel and ride services. Manage bookings and availability.</p>
                    <a href="travel/add_vehicle.php" class="btn">
                        <i class="fas fa-plus"></i> Add Travel Vehicle
                    </a>
                </div>
                
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Booking Management</h3>
                    <p>View and manage all your vehicle bookings. Confirm, cancel, or update booking status.</p>
                    <a href="front/your_vehicle_info.php" class="btn">
                        <i class="fas fa-list"></i> View Bookings
                    </a>
                </div>
                
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-toggle-on"></i>
                    </div>
                    <h3>Vehicle Availability</h3>
                    <p>Quickly toggle your vehicles' availability status to control when they can be booked.</p>
                    <a href="front/your_vehicle_info.php" class="btn btn-secondary">
                        <i class="fas fa-cog"></i> Manage Availability
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>