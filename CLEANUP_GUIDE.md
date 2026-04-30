# Professional File Structure - Cleanup Guide

## Files to DELETE from htdocs (Redundant/Unused)

### 1. Delete These Files:
```
- index_alternative.php (redundant)
- r.html (unused HTML file)
- INFINITYFREE_UPLOAD_GUIDE.md (documentation only)
- README.md (not needed on production)
```

### 2. Delete These Folders:
```
- Drifter/ (entire folder - files moved to root)
- .git/ (version control - not needed on production)
- .vscode/ (development environment - not needed)
```

## Professional File Structure (Final)

```
htdocs/
├── index.php                    ← Homepage
├── login.php                    ← Login page
├── signup.php                   ← Registration
├── logout.php                   ← Logout handler
├── about.php                    ← About page
├── support.php                  ← Support/Contact
├── dashboard_customer.php       ← Customer dashboard
├── dashboard_owner.php          ← Owner dashboard
├── config.php                   ← Configuration
├── .htaccess                    ← URL rewriting
├── db_setup_infinityfree.sql    ← Database schema
│
├── includes/                    ← Shared components
│   ├── auth.php                 ← Authentication system
│   ├── db.php                   ← Database connections
│   ├── navbar.php               ← Navigation component
│   └── footer.php               ← Footer component
│
├── transport/                   ← Transport goods module
│   ├── uploads/                 ← Vehicle images
│   ├── booking_step1.php        ← Booking form
│   ├── select_vehicle.php       ← Vehicle selection
│   ├── book_vehicle.php         ← Booking handler
│   └── add_vehicle.php          ← Add vehicle form
│
├── travel/                      ← Travel/ride module
│   ├── uploads/                 ← Vehicle images
│   ├── booking_step1.php        ← Booking form
│   ├── select_vehicle.php       ← Vehicle selection
│   ├── book_vehicle.php         ← Booking handler
│   └── add_vehicle.php          ← Add vehicle form
│
├── courier/                     ← Courier services module
│   ├── uploads/                 ← Company logos
│   ├── courier.php              ← Send package form
│   ├── providers.php            ← List companies
│   ├── add_company.php          ← Register company
│   ├── company_info.php         ← Company dashboard
│   ├── company_success.php      ← Success page
│   ├── process_company.php      ← Company registration handler
│   ├── process_request.php      ← Request handler
│   └── db_connect.php           ← Legacy DB connection
│
├── move/                        ← Packers & movers module
│   ├── uploads/                 ← Company logos
│   ├── movers.php               ← Moving request form
│   ├── providers.php            ← List companies
│   ├── add_company.php          ← Register company
│   ├── company_info.php         ← Company dashboard
│   ├── company_success.php      ← Success page
│   ├── process_company.php      ← Company registration handler
│   ├── process_request.php      ← Request handler
│   └── db_connect.php           ← Legacy DB connection
│
├── front/                       ← Legacy front-end (keep for compatibility)
│   ├── api_*.php                ← AJAX endpoints
│   ├── dashboard_customer.php   ← Legacy customer dashboard
│   ├── your_vehicle_*.php       ← Vehicle management pages
│   └── toggle_vehicle.php       ← Vehicle availability toggle
│
├── uploads/                     ← Global uploads directory
│   ├── vehicles/                ← Vehicle images
│   ├── licenses/                ← License images
│   └── companies/               ← Company logos
│
└── assets/                      ← Static assets (create if needed)
    ├── css/                     ← Custom stylesheets
    ├── js/                      ← Custom JavaScript
    └── images/                  ← Static images
```

## Key Improvements Made

### 1. **Unified Configuration**
- Single `config.php` with environment detection
- Professional database connection management
- Centralized settings and constants

### 2. **Enhanced Security**
- Improved authentication system with session management
- CSRF token generation and validation
- Role-based access control
- Secure password handling

### 3. **Professional UI Components**
- Responsive navigation with role-based menus
- Professional footer with company information
- Consistent styling across all pages
- Mobile-friendly design

### 4. **Better Database Management**
- Centralized database connection class
- Error handling and logging
- Support for multiple database connections
- Backward compatibility with existing code

### 5. **Organized File Structure**
- Clear separation of concerns
- Logical folder organization
- Consistent naming conventions
- Removed redundant files

## Update Required in Existing Files

### 1. Update all PHP files to use new includes:
```php
// Old
require_once '../includes/db.php';

// New
require_once 'includes/db.php';
require_once 'includes/auth.php';
```

### 2. Update database connections:
```php
// Old
$conn = db();

// New
$conn = DatabaseManager::getMainConnection();
```

### 3. Update authentication:
```php
// Old
requireLogin();

// New
Auth::requireLogin();
Auth::requireRole('customer'); // for role-specific pages
```

## Configuration Steps

### 1. Update config.php with your InfinityFree credentials:
```php
define('DB_HOST', 'your-actual-hostname');
define('DB_USER', 'your-actual-username');
define('DB_PASS', 'your-actual-password');
define('DB_NAME', 'your-actual-database-name');
define('BASE_URL', 'https://your-subdomain.infinityfreeapp.com');
```

### 2. Import database schema:
- Use `db_setup_infinityfree.sql` in phpMyAdmin
- All tables will be created in single database

### 3. Test the application:
- Homepage should load without errors
- Login/signup should work
- Dashboard redirects should work based on user role
- All navigation links should work

This professional structure provides better maintainability, security, and user experience while keeping all existing functionality intact.