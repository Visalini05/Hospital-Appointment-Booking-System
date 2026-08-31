# ABC Hospital — Appointment Booking System

Stack: HTML + CSS + JavaScript + PHP 8 + SQLite. No frameworks, no external dependencies.

## What's inside

### Patient-facing
- Home (live stats, featured doctors, testimonials), Services (departments overview), Doctors (search & filter,
  ratings), Doctor Profile (bio, fee, reviews), Book Appointment (live slot-availability picker), Confirmation
  (printable), My Appointments (login-based or guest phone lookup), Reschedule, Cancel, Leave a Review, About
  (with FAQ accordion), Contact (message form).
- **Patient accounts**: Register / Login / Logout. Logged-in patients skip the phone-lookup step on "My
  Appointments". Guests can still look up and manage bookings by phone number, exactly as before.

### Admin
- Login, Dashboard (stats + quick actions with unread/pending badges), Manage Doctors (add/edit/delete,
  bio & fee), Manage Appointments (search/update status/delete), **Messages inbox** (contact form submissions),
  **Review moderation** (approve/delete patient reviews before they go public), **Reports & Analytics**
  (appointments by department, top doctors, 14-day booking trend, cancellation rate — all rendered with plain
  CSS/SVG bar charts, no chart library), **CSV export** of all appointments.

### Database
SQLite file auto-created on first run (`database/hospital.db`) with 6 sample doctors (bios + fees) and a
default admin. New tables (`reviews`, `messages`) and new columns (`patients.password`, `doctors.bio`,
`doctors.fee`) are created automatically via lightweight migrations in `database/connect.php` — safe to run
against a fresh or existing database.

## Requirements
- PHP 8.x with the `pdo_sqlite` extension enabled (bundled by default in most PHP installs).
- No MySQL, no Composer, no Node needed.

## How to run it (2 options)

### Option A — Quick start with PHP's built-in server (recommended for testing)
1. Install PHP if you don't have it (php.net or `sudo apt install php-cli php-sqlite3` on Ubuntu).
2. Unzip the project, open a terminal inside the `hbs` folder.
3. Run:
   ```
   php -S localhost:8000
   ```
4. Open your browser at `http://localhost:8000`.

### Option B — XAMPP / WAMP / MAMP
1. Install XAMPP and start Apache.
2. Copy the unzipped `hbs` folder into `htdocs` (XAMPP) or `www` (WAMP).
3. Open `http://localhost/hbs/` in your browser.

The database file and tables are created automatically the first time any page runs — you don't need to run
any SQL manually. Make sure the `database/` folder is writable by the web server.

## Default logins
- **Admin panel** (`/admin/login.php`): username `admin`, password `admin123`
- **Patients**: create a free account at `/patient/register.php`, or continue booking as a guest — booking only
  asks for name/phone/email, and "My Appointments" looks up bookings by the phone number you booked with.

## Try it out (suggested walkthrough)
1. Go to Home → Services to see the departments, or straight to Doctors to search/filter and open a doctor's
   profile.
2. Click "Book Appointment". Fill the form — the time-slot grid greys out slots already taken for the doctor
   and date you pick (live, via `slots.php`). Submit.
3. You'll land on a printable Confirmation page with your Appointment ID.
4. Go to "My Appointments" (or create an account first to skip typing your phone number every time). You can
   Reschedule or Cancel any upcoming booking, and once the appointment date has passed, leave a Review.
5. Go to Admin → login with admin/admin123. Check the dashboard badges, approve the review you just submitted,
   read the message from Contact Us, edit a doctor's fee/bio, and open Reports & Export.

## Project structure
```
hbs/
├── css/style.css                    # single stylesheet, all pages
├── js/validation.js, slots.js, animations.js
├── database/connect.php             # PDO connection + schema + migrations + seed data
├── includes/header.php, footer.php, functions.php   # shared layout + helpers
├── admin/                           # login, dashboard, doctors, edit_doctor, add_doctor,
│                                     # appointments, messages, reviews, reports, export, auth guard
├── patient/                         # register, login, logout, dashboard, reschedule, review, cancel
├── index.php, services.php, doctors.php, doctor.php, appointment.php, book.php, slots.php,
│   confirmation.php, about.php, contact.php
```

## Notes
- Passwords are hashed with `password_hash()`; nothing is stored in plain text.
- All SQL uses prepared statements (PDO) to prevent SQL injection.
- Double-booking the same doctor at the same date/time slot is blocked server-side (checked again on reschedule).
- Reviews are moderated: they're saved as "Pending" and only appear on a doctor's profile / the homepage
  testimonials once an admin approves them.
- To reset all data, just delete `database/hospital.db` — it will be recreated (with fresh seed doctors) the
  next time a page loads.
