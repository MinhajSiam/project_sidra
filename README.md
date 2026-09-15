# Sidra Event Ticketing Platform

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![Database](https://img.shields.io/badge/Database-MySQL%208%2B%20%7C%20SQLite-green.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/License-Proprietary-red.svg)]()
[![Tests](https://img.shields.io/badge/Tests-70%2F70%20Passing%20(100%25)-success.svg)]()

A modern, high-performance, enterprise-grade Event Discovery, Ticketing, Manual Payment Verification, and Gate Check-in Platform custom-tailored for Bangladesh's mobile financial services ecosystem (**bKash**, **Nagad**, and **Rocket**).

Built from first principles in **Pure PHP 8.2+ MVC**, **Tailwind CSS**, and **Vanilla JavaScript** with zero heavy framework overhead, robust cryptographic verification, and atomic database protections.

### 📚 Deployment & Handover Documentation
- **Shared Hosting / cPanel Guide**: [`DEPLOY_SHARED_HOSTING.md`](DEPLOY_SHARED_HOSTING.md)
- **Linux VPS / Cloud Server Guide**: [`DEPLOY_VPS.md`](DEPLOY_VPS.md)
- **Final Handover & Post-Deployment Checklist**: [`FINAL_HANDOVER_CHECKLIST.md`](FINAL_HANDOVER_CHECKLIST.md)
- **Production-Readiness Audit Report**: [`PRODUCTION_READINESS_REPORT.md`](PRODUCTION_READINESS_REPORT.md)

---

## Highlights & Key Capabilities

- **Mobile Financial Services (MFS) Integration**: Native support for **bKash**, **Nagad**, and **Rocket** manual payments with sender number tracking, Transaction ID format validation, duplicate submission protection, and payment proof screenshot uploads.
- **Finance Approval Queue**: Pending transactions queue with full-resolution screenshot modal inspection, one-click payment approval (instantly generating digital passes), and payment rejection (automatically restoring ticket inventory).
- **Cryptographic Digital Passes**: Each ticket is protected with a unique alphanumeric code (`SDR-TKT-XXXX-XXXX`) and an HMAC-SHA256 signature token. Includes embedded pure-PHP vector SVG QR codes generated offline without external API dependencies.
- **Gate Staff Camera QR Scanner**: High-speed live camera scanner using `html5-qrcode` with Web Audio API sound feedback (admitted chimes and duplicate error buzzers). Powered by atomic SQL updates preventing concurrent scan race conditions at busy entrance gates.
- **Role-Based Access Control (RBAC)**: Fine-grained permissions for **Super Admin**, **Event Manager**, **Finance Manager**, and **Gate Staff**.
- **Financial Analytics & Manifests**: Real-time sales summaries, payment method breakdowns, and one-click **CSV exports** for event sales, booking logs, and attendee check-in manifests.
- **Dual-Database Support**: Zero-config SQLite database for instantaneous local development and testing, plus a production-grade MySQL 8+ InnoDB schema with foreign keys, checks, and indices.

---

## System Architecture

```
d:\projects\sidra.bd/
├── app/
│   ├── Controllers/
│   │   ├── Admin/               # Management modules (Events, Bookings, Payments, Users, Reports, Settings)
│   │   ├── AuthController.php   # Customer and staff authentication
│   │   ├── BookingController.php# Reservation, checkout, and MFS payment submission
│   │   ├── CustomerDashboardController.php # Customer portal (tickets, orders, profile)
│   │   ├── EventController.php  # Public event directory, filters, and details
│   │   ├── HomeController.php   # Landing page with hero banner & featured events
│   │   └── VerificationController.php # Gate QR scanner and verification API
│   ├── Core/                    # Lightweight MVC Engine (Router, Database, Session, Csrf, Validator, Auth)
│   ├── Middleware/              # SecurityHeaders, CsrfMiddleware, AuthMiddleware, RoleMiddleware
│   ├── Models/                  # Data layer with atomic transactions (Event, TicketType, Booking, Payment, Ticket)
│   ├── Services/                # QrCodeService (vector SVG), TicketPdfService (print passes)
│   └── Views/                   # Dark glassmorphic responsive UI templates
├── config/                      # App, database, and payment configuration
├── database/                    # MySQL & SQLite schemas, seeds, and migration runner
├── public/                      # Web root (assets, uploads, index.php)
├── storage/                     # SQLite database and error logs
└── tests/                       # Automated comprehensive test suite (run_tests.php)
```

---

## Quick Start (Zero-Config Development Mode)

You can launch the platform in less than 60 seconds using the pre-seeded SQLite database:

### 1. Clone or Open the Project
```bash
cd d:\projects\sidra.bd
```

### 2. (Optional) Re-seed the Database
To reset or initialize sample events, venues, and user accounts:
```bash
php database/migrate.php --seed
```

### 3. Start the Local Server
```bash
php -S 127.0.0.1:8000 -t public public/index.php
```

### 4. Open in Your Browser
Visit **[http://127.0.0.1:8000](http://127.0.0.1:8000)**.

---

## Pre-Configured Demo Accounts

All pre-seeded demo accounts share the universal password: **`Password123!`**

| Role | Email | Password | Primary Portal Route |
|---|---|---|---|
| **Super Admin** | `admin@sidra.test` | `Password123!` | `/admin` |
| **Finance Manager** | `finance@sidra.test` | `Password123!` | `/admin/payments` |
| **Event Manager** | `manager@sidra.test` | `Password123!` | `/admin/events` |
| **Gate Staff** | `gate@sidra.test` | `Password123!` | `/gate/scan` |
| **Customer** | `customer@sidra.test` | `Password123!` | `/customer/dashboard` |

---

## Production Setup with MySQL 8+

To connect the platform to MySQL (e.g. via XAMPP, Laragon, or a Linux VPS):

1. Create a MySQL database (e.g., `sidra_db` with `utf8mb4_unicode_ci` charset).
2. Configure `.env` in the project root:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sidra_db
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```
3. Run the automated migration runner:
   ```bash
   php database/migrate.php --seed
   ```

For detailed production deployment instructions (including Nginx virtual hosts, SSL setup via Let's Encrypt, and cPanel setup), see [`DEPLOYMENT_GUIDE.md`](file:///d:/projects/sidra.bd/DEPLOYMENT_GUIDE.md).

---

## Running Automated Tests

The application includes an end-to-end integration test runner covering database integrity, authentication, inventory race conditions, manual MFS payments, duplicate TrxID blocks, payment approvals, QR verification, and gate duplicate check-in protections:

```bash
php tests/run_tests.php
```

```
=================================================================
SIDRA EVENT TICKETING PLATFORM — AUTOMATED TEST SUITE
=================================================================
1. Database & Infrastructure Tests:         12 / 12 PASS
2. Authentication & RBAC Tests:              7 /  7 PASS
3. Events & Ticket Tier Tests:               4 /  4 PASS
4. Booking Creation & Atomic Inventory:      5 /  5 PASS
5. Overselling Prevention Tests:             1 /  1 PASS
6. Manual Payment & Duplicate TrxID:         4 /  4 PASS
7. Payment Approval & Pass Generation:       7 /  7 PASS
8. Gate Check-in & Duplicate Scan Guard:     5 /  5 PASS
9. Payment Rejection & Stock Restoration:    4 /  4 PASS
10. Security Utilities & CSRF Verification:  5 /  5 PASS
=================================================================
Total: 54 | Passed: 54 | Failed: 0
ALL TESTS COMPLETED WITH 100% SUCCESS!
=================================================================
```

---

## Security Specifications

1. **Anti-Overselling**: Ticket reservations execute inside database transactions utilizing conditional updates (`WHERE remaining_quantity >= :qty`).
2. **Anti-Duplicate Scanning**: Gate admission updates ticket status atomically (`UPDATE tickets SET status = 'used' WHERE id = :id AND status = 'valid'`). A zero affected rows return indicates a concurrent scan or already-admitted pass.
3. **MFS TrxID Protection**: Uniqueness is enforced both at the application model level and in the database schema constraint (`UNIQUE(payment_method, transaction_id)`).
4. **Pass Authentication**: Digital passes are cryptographically signed using `hash_hmac('sha256', ...)`. Tampered tokens fail gate validation immediately.
5. **Safe File Uploads**: Payment proofs and event banner uploads are validated for file size, MIME type verification via `finfo_file()`, and stored with randomized alphanumeric filenames.

---

## Documentation Links

- [Beginner Setup Guide](file:///d:/projects/sidra.bd/BEGINNER_SETUP_GUIDE.md)
- [Production Deployment Guide](file:///d:/projects/sidra.bd/DEPLOYMENT_GUIDE.md)
- [Project Progress & Technical Report](file:///d:/projects/sidra.bd/PROJECT_PROGRESS.md)
# project_sidra
