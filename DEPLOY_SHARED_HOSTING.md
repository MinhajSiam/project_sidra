# Sidra Event Ticketing Platform — Shared Hosting (cPanel) Deployment Guide

> **Audience**: Beginners, non-technical administrators, and web developers deploying to standard shared hosting environments (e.g., Namecheap, Bluehost, Hostinger, cPanel, Dianahost, ExonHost, ItNutHosting, etc.).  
> **Target Environment**: cPanel with Apache, PHP 8.2+, MySQL 8.0 / MariaDB 10.4+.  
> **Estimated Deployment Time**: 15–20 minutes.

---

## Overview of How the Application Works on Shared Hosting

The Sidra platform uses an enterprise-grade MVC architecture where:
- The **`public/`** folder contains the public web assets (`index.php`, CSS, JS, images, uploads).
- The parent folders (`app/`, `database/`, `storage/`, `vendor/`, and `.env`) contain sensitive backend code, templates, logs, and database credentials.

On cPanel shared hosting, you have **two simple options** to deploy:
- **Recommended Option A (Separate Root)**: Put the application files in your home folder and link/point `public_html` to `public`.
- **Beginner Option B (Single Folder)**: Upload the entire project into `public_html`. The pre-configured root [`.htaccess`](file:///d:/projects/sidra.bd/.htaccess) automatically redirects visitors to `/public` while denying access to `.env`, `storage/`, and backend code.

---

## Step 1: Prepare Your Project Archive on Your Computer

1. Open your project folder on your computer (`d:\projects\sidra.bd`).
2. Make sure the `vendor/` folder is present (if not, run `composer install --no-dev --optimize-autoloader` locally).
3. Select all files and folders inside the project folder:
   - `app/`
   - `console/`
   - `database/`
   - `public/`
   - `storage/`
   - `vendor/`
   - `.htaccess`
   - `.env.example`
   - `composer.json`
4. Right-click and choose **Compress to ZIP file** (name it `sidra-deploy.zip`).

---

## Step 2: Upload Files to cPanel File Manager

1. Log in to your **cPanel Dashboard** (usually `https://yourdomain.com:2083`).
2. Scroll to the **Files** section and click on **File Manager**.
3. In the top right corner of File Manager, click **Settings** &rarr; check **Show Hidden Files (dotfiles)** &rarr; click **Save**.
4. Navigate to your target directory:
   - If deploying as your main site: Open the **`public_html`** directory.
   - If deploying on a subdomain (e.g. `tickets.yourdomain.com`): Open the subdomain's folder.
5. In the top toolbar, click **Upload**.
6. Drag and drop your `sidra-deploy.zip` file. Wait until the progress bar reaches 100% and turns green.
7. Return to the File Manager, right-click `sidra-deploy.zip`, and click **Extract** &rarr; **Extract Files**.
8. After extraction finishes, you can safely delete the `.zip` file to save disk space.

---

## Step 3: Create MySQL Database & User in cPanel

1. In the cPanel Dashboard, scroll to the **Databases** section and click **MySQL® Database Wizard**.
2. **Step 1: Create a Database**:
   - Enter a name, e.g., `ticketing`.
   - The full database name will look like: `yourcpaneluser_ticketing`.
   - Click **Next Step**.
3. **Step 2: Create Database Users**:
   - Username: e.g., `dbuser` (Full username: `yourcpaneluser_dbuser`).
   - Password: Click **Password Generator** to create a strong password (e.g. `K9#m$L2!pQx8vW`).
   - Copy this password into a Notepad note.
   - Click **Create User**.
4. **Step 3: Add User to Database**:
   - Check the box for **ALL PRIVILEGES**.
   - Click **Make Changes** / **Next Step**.
   - You should see a green success message: *"User was added to the database."*

---

## Step 4: Import Database Schema & Initial Data

1. Return to the cPanel home page, scroll to **Databases**, and click **phpMyAdmin**.
2. In the left-hand column of phpMyAdmin, click on your newly created database (`yourcpaneluser_ticketing`).
3. In the top tab bar, click **Import**.
4. Under **File to import**, click **Choose File**:
   - Browse to your local computer: `database/schema.sql`.
   - Click the blue **Import** (or **Go**) button at the bottom of the screen.
   - *Successful Output*: A green bar appears stating *"Import has been successfully finished, X queries executed."*
5. Now import default roles, settings, and sample events:
   - Click the **Import** tab again.
   - Click **Choose File** &rarr; select `database/seeds.sql`.
   - Click **Import** (or **Go**).
   - *Successful Output*: Green confirmation message with tables populated.

---

## Step 5: Configure the Production `.env` File

1. Return to cPanel **File Manager** in your project folder.
2. Locate the file named `.env.example`.
3. Right-click `.env.example` &rarr; click **Rename** &rarr; change the name to `.env`.
4. Right-click the `.env` file &rarr; click **Edit** (confirm character encoding UTF-8).
5. Update the following configuration lines:

```ini
APP_NAME="Sidra Event Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=yourcpaneluser_ticketing
DB_USERNAME=yourcpaneluser_dbuser
DB_PASSWORD=YourPasswordGeneratedInStep3

SESSION_SECURE_COOKIE=true
```

6. Click **Save Changes** in the top-right corner, then click **Close**.

---

## Step 6: Set Directory Permissions

cPanel requires specific Linux permissions so the web server can write images, session logs, and cache without compromising security:

1. In cPanel File Manager:
   - Find folder **`storage`** &rarr; right-click &rarr; click **Change Permissions**.
   - Set to **`775`** (or `755`). If your hosting provider requires group write, select `775`. Check **User: Read/Write/Execute**, **Group: Read/Write/Execute**, **World: Read/Execute**.
   - Open **`public`** &rarr; find folder **`uploads`** &rarr; right-click &rarr; click **Change Permissions** &rarr; set to **`775`**.
2. Ensure standard files are set to **`644`** and directories to **`755`** (cPanel sets this by default during zip extraction).

---

## Step 7: Activate Free SSL Certificate (HTTPS)

Digital ticket QR scanners strictly require HTTPS to access mobile phone cameras:

1. In cPanel Dashboard, scroll to **Security** &rarr; click **SSL/TLS Status**.
2. Check the box next to your domain name.
3. Click the blue **Run AutoSSL** button.
4. Wait 1–2 minutes. When completed, a green padlock icon will appear next to your domain, confirming an active Let's Encrypt / cPanel SSL certificate.
5. To force all visitors to use HTTPS:
   - In cPanel, search for **Domains**.
   - Locate your domain and toggle **Force HTTPS Redirect** to **ON**.

---

## Step 8: Set Up Automated Background Cron Job

Sidra automatically cancels expired unpaid ticket reservations after 30 minutes to restore seats back to inventory.

1. In cPanel Dashboard, scroll to **Advanced** &rarr; click **Cron Jobs**.
2. Under **Add New Cron Job**:
   - Common Settings: Select **Once Every 10 Minutes (*/10 * * * *)**.
   - In the **Command** field, enter the following command (replace `yourcpaneluser` with your actual cPanel username):

```bash
/usr/local/bin/php /home/yourcpaneluser/public_html/console/cron.php >> /dev/null 2>&1
```

*(Note: If your PHP binary path differs, your host may use `/usr/bin/php` or `/usr/bin/ea-php82`)*.
3. Click **Add New Cron Job**.
4. *Successful Output*: A new entry appears under *Current Cron Jobs* indicating execution every 10 minutes.

---

## Step 9: Configure Payment Receiving Numbers (bKash, Nagad, Rocket)

1. Open your browser and navigate to: `https://yourdomain.com/admin/login`.
2. Log in using the default Super Admin credentials:
   - **Email**: `admin@sidra.test`
   - **Password**: `Password123!`
3. In the left-hand sidebar, click **Platform Settings** (gear icon).
4. Under **Manual Payment Receiving Accounts (MFS)**:
   - Enter your real bKash Merchant or Personal number.
   - Enter your Nagad and Rocket numbers.
   - Enter instructions for attendees (e.g. *"Use Send Money to 017XXXXXXXX, enter your Booking Ref in Reference"*).
5. Click **Save Platform Configuration**.
6. In the left sidebar, click **Staff Management** &rarr; edit the Super Admin account to change your email and set a new, secure password!

---

## Step 10: How to Verify the Application Is Working

| Check Item | Action | Expected Successful Output |
|---|---|---|
| **Public Homepage** | Visit `https://yourdomain.com` | Homepage loads with event search bar, categories, and published summits. No PHP warnings or styling errors. |
| **HTTPS Security** | Check address bar | Padlock icon is locked. URL begins with `https://`. |
| **Security Shield** | Try visiting `https://yourdomain.com/.env` | Returns **403 Forbidden** or **404 Not Found**. Credentials are NEVER displayed in the browser. |
| **Test Checkout** | Open an event, pick 1 ticket, enter attendee info, click Book | Redirects to `/booking/SDR-BK-.../payment` displaying your configured bKash number. |
| **Payment Submission**| Enter test TrxID `TESTBKASH99`, upload a screenshot, submit | Redirects to confirmation screen: *"Payment Submitted — Pending Admin Verification"*. |
| **Admin Approval** | Log into `/admin/payments`, locate payment, click Approve | Status changes to Approved; booking marks Confirmed; digital pass generates immediately. |
| **Digital Pass** | Click **View Pass** on booking or go to `/tickets/SDR-TKT-...` | Digital ticket displays with live vector QR code and "Print Pass" button. |
| **Gate Check-in** | Log in as gate staff at `/admin/login` (`gate@sidra.test` / `Password123!`) | Automatically redirects to `/gate/scan`. Enter ticket code &rarr; shows green **Admitted / Valid Pass** screen. |

---

## Troubleshooting Common Shared Hosting Issues

- **Error 500 Internal Server Error**:
  - Open cPanel &rarr; **File Manager** &rarr; open `storage/logs/app.log`. Read the bottom lines to see the exact error.
  - Check `storage/` directory permissions (ensure it is `755` or `775`, not `777` as some hosts block 777).
  - Verify your `.env` database username and password.
- **Images Not Showing**:
  - Ensure `public/uploads` exists and has `775` permissions.
- **AutoSSL Failing**:
  - Ensure your domain's DNS A-record points directly to your cPanel server's IP address.
