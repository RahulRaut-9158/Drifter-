# Drifter — Transport Services Platform

![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-CSS3-E34F26?style=flat&logo=html5&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=flat&logo=javascript&logoColor=black)
![XAMPP](https://img.shields.io/badge/XAMPP-Local-FB7A24?style=flat&logo=xampp&logoColor=white)

A full-stack PHP/MySQL web application for booking transport, travel, courier, and packers & movers services across India. Built with role-based authentication, live booking management, and a clean responsive UI.

---

## Screenshots

> Homepage, Customer Dashboard, Owner Dashboard, Login/Signup

| Page | Description |
|------|-------------|
| Homepage | Hero banner, live stats, services grid, how it works |
| Login / Signup | Glassmorphism card with role selector (Customer / Owner / Company) |
| Customer Dashboard | Booking stats, quick actions, tabbed booking history |
| Owner Dashboard | Vehicle stats, full bookings table with confirm/cancel, availability toggle |
| Booking Flow | 2-step: enter trip details → select vehicle → confirm |

---

## Features

- **Transport Goods** — Book verified transport vehicles by pickup city, date, and distance
- **Travel / Ride** — Book passenger vehicles for commutes and long-distance trips
- **Courier** — Find courier companies and submit delivery requests
- **Packers & Movers** — Request relocation services with property and item details
- **Vehicle Owner Dashboard** — Register vehicles, toggle availability, confirm/cancel bookings
- **Customer Dashboard** — View all bookings, cancel pending bookings, live polling updates
- **Company Dashboard** — Register courier/movers company, view incoming requests
- **Role-based Auth** — Customer, Vehicle Owner, and Company roles with CSRF protection
- **Live Stats** — Real-time vehicle, booking, and customer counts on homepage
- **Contact Form** — Support page with AJAX message submission

---

## Tech Stack

- **Backend:** PHP 8+
- **Database:** MySQL (via XAMPP)
- **Frontend:** HTML, CSS, JavaScript (no framework)
- **Icons:** Font Awesome 6
- **Fonts:** Google Fonts — Inter

---

## Design System

| Element            | Color                   | Hex       |
|--------------------|-------------------------|-----------|
| Background         | Off-White / Silver Tint | `#F8F9FA` |
| Primary            | Charcoal                | `#212529` |
| Accent 1           | Hi-Vis Orange           | `#FF6B00` |
| Accent 2           | Safety Yellow           | `#FFC107` |
| Accent 3           | Reflective Silver       | `#ADB5BD` |
| Muted Text         | Grey                    | `#6c757d` |
| Card Border        | Light Grey              | `#e9ecef` |
| Success            | Green                   | `#dcfce7` / `#166534` |
| Danger             | Red                     | `#fee2e2` / `#991b1b` |

All pages use a consistent white-card-on-light-background layout with the orange/yellow accent palette. Dark sections (hero banners, stats) use `#212529` charcoal with orange highlights.

---

## Project Structure

```
Drifter/
├── front/                      # Public-facing pages
│   ├── index.php               # Homepage with live stats
│   ├── login.php               # Login (CSRF protected)
│   ├── signup.php              # Registration (role selector)
│   ├── logout.php              # Session logout
│   ├── about.php               # About page
│   ├── support.php             # Contact & support
│   ├── dashboard_customer.php  # Customer bookings dashboard
│   ├── your_vehicle_info.php   # Owner: transport vehicles & bookings
│   ├── your_vehicle_travel.php # Owner: travel vehicles & bookings
│   ├── toggle_vehicle.php      # AJAX: toggle vehicle availability
│   ├── api_my_rides.php        # AJAX: fetch customer bookings
│   ├── api_dashboard.php       # AJAX: fetch owner dashboard data
│   ├── api_update_booking.php  # AJAX: owner confirm/cancel booking
│   ├── api_cancel_booking.php  # AJAX: customer cancel booking
│   ├── api_stats.php           # AJAX: homepage live stats
│   └── api_contact.php         # AJAX: support form submission
│
├── transport/                  # Goods transport module
│   ├── booking_step1.php       # Step 1: Enter trip details
│   ├── select_vehicle.php      # Step 2: Choose vehicle
│   ├── book_vehicle.php        # AJAX: create booking
│   ├── add_vehicle.php         # Register transport vehicle
│   └── uploads/                # Vehicle & license images
│
├── travel/                     # Passenger travel module
│   ├── booking_step1.php       # Step 1: Enter trip details
│   ├── select_vehicle.php      # Step 2: Choose vehicle
│   ├── book_vehicle.php        # AJAX: create booking
│   ├── add_vehicle.php         # Register travel vehicle
│   └── uploads/                # Vehicle & license images
│
├── courier/                    # Courier module
│   ├── courier.php             # Submit courier request
│   ├── providers.php           # List courier companies
│   ├── add_company.php         # Register courier company
│   ├── company_info.php        # Owner: view own companies & requests
│   ├── company_success.php     # Registration success page
│   ├── process_company.php     # Handle company registration
│   ├── process_request.php     # Handle courier request submission
│   ├── db_connect.php          # drifter_courier DB connection
│   └── uploads/                # Company logos
│
├── move/                       # Packers & Movers module
│   ├── movers.php              # Submit moving request
│   ├── providers.php           # List moving companies
│   ├── add_company.php         # Register movers company
│   ├── company_info.php        # Owner: view own companies & requests
│   ├── company_success.php     # Registration success page
│   ├── process_company.php     # Handle company registration
│   ├── process_request.php     # Handle moving request submission
│   ├── db_connect.php          # moveeasy DB connection
│   └── uploads/                # Company logos
│
├── includes/                   # Shared components (used by all pages)
│   ├── navbar.php              # Global navigation bar + global CSS variables
│   ├── footer.php              # Global footer
│   ├── db.php                  # All DB connections (db, courier, movers)
│   └── auth.php                # Auth helper (requireLogin)
│
├── others/                     # Static media assets
│   ├── courier.mp4
│   ├── packers and movers.mp4
│   ├── trans.webm
│   └── travelling2.mp4
│
├── vehicles/                   # Sample vehicle images
├── driving licence/            # Sample licence images
├── db_setup.sql                # Full database schema (run once)
└── README.md
```

---

## Deployment on Railway

### Step 1 — Push to GitHub
```bash
git init
git add .
git commit -m "Initial commit: Drifter Transport Platform"
git branch -M main
git remote add origin https://github.com/YOURUSERNAME/drifter-transport-platform.git
git push -u origin main
```

### Step 2 — Deploy on Railway
1. Go to [railway.app](https://railway.app) → **New Project**
2. Click **Deploy from GitHub repo** → select your repo
3. Click **Add Plugin** → **MySQL** — Railway creates a MySQL instance
4. Go to your web service → **Variables** tab → add:
   ```
   APP_URL = https://yourapp.up.railway.app
   ```
   (Railway auto-sets `MYSQLHOST`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`, `MYSQLPORT`)
5. Go to MySQL plugin → **Connect** tab → open **Query** tab
6. Paste and run `db_setup_railway.sql` to create all tables
7. Your app is live! 🚀

### Step 3 — Local Development
Local setup still works exactly as before with XAMPP.
The `config.php` auto-detects Railway vs local via environment variables.

---

## Setup (Local XAMPP)

1. Copy the project into `C:\xampp\htdocs\Drifter\`

2. Start **Apache** and **MySQL** in XAMPP Control Panel

3. Open [phpMyAdmin](http://localhost/phpmyadmin) → SQL tab → paste and run `db_setup.sql`
   - Creates 3 databases: `db`, `drifter_courier`, `moveeasy`

4. Open [http://localhost/Drifter/front/index.php](http://localhost/Drifter/front/index.php)

---

## Database Overview

| Database          | Tables                                    | Used By                  |
|-------------------|-------------------------------------------|--------------------------|
| `db`              | `signup`, `vehicles`, `booking`           | Transport, Travel, Auth  |
| `drifter_courier` | `companies`, `services`, `user_requests`  | Courier                  |
| `moveeasy`        | `companies`, `services`, `user_requests`  | Packers & Movers         |

### Key Columns Added (post-initial setup)

If you have an existing database, run these migrations:

```sql
-- db
ALTER TABLE signup ADD COLUMN role ENUM('customer','owner','company') DEFAULT 'customer' AFTER password;
ALTER TABLE signup ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER role;

-- drifter_courier
ALTER TABLE companies ADD COLUMN owner_username VARCHAR(100) DEFAULT NULL AFTER id;
ALTER TABLE user_requests ADD COLUMN company_id INT DEFAULT NULL AFTER id;
ALTER TABLE user_requests ADD COLUMN status ENUM('Pending','Assigned','Delivered','Cancelled') DEFAULT 'Pending' AFTER package_details;

-- moveeasy
ALTER TABLE companies ADD COLUMN owner_username VARCHAR(100) DEFAULT NULL AFTER id;
ALTER TABLE user_requests ADD COLUMN company_id INT DEFAULT NULL AFTER id;
ALTER TABLE user_requests ADD COLUMN customer_name VARCHAR(100) DEFAULT NULL AFTER company_id;
ALTER TABLE user_requests ADD COLUMN status ENUM('Pending','Assigned','Completed','Cancelled') DEFAULT 'Pending' AFTER additional_info;
```

---

## User Roles

| Role       | After Login Redirects To          | Can Do                                                          |
|------------|-----------------------------------|-----------------------------------------------------------------|
| `customer` | `dashboard_customer.php`          | Book transport/travel/courier/movers, cancel bookings           |
| `owner`    | `your_vehicle_info.php`           | Register vehicles, toggle availability, confirm/cancel bookings |
| `company`  | `courier/company_info.php`        | Register courier/movers company, view incoming requests         |

### Role-based Navigation

- **Customer** sees: Services menu (book transport, travel, courier, movers)
- **Owner** sees: My Vehicles menu (transport/travel vehicles + add links)
- **Company** sees: My Companies menu (courier/movers companies + add links)
- Each role's user dropdown shows only their relevant dashboard links

---

## Security

- Passwords hashed with `password_hash()` (bcrypt)
- CSRF token on login form
- Role guards on all dashboard pages (redirect if wrong role)
- All booking/toggle/cancel endpoints verify session ownership
- `owner_name` in vehicles always saved from `$_SESSION['username']` (never from form input)
- File uploads restricted to image extensions only
- All DB queries use prepared statements

---

## Known Issues Fixed

| Issue | Fix |
|-------|-----|
| `bind_param() on bool` on signup | Added missing `role` and `phone` columns to `signup` table |
| `Unknown column 'owner_username'` | Added missing columns to `drifter_courier` and `moveeasy` tables |
| Owner cannot confirm bookings | `owner_name` now always saved as session username; API checks both username and email |
| All roles saw all nav items | Navbar now shows role-specific links only |
| Inconsistent green/orange colors | Full site recolored to consistent orange/charcoal palette |
