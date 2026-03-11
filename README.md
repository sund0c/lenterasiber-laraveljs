# Lentera Siber — Laravel Backend

Platform literasi keamanan siber untuk ASN Pemprov Bali.

## Stack
- **PHP 8.2+** + **Laravel 11**
- **MySQL 8.0** (atau MariaDB 10.6+)
- Session & Cache: **database** driver (tanpa Redis)
- 2FA: **TOTP RFC 6238** — pure PHP, tanpa library eksternal
- QR Code: **Pure PHP GD** — tanpa CDN, tanpa composer package

---

## Instalasi di VM (Isolated, tanpa internet)

### 1. Persyaratan VM
```
php >= 8.2
php-mbstring php-xml php-gd php-pdo php-pdo-mysql php-bcmath php-curl php-zip
mysql-server >= 8.0
nginx
```

### 2. Clone / Copy project
```bash
cp lentera-siber-laravel.zip /var/www/
cd /var/www
unzip lentera-siber-laravel.zip
cd lentera-siber
```

### 3. Install dependencies (di mesin dengan internet, lalu copy vendor/)
```bash
# Di mesin development dengan internet:
composer install --no-dev --optimize-autoloader

# Copy folder vendor/ ke VM via SCP/USB
scp -r vendor/ user@vm-ip:/var/www/lentera-siber/
```

### 4. Konfigurasi .env
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://lenterasiber.baliprov.go.id

DB_HOST=127.0.0.1
DB_DATABASE=lentera_siber
DB_USERNAME=lentera_user
DB_PASSWORD=your_strong_password

TOTP_ISSUER="Lentera Siber Admin"
ADMIN_LOGIN_PATH=/portal-internal-x83fj9/login
```

### 5. Database
```sql
-- Jalankan sebagai root MySQL
CREATE DATABASE lentera_siber CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'lentera_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP
  ON lentera_siber.* TO 'lentera_user'@'localhost';
FLUSH PRIVILEGES;
```

```bash
php artisan migrate --force
```

### 6. Buat admin user (SATU KALI)
```bash
php artisan admin:create
# Ikuti prompt: username, nama, email, password
# 2FA akan diminta saat login pertama
```

### 7. Storage
```bash
php artisan storage:link
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 8. Nginx config
```nginx
server {
    listen 443 ssl http2;
    server_name lenterasiber.baliprov.go.id;

    ssl_certificate     /etc/ssl/lentera-siber.crt;
    ssl_certificate_key /etc/ssl/lentera-siber.key;
    ssl_protocols       TLSv1.2 TLSv1.3;
    ssl_ciphers         HIGH:!aNULL:!MD5;

    root /var/www/lentera-siber/public;
    index index.php;

    # Hide server version
    server_tokens off;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        # Prevent PHP info leaks
        fastcgi_hide_header X-Powered-By;
    }

    # Block direct access to sensitive paths
    location ~ /\.(env|git|htaccess) { deny all; }
    location ~ ^/(storage|bootstrap/cache) { deny all; }

    # Static assets cache
    location ~* \.(css|js|png|jpg|jpeg|svg|ico|woff2?)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name lenterasiber.baliprov.go.id;
    return 301 https://$host$request_uri;
}
```

### 9. Laravel optimize
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 10. Cron (opsional)
```bash
# /etc/cron.d/lentera-siber
* * * * * www-data php /var/www/lentera-siber/artisan schedule:run >> /dev/null 2>&1
```

---

## Alur Login 2FA

```
1. GET  /portal-internal-x83fj9/login      → Form login
2. POST /portal-internal-x83fj9/login      → Validasi username + password
   ↓ (jika OK)
3. GET  /portal-internal-x83fj9/2fa/setup  → (pertama kali) Scan QR, konfirmasi kode
   atau
3. GET  /portal-internal-x83fj9/2fa/verify → Masukkan kode 6 digit dari authenticator
   ↓ (jika OK)
4. Redirect → /admin/
```

## Fitur Keamanan

| Fitur | Implementasi |
|---|---|
| 2FA TOTP | RFC 6238, pure PHP, tanpa library |
| QR Code | Pure PHP GD, tanpa CDN |
| Password | bcrypt cost 13, min 12 chars |
| Rate limiting | Laravel RateLimiter (per IP+username) |
| Account lockout | DB-level, survive restart |
| Session | Encrypted, HttpOnly, SameSite=Strict, 1 jam idle |
| IP binding | Session terikat ke IP login |
| CSRF | Laravel token, rotate per request |
| Security headers | CSP, HSTS, X-Frame-Options, dsb |
| Audit log | Semua aksi admin tercatat |
| SQL injection | Eloquent + Query Builder, prepared statements |
| XSS | Blade auto-escape, htmlspecialchars |
| File upload | MIME check via finfo, random filename |
| CDN | NOL — semua aset lokal |

---

## Struktur Direktori Penting

```
app/
  Http/
    Controllers/
      Auth/           ← LoginController, TotpController
      Admin/          ← DashboardController, KabarController, dst
      Api/            ← ContentController, ContactController
    Middleware/
      AuthStep1Middleware.php
      AuthFullMiddleware.php
      AuditMiddleware.php
      SecurityHeadersMiddleware.php
  Models/
    AdminUser.php
    AuditLog.php
  Services/
    TotpService.php   ← TOTP pure PHP
    QrCodeService.php ← QR Code pure PHP GD
  Console/Commands/
    CreateAdminUser.php
database/
  migrations/
    ..._create_admin_users_table.php
    ..._create_core_tables.php
public/
  css/admin.css       ← Semua CSS lokal
routes/
  web.php
```
