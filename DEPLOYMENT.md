# Deployment Guide
### The Karen Hospital School of Nursing — School Management System (by Algotech Labs)

A step-by-step guide to deploying this Laravel 9 application to a production server.

---

## 1. Architecture at a glance

| Component | Detail |
|---|---|
| Framework | Laravel 9 (PHP) |
| PHP | **8.1** recommended (min 8.0) |
| Database | **MySQL 8** (or MariaDB 10.4+) |
| Web server | Nginx + PHP-FPM (recommended) or Apache |
| Document root | **`public/`** (never the project root) |
| Sessions / cache / queue | file / file / sync (single-server default — no Redis required) |
| File uploads | stored under `public/assets/uploads/*` |
| Schema | provided as a **SQL dump** (`database/dump/ekattor8_full_dump.sql`) — this app is seeded via SQL, not `artisan migrate` |

> **Do not** run `php artisan migrate` on this project — the `migrations` table is intentionally empty and the schema ships as a SQL dump. Migrating would try to recreate existing tables and fail.

---

## 2. Server requirements

- Ubuntu 22.04 LTS (or similar) with root/sudo
- **PHP 8.1** with extensions: `bcmath, ctype, curl, dom, fileinfo, gd, json, mbstring, openssl, pdo, pdo_mysql, tokenizer, xml, zip`
- **Composer 2**
- **MySQL 8** (or MariaDB)
- Nginx (or Apache)
- A domain name pointed at the server (for HTTPS)

Install the stack on Ubuntu:

```bash
sudo apt update
sudo apt install -y nginx mysql-server unzip curl \
  php8.1-fpm php8.1-cli php8.1-mysql php8.1-mbstring php8.1-xml \
  php8.1-curl php8.1-zip php8.1-gd php8.1-bcmath
# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## 3. Get the code onto the server

```bash
sudo mkdir -p /var/www/karen-sms
sudo chown -R $USER:$USER /var/www/karen-sms
# Upload/clone the project (the folder that contains artisan, app/, public/, ...)
cd /var/www/karen-sms
# e.g. rsync from your machine, git clone, or unzip a release archive
```

---

## 4. Install PHP dependencies

```bash
cd /var/www/karen-sms
composer install --no-dev --optimize-autoloader
```

---

## 5. Configure the environment (`.env`)

Copy the example and edit production values:

```bash
cp .env.example .env   # if .env.example exists; otherwise edit the shipped .env
```

Set at minimum:

```dotenv
APP_NAME="Algotech School Management System"
APP_ENV=production
APP_DEBUG=false                      # CRITICAL — never true in production
APP_URL=https://sms.karenhospital.org

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306                         # standard MySQL port on the server
DB_DATABASE=karen_sms
DB_USERNAME=karen_sms_user
DB_PASSWORD=<strong-password>

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Outgoing email (used for password resets, notifications)
MAIL_MAILER=smtp
MAIL_HOST=<smtp-host>
MAIL_PORT=587
MAIL_USERNAME=<smtp-user>
MAIL_PASSWORD=<smtp-pass>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@karenhospital.org"
MAIL_FROM_NAME="${APP_NAME}"
```

Generate the application key (only if `APP_KEY` is empty):

```bash
php artisan key:generate
```

---

## 6. Create and load the database

```bash
# 1) create the database + a least-privilege user
sudo mysql <<'SQL'
CREATE DATABASE karen_sms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'karen_sms_user'@'localhost' IDENTIFIED BY '<strong-password>';
GRANT SELECT, INSERT, UPDATE, DELETE ON karen_sms.* TO 'karen_sms_user'@'localhost';
FLUSH PRIVILEGES;
SQL

# 2) import the full dump (schema + data)
mysql -u root -p karen_sms < database/dump/ekattor8_full_dump.sql
```

> **Note on demo data:** the dump includes demo accounts (300 students, 25 teachers, etc.). For a live school you can keep it as a working base and clean the demo records later, or start from the dump and re-enter real users. See §11.

**Alternative — starting completely clean:** import the dump to get the schema, then delete the demo `users`/`enrollments` and create your own admin + students. (The app is designed to run from this dump; there is no `artisan migrate` path.)

---

## 7. File & folder permissions

Laravel needs `storage/` and `bootstrap/cache/` writable, and the app writes uploads under `public/assets/uploads/`:

```bash
cd /var/www/karen-sms
sudo chown -R www-data:www-data storage bootstrap/cache public/assets/uploads
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
sudo chmod -R 775 public/assets/uploads
```

Ensure these upload subfolders exist (created automatically on first upload, but pre-create to be safe):

```
public/assets/uploads/{logo,user,syllabus,assignments,submissions,
  course_materials,course_thumbnails,noticeboard,expenses,offline_payment,email_logo}
```

---

## 8. Web server configuration (Nginx + PHP-FPM)

Create `/etc/nginx/sites-available/karen-sms`:

```nginx
server {
    listen 80;
    server_name sms.karenhospital.org;
    root /var/www/karen-sms/public;      # <-- public/ is the web root

    index index.php;
    charset utf-8;
    client_max_body_size 50M;            # allow file/material uploads

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* { deny all; }   # block dotfiles (.env etc.)
}
```

Enable and reload:

```bash
sudo ln -s /etc/nginx/sites-available/karen-sms /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

**Apache alternative:** point the `DocumentRoot` at `.../public`, enable `mod_rewrite`, and allow `.htaccess` overrides (the shipped `public/.htaccess` handles routing).

---

## 9. HTTPS / SSL (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d sms.karenhospital.org
```

Certbot auto-configures the redirect and renewal. Confirm `APP_URL` uses `https://`.

---

## 10. Optimize for production

```bash
cd /var/www/karen-sms
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> After **any** later change to `.env`, routes, or config, re-run these (or `php artisan optimize:clear` then re-cache).

---

## 11. Post-deploy configuration (in the app)

1. **Log in as the Super Admin** and **change every default password immediately** (see `PERSONAS-CREDENTIALS.md`). The universal demo password `12345678` must not survive to production.
2. **Branding** is already set for Karen Hospital (logo, colours, footer "By Algotech Labs"). Adjust under **Super Admin → Settings → Website / System** if needed.
3. **Landing page** is set to go straight to the branded login (`frontend_view = 0`). To restore the marketing site instead, set it back to `1`.
4. **Currency / timezone** — Super Admin → Settings → System (currently KES / configured timezone).
5. **SMTP** — either via `.env` (above) or Super Admin → Settings → SMTP.
6. **Page-view logging** — Super Admin → **Activity Log** → settings bar. It is currently **ON** (logs every page a user opens). For a large live school, consider turning it **off** or lowering the retention days to keep the table small.
7. **Clean demo data** if starting fresh — remove the seeded demo students/teachers you don't need.

---

## 12. Go-live verification checklist

- [ ] `https://<domain>/` loads the branded **login page** (not a Laravel error)
- [ ] Log in as **Super Admin**, **Admin**, **Teacher**, **Accountant**, **Librarian**, **Parent**, **Student** — each reaches its dashboard
- [ ] Create a test student (Admin) → appears in the list
- [ ] Upload a file (e.g., an assignment attachment) → saves without error (validates upload dir permissions)
- [ ] Record a fee payment (Accountant/Admin) → receipt prints, dashboard updates
- [ ] Super Admin → **Activity Log** shows logins + actions with IP/time
- [ ] Password reset email is received (validates SMTP)
- [ ] `APP_DEBUG=false` (view source of a deliberately bad URL — you should get a generic error page, not a stack trace)

---

## 13. Security hardening (already applied + to confirm)

Applied in code (see `BUGS.md`): grade-write authorization, IDOR ownership checks, upload MIME validation, CSRF on all delete routes (now POST), auth-order fixes, disabled public registration. **Confirm on the server:**

- [ ] `APP_DEBUG=false`, `APP_ENV=production`
- [ ] HTTPS enforced (Certbot redirect)
- [ ] `.env` is **not** web-accessible (Nginx dotfile rule above) and is `chmod 640`
- [ ] MySQL user has only `SELECT/INSERT/UPDATE/DELETE` on the app DB (no root)
- [ ] Server firewall: allow 80/443/22 only (`ufw`)
- [ ] Default passwords changed for **all** roles
- [ ] File permissions per §7 (app runs as `www-data`, not root)

---

## 14. Backups & maintenance

**Nightly database backup** (cron):

```bash
sudo crontab -e
# add:
0 2 * * * mysqldump -u root -p'<pass>' karen_sms | gzip > /var/backups/karen_sms_$(date +\%F).sql.gz
```

Also back up `public/assets/uploads/` (user files: logos, syllabi, submissions, receipts).

**Activity/visit logs** self-prune to the retention window set in the Activity Log settings (default 30 days). No cron needed.

**Laravel scheduler (optional):** if you later add scheduled jobs, enable the cron:
```bash
* * * * * cd /var/www/karen-sms && php artisan schedule:run >> /dev/null 2>&1
```

---

## 15. Updating / redeploying

```bash
cd /var/www/karen-sms
php artisan down                       # maintenance mode
# pull/upload new code
composer install --no-dev --optimize-autoloader
# apply any new database/*.sql changes if a release includes them:
#   mysql -u root -p karen_sms < database/<new_change>.sql
php artisan optimize:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
```

---

## 16. Troubleshooting

| Symptom | Likely cause / fix |
|---|---|
| **HTTP 500 on every page** | `APP_KEY` empty (`php artisan key:generate`), or `storage/`+`bootstrap/cache` not writable (§7), or stale config cache (`php artisan optimize:clear`) |
| **Blank white page** | `storage/logs/` not writable; check `storage/logs/laravel.log` |
| **Uploads fail / 500 on file upload** | `public/assets/uploads/*` not writable, or `client_max_body_size` too small in Nginx |
| **Routes 404 after deploy** | run `php artisan route:cache`; ensure `try_files … /index.php` in Nginx |
| **CSS/JS missing** | web root must be `public/`, not the project root |
| **Login redirects in a loop** | wrong `APP_URL`/session domain; clear cookies + `php artisan optimize:clear` |
| **"Table already exists" on import** | database wasn't empty — drop and recreate it, then re-import the dump |
| **Emails not sending** | verify SMTP creds in `.env`/Settings; check port 587/TLS |

---

## 17. Shared hosting / cPanel (alternative)

If deploying to cPanel instead of a VPS:

1. Upload the project outside `public_html` (e.g. `/home/user/karen-sms`), and move/point `public_html` to the project's `public/` folder (or copy `public/*` into `public_html` and edit `index.php` paths).
2. Create the MySQL database + user in cPanel, then import `database/dump/ekattor8_full_dump.sql` via **phpMyAdmin**.
3. Set PHP version to **8.1** in cPanel (MultiPHP Manager) and enable the required extensions.
4. Edit `.env` with the cPanel DB credentials and `APP_URL`; set `APP_DEBUG=false`.
5. Run the optimize commands via SSH if available, or use cPanel's Terminal/Cron.
6. Set folder permissions on `storage`, `bootstrap/cache`, and `public/assets/uploads` to `755/775`.

---

*Deployment guide prepared by Algotech Labs for The Karen Hospital School of Nursing.
See also: `PERSONAS-CREDENTIALS.md` (logins), `PLATFORM-FEATURES.md` (features), `BUGS.md` (security/QA record).*
