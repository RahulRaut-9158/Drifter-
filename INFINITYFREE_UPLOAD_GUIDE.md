# Drifter — InfinityFree Hosting Guide

## BEFORE YOU START — Get These Ready

1. Go to https://infinityfree.com → Sign up (free)
2. Create a new hosting account → note your:
   - **Subdomain**: e.g. `drifter.infinityfreeapp.com`
   - **cPanel URL**: e.g. `https://cpanel.infinityfree.com`
3. In cPanel → **MySQL Databases** → create a database:
   - Note: `DB Host`, `DB User`, `DB Password`, `DB Name`
   - They look like: `sql200.infinityfree.com`, `if0_12345678`, `yourpassword`, `if0_12345678_drifter`

---

## STEP 1 — Update config.php

Open `config.php` and fill in your InfinityFree credentials:

```php
define('DB_HOST',   'sql200.infinityfree.com');  // from cPanel MySQL
define('DB_USER',   'if0_12345678');              // your MySQL username
define('DB_PASS',   'your_actual_password');       // your MySQL password
define('DB_NAME',   'if0_12345678_drifter');       // your database name
define('DB_PORT',   '3306');
define('SINGLE_DB', true);
define('BASE',      '');                           // leave empty for root hosting
define('APP_URL',   'https://drifter.infinityfreeapp.com'); // your subdomain
```

---

## STEP 2 — Import the Database

1. In cPanel → **phpMyAdmin** → click your database on the left
2. Click the **Import** tab
3. Click **Choose File** → select `db_setup_infinityfree.sql`
4. Click **Go** (Import)
5. You should see "Import has been successfully finished"

This creates all 11 tables + seeds the admin and test accounts.

**Default login credentials after import:**
| Account | Username | Password |
|---------|----------|----------|
| Admin | `drifter_admin` | `password` |
| Test Customer | `testcustomer` | `password` |
| Test Owner | `testowner` | `password` |
| Test Company | `testcompany` | `password` |

---

## STEP 3 — Upload Files via File Manager

### Option A — File Manager (Recommended for first upload)

1. In cPanel → **File Manager** → open `htdocs` folder
2. Delete any existing `index.html` or `index2.html` files
3. Click **Upload** → upload all project files

**Folder structure to upload into `htdocs/`:**
```
htdocs/
├── admin/
├── courier/
├── front/
├── includes/
├── move/
├── transport/
├── travel/
├── .htaccess
└── config.php
```

> Do NOT upload: `db_setup.sql`, `db_setup_infinityfree.sql`, `gen_hash.php`,
> `README.md`, `CLEANUP_GUIDE.md`, `.git/`, `*.md` files

### Option B — FTP (Faster for large uploads)

FTP credentials are in cPanel → **FTP Accounts**:
- Host: `ftpupload.net`
- Username: your FTP username
- Password: your FTP password
- Port: `21`
- Upload to: `/htdocs/`

Use FileZilla (free): https://filezilla-project.org

---

## STEP 4 — Set Correct File Permissions

In File Manager, right-click each `uploads/` folder → **Change Permissions** → set to `755`:
- `transport/uploads/` → 755
- `travel/uploads/` → 755
- `courier/uploads/` → 755
- `move/uploads/` → 755

---

## STEP 5 — Change Admin Password

1. Visit: `https://yourdomain.infinityfreeapp.com/admin/seed_admin.php`
   - This will show "Admin already exists" (since SQL already seeded it)
2. Go to: `https://yourdomain.infinityfreeapp.com/front/login.php`
3. Login with `drifter_admin` / `password`
4. **Immediately change your password** — go to phpMyAdmin:
   ```sql
   UPDATE signup
   SET password = '$2y$10$NEWHASHHERE'
   WHERE username = 'drifter_admin';
   ```
   Generate a new hash at: `https://yourdomain.infinityfreeapp.com/gen_hash.php`
   (after uploading it temporarily)

---

## STEP 6 — Delete Sensitive Files

After everything works, delete these files from File Manager:
- `gen_hash.php`
- `admin/seed_admin.php`
- `db_setup_infinityfree.sql`
- `db_setup.sql`

---

## STEP 7 — Test Everything

Visit your site and test each flow:

| Test | URL |
|------|-----|
| Homepage | `https://yourdomain.infinityfreeapp.com/front/index.php` |
| Signup | `https://yourdomain.infinityfreeapp.com/front/signup.php` |
| Login | `https://yourdomain.infinityfreeapp.com/front/login.php` |
| Book Transport | Login as customer → Services → Transport |
| Owner Dashboard | Login as `testowner` → My Vehicles |
| Admin Panel | Login as `drifter_admin` → `/admin/index.php` |
| Contact Form | `/front/support.php` → send a message |

---

## TROUBLESHOOTING

### Blank page / 500 error
- Check `.htaccess` — InfinityFree does NOT support `php_value` directives
- The provided `.htaccess` is already compatible

### Database connection failed
- Double-check all 4 values in `config.php` (host, user, pass, name)
- Make sure `SINGLE_DB` is `true`
- InfinityFree MySQL host is usually `sql200.infinityfree.com` or `sql300.infinityfree.com` — check your cPanel

### Images not showing
- Check `uploads/` folder permissions are `755`
- Make sure you uploaded the `uploads/` folders (even if empty)

### Session / login issues
- InfinityFree supports PHP sessions — should work out of the box
- If login redirects loop, clear browser cookies

### File upload fails
- InfinityFree free plan has a 10MB file size limit
- The `.htaccess` `php_value` lines have been removed (they cause 500 errors)

---

## INFINITYFREE LIMITS (Free Plan)

| Limit | Value |
|-------|-------|
| Disk Space | 5 GB |
| Bandwidth | Unlimited |
| MySQL Databases | Unlimited |
| PHP Version | 7.4 / 8.x |
| File Size Limit | 10 MB per file |
| Subdomains | Unlimited |
| SSL | Free (Let's Encrypt) |

---

## QUICK REFERENCE — File Locations

| Page | URL Path |
|------|----------|
| Homepage | `/front/index.php` |
| Login | `/front/login.php` |
| Signup | `/front/signup.php` |
| Customer Dashboard | `/front/dashboard_customer.php` |
| Owner Vehicles | `/front/your_vehicle_info.php` |
| Admin Panel | `/admin/index.php` |
| Book Transport | `/transport/booking_step1.php` |
| Book Travel | `/travel/booking_step1.php` |
| Send Courier | `/courier/courier.php` |
| Packers & Movers | `/move/movers.php` |
