# Beginner-Friendly Setup Guide — Sidra Event Ticketing Platform

Welcome to the **Sidra Event Ticketing Platform**! This guide is written specifically for beginners. You do **not** need to be an experienced developer or database administrator to get this application running smoothly.

Follow these simple, numbered steps.

---

## 1. System Requirements

To run this platform, your computer needs:
1. **PHP 8.2 or higher** (with extensions: `pdo_sqlite`, `pdo_mysql`, `openssl`, `mbstring`, `fileinfo`, `gd`)
2. A web browser (Google Chrome, Firefox, Safari, or Microsoft Edge)
3. *(Optional for MySQL)*: **XAMPP**, **Laragon**, or a standalone **MySQL 8+** server

---

## 2. Fast Setup (Zero-Config Development Mode)

The platform comes with a pre-configured, zero-setup SQLite database already created in `storage/database/sidra.sqlite`. You can launch the entire platform in **less than 60 seconds**!

### Step 1: Open Your Terminal / Command Prompt
Open PowerShell, Command Prompt, or your preferred terminal and navigate to the project directory:
```bash
cd d:\projects\sidra.bd
```

### Step 2: Verify Your Database (Already Migrated & Seeded)
To reset or re-seed the sample database with demo events, venues, and user accounts at any time, run:
```bash
php database/migrate.php --seed
```
*You will see green confirmation messages stating that all tables and seed records were created successfully.*

### Step 3: Start the Web Server
Run PHP's built-in development web server:
```bash
php -S 127.0.0.1:8000 -t public public/index.php
```

### Step 4: Open in Your Browser
Open your browser and go to:
👉 **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

That's it! The homepage will load immediately.

---

## 3. Alternative: Running with XAMPP / Laragon (MySQL 8+)

If you prefer using **MySQL** via XAMPP or Laragon:

### Step 1: Start Apache & MySQL in XAMPP / Laragon
1. Open your **XAMPP Control Panel** or **Laragon**.
2. Click **Start** next to **MySQL** (and Apache if needed).

### Step 2: Create the Database
1. Open **phpMyAdmin** in your browser at `http://localhost/phpmyadmin`.
2. Click **New** and name the database: `sidra_db`.
3. Choose collation: `utf8mb4_unicode_ci` and click **Create**.

### Step 3: Update `.env` Configuration
Open `.env` in the root of `d:\projects\sidra.bd` (if it does not exist, copy `.env.example` to `.env`):
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sidra_db
DB_USERNAME=root
DB_PASSWORD=
```
*(Leave `DB_PASSWORD` blank if you are using default XAMPP settings).*

### Step 4: Run the Migration & Seed Script
In your terminal, run:
```bash
php database/migrate.php --seed
```
The script will detect MySQL and create all tables, foreign keys, constraints, and default data automatically.

---

## 4. Default Login Accounts & Roles

You can test all platform roles immediately. All accounts share the demo password: `Password123!`

| Role | Email | Password | What You Can Test |
|---|---|---|---|
| **Super Admin** | `admin@sidra.test` | `Password123!` | Complete administrative power, audit logs, staff management, settings. |
| **Finance Manager** | `finance@sidra.test` | `Password123!` | Review manual bKash/Nagad/Rocket payments, approve payments, inspect proof screenshots, view sales reports. |
| **Event Manager** | `manager@sidra.test` | `Password123!` | Create and edit events, ticket tiers, venues, and categories. |
| **Gate Staff** | `gate@sidra.test` | `Password123!` | Camera QR code scanner for entrance gates and manual ticket code lookup. |
| **Customer** | `customer@sidra.test` | `Password123!` | Public ticket checkout, viewing digital ticket passes, and order history. |

---

## 5. Walkthrough: Complete End-to-End User Experience

Follow this quick guide to experience the full ticketing lifecycle:

### Step A: Browse & Select Tickets (Customer)
1. Go to `http://127.0.0.1:8000/events`.
2. Click on **Sidra Tech & AI Summit 2026**.
3. Select 1 or 2 tickets under **Standard Pass** or **VIP Delegate**.
4. Click **Proceed to Checkout**.
5. Fill in attendee details and click **Confirm Reservation & Proceed to Payment**.

### Step B: Submit Manual Mobile Payment (bKash / Nagad / Rocket)
1. You will be redirected to the payment instruction page.
2. Select your payment provider (e.g., **bKash**).
3. The screen will display the official merchant number (`01711-223344`) and step-by-step dialing instructions.
4. Enter a dummy Sender Number (e.g., `01711223344`) and Transaction ID (e.g., `TRX987654321`).
5. *(Optional)* Attach any picture as a proof screenshot.
6. Click **Submit Payment Verification**.
7. The order status will change to **Pending Review**.

### Step C: Approve Payment (Finance Manager / Admin)
1. In another tab (or Incognito), go to `http://127.0.0.1:8000/admin/login`.
2. Sign in with:
   - Email: `finance@sidra.test`
   - Password: `Password123!`
3. Click **Manual Payments** on the sidebar.
4. You will see the pending payment with the Transaction ID you submitted!
5. Click **Approve**.
6. The system will confirm the booking and immediately generate cryptographic digital passes with QR codes.

### Step D: View the Digital Pass (Customer)
1. Return to the customer window and refresh or go to `http://127.0.0.1:8000/customer/tickets`.
2. Click **View Digital Pass**.
3. You will see a high-resolution, branded gate pass featuring:
   - Attendee Name & Tier
   - Event Date & Venue
   - Vector SVG QR Code
   - High-contrast print button (`Ctrl + P` formatted for physical passes)

### Step E: Gate Entry Check-in (Gate Staff)
1. Open `http://127.0.0.1:8000/admin/login` and log in as:
   - Email: `gate@sidra.test`
   - Password: `Password123!`
2. Navigate to `http://127.0.0.1:8000/gate/scan`.
3. Allow camera access if testing on a laptop/phone, or use the **Manual Ticket Code Verification** box at the bottom.
4. Paste the ticket code (e.g., `SDR-TKT-XXXX-XXXX`) and click **Verify Ticket**.
5. **First Scan:** You will see a glowing **Green Valid Pass** badge and hear the admission chime.
6. **Second Scan:** Scan or submit the exact same code again. The platform instantly displays a **Red Duplicate Warning** with the exact timestamp of the first entry!

---

## 6. Running Automated Tests

To ensure your environment is functioning with zero errors:
```bash
php tests/run_tests.php
```
You should see all 54 assertions pass with:
```
ALL TESTS COMPLETED WITH 100% SUCCESS!
```

---

## 7. Troubleshooting & FAQ

#### Q: How do I change the bKash, Nagad, or Rocket receiving numbers?
Log into `/admin` as `admin@sidra.test`, click **System Settings**, update the numbers and instructions, and click **Save Settings**. The checkout screens will update immediately.

#### Q: Where are uploaded screenshots stored?
Uploaded screenshots and event banners are stored in `public/uploads/`.

#### Q: What if I get an "Undefined function mb_substr or finfo" error?
Open your `php.ini` file and make sure the following lines are uncommented (no leading semicolon `;`):
```ini
extension=mbstring
extension=fileinfo
extension=openssl
extension=pdo_sqlite
extension=pdo_mysql
extension=gd
```
Save the file and restart your PHP server.
