<?php
/**
 * Customer Dashboard
 * Main dashboard for customers to book services and manage bookings
 */

require_once 'includes/auth.php';
require_once 'includes/db.php';

Auth::requireRole('customer');
$user = Auth::getCurrentUser();

// Get customer statistics
$stats = ['total_bookings' => 0, 'pending_bookings' => 0, 'completed_bookings' => 0];

try {
    $conn = DatabaseManager::getMainConnection();
    
    // Total bookings
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM booking WHERE user_name = ?");
    $stmt->bind_param("s", $user['username']);
    $stmt->execute();
    $stats['total_bookings'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Pending bookings
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM booking WHERE user_name = ? AND status = 'Pending'");
    $stmt->bind_param("s", $user['username']);
    $stmt->execute();
    $stats['pending_bookings'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Completed bookings
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM booking WHERE user_name = ? AND status = 'Confirmed'");
    $stmt->bind_param("s", $user['username']);
    $stmt->execute();
    $stats['completed_bookings'] = $stmt->get_result()->fetch_assoc()['count'];
    
} catch (Exception $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - <?= APP_NAME ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8f9fa; }
        
        .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        
        .welcome-section {
            background: linear-gradient(135deg, #FF6B00, #FFC107);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(255, 107, 0, 0.3);
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
        
        .services-section h2 {
            margin-bottom: 1.5rem;
            color: #212529;
            font-size: 1.5rem;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .service-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .service-card:hover {
            transform: translateY(-4px);
            border-color: #FF6B00;
            box-shadow: 0 8px 30px rgba(255, 107, 0, 0.2);
        }
        
        .service-icon {
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
        
        .service-card h3 {
            margin-bottom: 1rem;
            color: #212529;
            font-size: 1.25rem;
        }
        
        .service-card p {
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
        
        .recent-bookings {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .recent-bookings h3 {
            margin-bottom: 1.5rem;
            color: #212529;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .no-bookings {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }
        
        .no-bookings i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .dashboard-container { padding: 1rem; }
            .welcome-content h1 { font-size: 1.5rem; }
            .stats-grid { grid-template-columns: 1fr; }
            .services-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-content">
                <h1>Welcome back, <?= htmlspecialchars($user['username']) ?>!</h1>
                <p>Ready to book your next transport service? Choose from our wide range of verified partners.</p>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="stats-grid">
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
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number"><?= $stats['completed_bookings'] ?></div>
                <div class="stat-label">Completed Bookings</div>
            </div>
        </div>
        
        <!-- Services -->
        <div class="services-section">
            <h2><i class="fas fa-concierge-bell"></i> Book a Service</h2>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Transport Goods</h3>
                    <p>Book verified transport vehicles for safe and reliable goods delivery across cities.</p>
                    <a href="transport/booking_step1.php" class="btn">
                        <i class="fas fa-arrow-right"></i> Book Transport
                    </a>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3>Travel & Ride</h3>
                    <p>Book comfortable passenger vehicles for your commutes and long-distance trips.</p>
                    <a href="travel/booking_step1.php" class="btn">
                        <i class="fas fa-arrow-right"></i> Book Travel
                    </a>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3>Courier Services</h3>
                    <p>Send packages and documents through our network of reliable courier partners.</p>
                    <a href="courier/courier.php" class="btn">
                        <i class="fas fa-arrow-right"></i> Send Package
                    </a>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3>Packers & Movers</h3>
                    <p>Professional relocation services for your home and office moving needs.</p>
                    <a href="move/movers.php" class="btn">
                        <i class="fas fa-arrow-right"></i> Request Quote
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Bookings -->
        <div class="recent-bookings">
            <h3><i class="fas fa-history"></i> Recent Bookings</h3>
            <div class="no-bookings">
                <i class="fas fa-clipboard-list"></i>
                <p>No recent bookings found.</p>
                <p>Start by booking your first service above!</p>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>