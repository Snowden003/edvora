# 🚀 Edvora — Production Deployment Guide

## Requirements

| Tool | Version |
|------|---------|
| PHP | ^8.3 |
| MySQL | 5.7+ / 8.0+ |
| Composer | 2.x |
| Node.js | 18+ |
| npm | 9+ |

> **Note:** Ensure `mod_rewrite` is enabled on Apache-based hosts (cPanel).

---

## 1. Upload Project Files

Upload the project to the server, preferably **outside** of `public_html`.

**Recommended structure:**
```
/home/username/
    edvora-project/        ← entire project goes here
    public_html/           ← contents of the project's public/ folder go here
```

---

## 2. Set Document Root

Copy the contents of `public/` into `public_html`, **or** point the host's Document Root to `edvora-project/public`.

---

## 3. Install Dependencies

```bash
# Install PHP packages (production only)
composer install --no-dev --optimize-autoloader

# Install Node packages
npm install

# Build CSS & JS assets
npm run build
```

---

## 4. Configure Environment

```bash
# Copy the example env file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Then edit `.env` and fill in the following values:

```env
APP_NAME=Edvora
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Session, Queue & Cache (all use database driver)
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_PORT=587
MAIL_USERNAME=your_email@domain.com
MAIL_PASSWORD=your_mail_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Edvora"

# MiroTalk (online class video conferencing)
MIROTALK_URL=https://meet.edvoratech.com
MIROTALK_API_SECRET=your_mirotalk_api_secret
```

---

## 5. Run Migrations & Seeders

```bash
# Create all database tables
php artisan migrate --force

# (Optional) Seed initial data — categories, courses, events, competitions, students
php artisan db:seed --force
```

> **Warning:** Only run `db:seed` on the first deployment or a fresh database. It inserts sample data.

---

## 6. Create Storage Symlink

```bash
php artisan storage:link
```

---

## 7. Optimize (Cache Config, Routes & Views)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Or all at once:
php artisan optimize
```

---

## 8. Set File Permissions

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 9. Queue Worker

The project uses **database queues** for sending emails and notifications.

### Option A: Supervisor (recommended for VPS)

```bash
php artisan queue:work --sleep=3 --tries=3 --daemon
```

Supervisor config (`/etc/supervisor/conf.d/edvora-worker.conf`):
```ini
[program:edvora-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/edvora-project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/edvora-project/storage/logs/worker.log
```

```bash
supervisorctl reread
supervisorctl update
supervisorctl start edvora-worker:*
```

### Option B: Shared Hosting

Use the cron job below to process queued jobs periodically.

---

## 10. Cron Job (Task Scheduler)

The project schedules `notifications:class-reminders` to run every minute.

Add the following line to your server's crontab (via cPanel or `crontab -e`):

```bash
* * * * * cd /path/to/edvora-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 11. Deploying Updates

```bash
php artisan down                                  # Enable maintenance mode
git pull                                          # Pull latest changes
composer install --no-dev --optimize-autoloader   # Update PHP dependencies
npm run build                                     # Rebuild assets
php artisan migrate --force                       # Run new migrations
php artisan optimize:clear                        # Clear all caches
php artisan optimize                              # Rebuild caches
php artisan up                                    # Disable maintenance mode
```

---

## Final Checklist ✅

- [ ] PHP 8.3+ is available on the server
- [ ] `public/` directory is set as Document Root
- [ ] `.env` is configured and `APP_KEY` is generated
- [ ] `composer install --no-dev --optimize-autoloader` has been run
- [ ] `npm run build` has been run and `public/build/` exists
- [ ] `php artisan migrate --force` has been run
- [ ] `php artisan storage:link` has been run
- [ ] `php artisan optimize` has been run
- [ ] `storage/` and `bootstrap/cache/` have `775` permissions
- [ ] Cron job for `schedule:run` is active
- [ ] Queue Worker / Supervisor is running
- [ ] `APP_DEBUG=false` is set in production
