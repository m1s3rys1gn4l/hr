# Employee Management System (Laravel)

This project is an admin-focused employee management system with salary tracking in Saudi Riyal (SAR).

## Features

- Admin dashboard with:
  - Active employee count
  - Left employee count
  - Total salary balance
  - Today's payout total
- Employee management:
  - Create employee with required `name`, optional `phone`, and required `daily_salary`
  - Auto-assign unique 3-digit ID (`001` to `999`), starting from the smallest available ID
  - Active and left employee lists
  - Inline action to set employee as left
  - ID is released when employee leaves and reused for new employees
- Attendance salary processing:
  - Accept ID input formats: `1,2,3` and `1+2+3`
  - `Full Time` IDs: full daily salary
  - `Half Time` IDs: half daily salary
  - `Overtime` IDs: additional `+10 SAR`
  - Attendance history stored per employee per date
  - Salary credits are added directly to employee current balance
- Payout flow:
  - Search active employee by unique ID
  - Show current balance and last payout date
  - Enter payout amount to deduct from balance
  - Payout history is saved and visible in profile
- Employee profile:
  - Attendance history
  - Current salary balance
  - Salary payout history

## Main Routes

- `/` Dashboard
- `/employees` Employee list (active + left)
- `/employees/create` Create employee
- `/employees/{employee}` Employee profile
- `/attendance` Attendance entry
- `/payouts` Salary payout page

## Quick Start

1. Install dependencies:

   ```bash
   composer install
   ```

2. Configure environment:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Use SQLite (default in this project):

   ```bash
   touch database/database.sqlite
   ```

4. Ensure PHP SQLite extension is installed (`pdo_sqlite` / `sqlite3`).

   Example (Arch Linux):

   ```bash
   sudo pacman -S php-sqlite
   ```

5. Run migrations:

   ```bash
   php artisan migrate
   ```

6. Start app:

   ```bash
   php artisan serve
   ```

## Notes

- Currency is Saudi Riyal (`SAR`).
- Overtime bonus is fixed to `10 SAR` per attendance entry.
- An employee cannot be both full-time and half-time on the same date.
# hr
# hr
