# Employee Management System - Setup Guide

## Prerequisites

The system requires PHP with SQLite support. Follow these steps to get started:

### Step 1: Enable SQLite Extension

On **Arch Linux**:
```bash
sudo pacman -S php-sqlite
sudo systemctl restart php-fpm  # if using FPM
```

On **Ubuntu/Debian**:
```bash
sudo apt-get install php-sqlite3
sudo systemctl restart apache2  # or your web server
```

Verify the extension is loaded:
```bash
php -m | grep -i sqlite
# Should output: pdo_sqlite or sqlite3
```

### Step 2: Navigate to Project

```bash
cd /home/m1s3ry/hr/laravel
```

### Step 3: Database Setup

Create SQLite database file:
```bash
touch database/database.sqlite
```

### Step 4: Run Migrations & Seed Data

```bash
php artisan migrate --seed
```

This will automatically:
- Create all database tables
- Seed 6 demo employees
- Add attendance records with salary credits
- Add payout history

### Step 5: Start Development Server

```bash
php artisan serve
```

The application will be available at: **http://localhost:8000**

## Demo Data Generated

### Employees (6 total, IDs 1-6)
- Ahmed Al-Zahrani (150 SAR/day)
- Fatima Al-Otaibi (120 SAR/day)
- Mohammed Al-Dossary (180 SAR/day)
- Sarah Al-Shammari (100 SAR/day)
- Ali Al-Muraikhi (160 SAR/day)
- Noor Al-Enezi (130 SAR/day)

### Attendance Records
- **Today**: All 6 employees Full Time
- **Yesterday**: First 3 employees Half Time
- **2 Days Ago**: Employees #3-4 with Full Time + Overtime (+10 SAR)

### Payouts
- Each employee received 50% payout of their balance yesterday

## Testing the System

1. **Dashboard** http://localhost:8000
   - View active/left counts
   - See total salary balance
   - Check today's payout total

2. **Employees** http://localhost:8000/employees
   - View active employee list (IDs, names, balances)
   - View left employee list
   - Click "Profile" to see attendance & payout history
   - Click "Set Left" to move employee to inactive

3. **Attendance** http://localhost:8000/attendance
   - Add attendance: Try entering IDs like: `1,2,3` or `1+2+3`
   - Test Full Time, Half Time, and Overtime
   - Watch balance updates in profiles

4. **Payouts** http://localhost:8000/payouts
   - Search an employee by ID (e.g., 001)
   - View current balance
   - Enter payout amount
   - Check balance deduction

## Common Issues

**Error: "could not find driver"**
→ SQLite extension not enabled. Re-run Step 1.

**Migration fails on unique constraint**
→ Delete `database/database.sqlite` and start over.

**Port 8000 already in use**
→ Use `php artisan serve --port=8001` instead.

## Resetting Demo Data

To reset everything and start fresh:
```bash
php artisan migrate:refresh --seed
```

This will drop and recreate all tables with fresh demo data.


CloudPanel Setup

Create a PHP Site in CloudPanel for your domain.
Set the site path to something like /home/cloudpanel/htdocs/hr.
Set Document Root to /home/cloudpanel/htdocs/hr/public.
In DNS, point your domain A record to the server IP, then enable Let’s Encrypt SSL in CloudPanel.
Deploy Repo

SSH into server, then:
cd /home/cloudpanel/htdocs
git clone https://github.com/m1s3rys1gn4l/hr.git hr
cd hr/laravel (if repo root contains laravel folder)
Install app:
composer install --no-dev --optimize-autoloader
cp [.env.example](http://_vscodecontentref_/1) .env
php artisan key:generate
Production Config

Edit .env:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
Set DB creds (CloudPanel MySQL recommended)
Then run:
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force
php artisan storage:link
php artisan optimize
chmod -R 775 storage bootstrap/cache