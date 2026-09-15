# Production Deployment Guide — Sidra Event Ticketing Platform

This guide provides production-grade deployment procedures for hosting the **Sidra Event Ticketing Platform** on either:
1. **Shared Hosting (cPanel / DirectAdmin / Plesk)**
2. **Dedicated Cloud VPS (Ubuntu 22.04/24.04 LTS + Nginx + PHP 8.2-FPM + MySQL 8+)**

---

## Architecture Requirements for Production

- **PHP Version:** PHP 8.2 or 8.3 with extensions:
  - `pdo_mysql`, `openssl`, `mbstring`, `fileinfo`, `curl`, `gd`, `zip`
- **Database:** MySQL 8.0+ or MariaDB 10.6+ with InnoDB engine
- **Web Server:** Nginx or Apache (with `mod_rewrite` enabled)
- **SSL Certificate:** HTTPS is mandatory for secure camera QR scanning via WebRTC (`getUserMedia` API requires HTTPS).

---

# SECTION 1: Shared Hosting Deployment (cPanel)

### Step 1: Upload Files
1. Compress your project into a `.zip` file (**exclude** `.git` and `storage/logs/`).
2. Open **cPanel File Manager**.
3. Create a directory named `sidra_app` outside of `public_html` (e.g. `/home/username/sidra_app`).
4. Upload and extract the zip archive inside `/home/username/sidra_app`.

### Step 2: Configure the Document Root
For security, only the `public/` directory must be accessible to the public internet:
- **Option A (Subdomain / Addon Domain):** Set the document root in cPanel to `/home/username/sidra_app/public`.
- **Option B (Main Domain):** If cPanel forces your document root to `public_html`:
  1. Move the contents of `sidra_app/public/` directly into `public_html/`.
  2. Edit `public_html/index.php` and update the bootstrap paths:
     ```php
     require_once __DIR__ . '/../sidra_app/vendor/autoload.php';
     require_once __DIR__ . '/../sidra_app/app/Core/Helpers.php';
     ```

### Step 3: Create MySQL Database in cPanel
1. Go to **cPanel > MySQL Database Wizard**.
2. Create database: `username_sidra`.
3. Create database user: `username_dbuser` with a strong password.
4. Assign user to the database with **ALL PRIVILEGES**.
5. Go to **phpMyAdmin**, select the database, click **Import**, and upload `database/schema.sql` followed by `database/seeds.sql`.

### Step 4: Configure `.env`
In `/home/username/sidra_app/.env`:
```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_KEY=base64:your-64-character-random-hex-key

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=username_sidra
DB_USERNAME=username_dbuser
DB_PASSWORD=YourStrongPasswordHere

SESSION_SECURE=true
```

### Step 5: Set Permissions
Ensure the web server can write to uploads and storage:
```bash
chmod -R 755 public/uploads
chmod -R 775 storage
```

---

# SECTION 2: Linux Cloud VPS Deployment (Ubuntu + Nginx + PHP-FPM)

Recommended cloud providers: DigitalOcean, Linode, AWS EC2, Hetzner, or Google Cloud.

### Step 1: System Packages Installation
Connect via SSH to your clean Ubuntu server and run:

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server git curl unzip ufw certbot python3-certbot-nginx

# Add PHP PPA
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update

# Install PHP 8.2 & Extensions
sudo apt install -y php8.2-fpm php8.2-mysql php8.2-curl php8.2-gd php8.2-mbstring \
                    php8.2-xml php8.2-zip php8.2-bcmath php8.2-intl php8.2-cli

# Install Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

### Step 2: Configure MySQL 8+ Database
Run MySQL secure installation:
```bash
sudo mysql_secure_installation
```

Log in and create production database:
```sql
sudo mysql -u root -p
```
Run the following SQL commands:
```sql
CREATE DATABASE sidra_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'sidra_user'@'localhost' IDENTIFIED BY 'STRONG_PROD_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON sidra_production.* TO 'sidra_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Clone Codebase & Install Dependencies
```bash
cd /var/www
sudo git clone https://github.com/your-username/sidra.bd.git sidra
cd /var/www/sidra

# Install Composer dependencies (optimized for production)
sudo composer install --no-dev --optimize-autoloader

# Run database migration & seeds
php database/migrate.php --seed
```

### Step 4: Configure Production Environment (`.env`)
Create `/var/www/sidra/.env`:
```ini
APP_NAME="Sidra Event Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tickets.yourdomain.com
APP_KEY=YOUR_64_CHAR_HEX_KEY_HERE

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=sidra_production
DB_USERNAME=sidra_user
DB_PASSWORD=STRONG_PROD_PASSWORD_HERE

SESSION_LIFETIME=7200
SESSION_SECURE=true
```

### Step 5: Configure Permissions
```bash
sudo chown -R www-data:www-data /var/www/sidra
sudo chmod -R 755 /var/www/sidra
sudo chmod -R 775 /var/www/sidra/storage
sudo chmod -R 775 /var/www/sidra/public/uploads
```

### Step 6: Configure Nginx Virtual Host
Create `/etc/nginx/sites-available/sidra`:
```nginx
server {
    listen 80;
    server_name tickets.yourdomain.com;
    root /var/www/sidra/public;
    index index.php index.html;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;

    # Max upload size (for payment screenshot proofs)
    client_max_body_size 12M;

    # Gzip Compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml image/svg+xml;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Pass PHP scripts to FastCGI server
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Block access to hidden files (.env, .git)
    location ~ /\. {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }
}
```

Enable the site and test configuration:
```bash
sudo ln -s /etc/nginx/sites-available/sidra /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 7: Install Free SSL Certificate (Let's Encrypt)
HTTPS is required for the gate camera scanner:
```bash
sudo certbot --nginx -d tickets.yourdomain.com
```
*Certbot will automatically obtain certificates and configure SSL renewal cron.*

### Step 8: Configure Firewall (UFW)
```bash
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw enable
```

---

## 3. Automated Database Backup Strategy

To ensure zero data loss, create an automated daily backup cron job:
Create `/usr/local/bin/backup_sidra.sh`:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/sidra"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p "$BACKUP_DIR"

mysqldump -u sidra_user -p'STRONG_PROD_PASSWORD_HERE' sidra_production | gzip > "$BACKUP_DIR/sidra_$DATE.sql.gz"

# Retain backups for 14 days only
find "$BACKUP_DIR" -type f -name "*.sql.gz" -mtime +14 -delete
```

Make it executable and add to crontab:
```bash
sudo chmod +x /usr/local/bin/backup_sidra.sh
sudo crontab -e
```
Add the following line to run every day at 3:00 AM:
```
0 3 * * * /usr/local/bin/backup_sidra.sh >/dev/null 2>&1
```

---

## 4. Production Checklist

- [x] Set `APP_DEBUG=false` and `APP_ENV=production` in `.env`.
- [x] Generated unique `APP_KEY`.
- [x] Verified HTTPS certificate is active.
- [x] Configured official bKash, Nagad, and Rocket numbers in `/admin/settings`.
- [x] Changed default staff passwords from `Password123!` to strong custom credentials.
- [x] Confirmed `storage/` and `public/uploads/` are writable by web server user.
- [x] Tested camera QR check-in on a mobile device or tablet over HTTPS.
