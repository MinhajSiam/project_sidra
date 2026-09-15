# Sidra Event Ticketing Platform — Linux VPS & Cloud Deployment Guide

> **Audience**: DevOps engineers, system administrators, and developers deploying to cloud virtual private servers (e.g., DigitalOcean, AWS EC2, Linode, Hetzner, Vultr, Google Cloud Compute).  
> **Target Environment**: Ubuntu 22.04 / 24.04 LTS, Nginx 1.24+, PHP 8.2 or 8.3 FPM, MySQL 8.0+ / MariaDB 10.11+, Let's Encrypt SSL.  
> **Estimated Deployment Time**: 15–25 minutes.

---

## Architecture Overview on a Virtual Private Server

In a dedicated Linux VPS environment, the Sidra application runs with optimal security and performance isolation:
- **Web Server**: Nginx acts as the high-concurrency reverse proxy and static asset server.
- **Application Runtime**: PHP-FPM executes backend PHP processes in non-privileged worker pools (`www-data`).
- **Database Server**: MySQL 8.0+ with InnoDB storage engine manages transactions and concurrency locks.
- **Document Root**: Nginx directs traffic strictly to `/var/www/sidra/public`. The backend application files, `.env`, logs, and database files remain completely outside the public web root.

---

## Step 1: Connect to Your VPS and Update Packages

Connect to your server via SSH from your local terminal or PowerShell:

```bash
ssh root@YOUR_SERVER_IP
```

Update package repositories and upgrade existing system packages:

```bash
apt update && apt upgrade -y
```

*Expected Successful Output*:
```text
Reading package lists... Done
Building dependency tree... Done
0 upgraded, 0 newly installed, 0 to remove.
```

---

## Step 2: Configure UFW Firewall

Secure the server by allowing only SSH, HTTP, and HTTPS traffic:

```bash
ufw default deny incoming
ufw default allow outgoing
ufw allow OpenSSH
ufw allow "Nginx Full"
ufw --force enable
ufw status
```

*Expected Successful Output*:
```text
Status: active

To                         Action      From
--                         ------      ----
OpenSSH                    ALLOW       Anywhere
Nginx Full                 ALLOW       Anywhere
```

---

## Step 3: Install Nginx, PHP 8.2 / 8.3 FPM, MySQL 8, and Tools

Install Nginx web server, PHP 8.2/8.3 with required extensions, MySQL server, Git, and Unzip:

```bash
apt install -y nginx mysql-server git unzip curl \
  php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml \
  php8.2-curl php8.2-gd php8.2-zip php8.2-intl
```

Verify the PHP version:

```bash
php -v
```

*Expected Successful Output*:
```text
PHP 8.2.x (cli) (built: ...) (NTS)
Copyright (c) The PHP Group
```

Ensure PHP-FPM and Nginx are enabled and active:

```bash
systemctl enable --now nginx php8.2-fpm mysql
systemctl status php8.2-fpm --no-pager
```

---

## Step 4: Install Composer (PHP Package Manager)

Install the latest Composer binary globally:

```bash
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
composer --version
```

*Expected Successful Output*:
```text
Composer version 2.7.x ...
```

---

## Step 5: Configure MySQL Database and Create Database User

1. Secure your MySQL installation:

```bash
mysql_secure_installation
```
*(Press Y to configure password policy, remove anonymous users, disallow remote root login, and reload privilege tables).*

2. Log into the MySQL command line:

```bash
mysql -u root -p
```

3. Execute the following SQL commands to create the Sidra database and dedicated non-root user (replace `YourSecurePasswordHere!` with a strong password):

```sql
CREATE DATABASE sidra_ticketing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'sidra_user'@'localhost' IDENTIFIED BY 'YourSecurePasswordHere!';
GRANT ALL PRIVILEGES ON sidra_ticketing.* TO 'sidra_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

*Expected Successful Output*:
```text
Query OK, 0 rows affected (0.01 sec)
Bye
```

---

## Step 6: Deploy Application Code to `/var/www/sidra`

Create the web root directory and clone or copy your project files:

```bash
mkdir -p /var/www/sidra
cd /var/www/sidra
```

### Option A: Clone from Git Repository
```bash
git clone https://github.com/your-org/sidra.bd.git /var/www/sidra
```

### Option B: Upload via SCP / SFTP
From your local computer:
```bash
scp -r d:/projects/sidra.bd/* root@YOUR_SERVER_IP:/var/www/sidra/
```

---

## Step 7: Install Production PHP Dependencies

Inside `/var/www/sidra`, install required production libraries and generate an optimized autoloader:

```bash
cd /var/www/sidra
composer install --no-dev --optimize-autoloader
```

*Expected Successful Output*:
```text
Installing dependencies from lock file
Generating optimized autoload files
Generated optimized autoload class loader
```

---

## Step 8: Configure the Production `.env` Environment

Copy the example environment template:

```bash
cp .env.example .env
nano .env
```

Set the following variables (adjust domain and password accordingly):

```ini
APP_NAME="Sidra Event Ticketing Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ticketing.yourdomain.com
APP_KEY=base64:7f9c2d1e8a4b6c3d5e0f1a2b3c4d5e6f7a8b9c0d1e2f3a4b5c6d7e8f9a0b1c2d

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sidra_ticketing
DB_USERNAME=sidra_user
DB_PASSWORD=YourSecurePasswordHere!

TICKET_SECRET_KEY=s1dr4_s3cur3_v3r1f1c4t10n_k3y_98745230918237465192837465918237
SESSION_LIFETIME=7200
SESSION_SECURE_COOKIE=true

DEFAULT_CURRENCY=BDT
DEFAULT_CURRENCY_SYMBOL="৳"
MAX_TICKETS_PER_ORDER=10
```

*(Press `CTRL + O`, then `ENTER` to save, and `CTRL + X` to exit `nano`).*

---

## Step 9: Run Database Migrations and Seed Initial Data

Run Sidra's built-in migration tool:

```bash
php database/migrate.php --seed
```

*Expected Successful Output*:
```text
[SIDRA] Starting Sidra Database Migration...
[SIDRA] Connecting to MySQL host: 127.0.0.1
[SIDRA] Ensuring database `sidra_ticketing` exists...
[SIDRA] Executing MySQL schema (database/schema.sql)...
[SIDRA] MySQL Schema migrated successfully!
[SIDRA] Executing Seeds (database/seeds.sql)...
[SIDRA] Database seeded successfully with initial staff, sample events, and settings!
[SIDRA] ============================================================
[SIDRA] Migration Complete! Your Sidra database is ready.
[SIDRA] Default Super Admin Login:  admin@sidra.test    | Pass: Password123!
[SIDRA] Default Gate Staff:         gate@sidra.test     | Pass: Password123!
[SIDRA] Default Customer Account:   customer@sidra.test | Pass: Password123!
[SIDRA] ============================================================
```

---

## Step 10: Set Ownership and File Permissions

Configure the web server user (`www-data`) as the owner and grant write access strictly where needed:

```bash
# Set ownership to web user
chown -R www-data:www-data /var/www/sidra

# Standard permissions
find /var/www/sidra -type f -exec chmod 644 {} \;
find /var/www/sidra -type d -exec chmod 755 {} \;

# Writable directories for uploads, sessions, and logs
chmod -R 775 /var/www/sidra/storage
chmod -R 775 /var/www/sidra/public/uploads

# Protect .env file from unauthorized read
chmod 600 /var/www/sidra/.env
```

---

## Step 11: Configure Production Nginx Virtual Host

1. Create a new Nginx server configuration:

```bash
nano /etc/nginx/sites-available/sidra.conf
```

2. Paste the following production Nginx block (replace `ticketing.yourdomain.com` with your real domain):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name ticketing.yourdomain.com;

    # Document Root is strictly the public/ subfolder
    root /var/www/sidra/public;
    index index.php index.html;

    # Maximum file upload size (bKash/Nagad screenshot proofs)
    client_max_body_size 10M;

    # Gzip compression for high speed
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript application/xml+rss image/svg+xml;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Front Controller Pattern
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # FastCGI PHP Handling
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;

        # Mitigate FastCGI buffer overflow on large headers
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    # CRITICAL: Prevent direct PHP execution inside uploads directory
    location ~* ^/uploads/.*\.php$ {
        deny all;
        return 403;
    }

    # Cache static assets (CSS, JS, images, SVG, fonts) for 30 days
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
        access_log off;
    }

    # Block access to hidden files (.env, .git, etc.)
    location ~ /\.(?!well-known).* {
        deny all;
        return 404;
    }
}
```

3. Enable the site configuration and test syntax:

```bash
ln -s /etc/nginx/sites-available/sidra.conf /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t
```

*Expected Successful Output*:
```text
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration file /etc/nginx/nginx.conf test is successful
```

4. Reload Nginx:

```bash
systemctl reload nginx
```

---

## Step 12: Install Let's Encrypt Free SSL Certificate

Install Certbot for Nginx and issue an automated SSL certificate:

```bash
apt install -y certbot python3-certbot-nginx
certbot --nginx -d ticketing.yourdomain.com
```

- Enter your admin email when prompted.
- Agree to terms of service (`Y`).
- Choose option `2` to **Redirect HTTP traffic to HTTPS**.

*Expected Successful Output*:
```text
Successfully received certificate.
Certificate is saved at: /etc/letsencrypt/live/ticketing.yourdomain.com/fullchain.pem
Key is saved at:         /etc/letsencrypt/live/ticketing.yourdomain.com/privkey.pem
Deploying certificate
Successfully deployed certificate for ticketing.yourdomain.com to /etc/nginx/sites-enabled/sidra.conf
Congratulations! You have successfully enabled HTTPS on https://ticketing.yourdomain.com
```

Certbot automatically schedules background auto-renewal via systemd timer (`systemctl list-timers | grep certbot`).

---

## Step 13: Configure Systemd Background Cron Task

Configure a recurring cron job for the `www-data` user to cancel expired reservations and rotate logs:

```bash
crontab -e -u www-data
```

Add the following line at the bottom:

```bash
*/10 * * * * /usr/bin/php /var/www/sidra/console/cron.php >> /var/www/sidra/storage/logs/cron.log 2>&1
```

Save and exit. Check the active crontab:

```bash
crontab -l -u www-data
```

---

## Step 14: Automated Daily Database Backup Script

Create a robust backup script to protect your ticketing records:

1. Create directory and backup script:

```bash
mkdir -p /root/backups
nano /root/backup_sidra.sh
```

2. Add the following script:

```bash
#!/bin/bash
BACKUP_DIR="/root/backups"
DATE=$(date +'%Y-%m-%d_%H%M%S')
DB_NAME="sidra_ticketing"
DB_USER="sidra_user"
DB_PASS="YourSecurePasswordHere!"

mkdir -p $BACKUP_DIR
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/sidra_$DATE.sql.gz

# Delete backups older than 14 days
find $BACKUP_DIR -type f -name "sidra_*.sql.gz" -mtime +14 -delete
echo "[$(date)] Sidra database backup completed: sidra_$DATE.sql.gz"
```

3. Make executable and add to root crontab to run at 2:00 AM daily:

```bash
chmod +x /root/backup_sidra.sh
(crontab -l 2>/dev/null; echo "0 2 * * * /root/backup_sidra.sh >> /var/log/sidra_backup.log 2>&1") | crontab -
```

---

## Step 15: Verification & Production Smoke Testing

Run the following checks to ensure your deployment is operational:

```bash
# 1. Run full test suite on VPS environment
cd /var/www/sidra
php tests/run_tests.php
```

*Expected Successful Output*:
```text
=================================================================
TEST SUITE SUMMARY:
Total: 70 | Passed: 70 | Failed: 0
ALL TESTS COMPLETED WITH 100% SUCCESS!
=================================================================
```

```bash
# 2. Test HTTP to HTTPS redirection
curl -I http://ticketing.yourdomain.com
# Should output: HTTP/1.1 301 Moved Permanently -> Location: https://...

# 3. Test .env file security protection
curl -I https://ticketing.yourdomain.com/.env
# Should output: HTTP/2 404 Not Found or 403 Forbidden
```

4. Open your web browser and visit `https://ticketing.yourdomain.com`:
   - Verify green padlock.
   - Log into `/admin/login` (`admin@sidra.test` / `Password123!`).
   - Immediately update the administrator password in **Staff Management**.
