# Multifunction App (Laravel + PostgreSQL + Cloudflare Tunnel)

Sistem aplikasi web multifungsi berbasis **Laravel** dan **PostgreSQL** yang berjalan di **Ubuntu Server (HomeServer)**. Aplikasi ini dirancang untuk manajemen file, antrean tugas (*task monitoring*), dan integrasi otomatisasi background process.

---

## 🛠️ Stack & Infrastructure

- **OS:** Ubuntu Server 24.04 LTS
- **Web Server:** Nginx 1.28
- **PHP:** PHP 8.5 (FPM)
- **Database:** PostgreSQL (`laravel_db`)
- **Framework:** Laravel 11.x / 13.x
- **Public Tunneling:** Cloudflare Quick Tunnel (`cloudflared`) via Systemd
- **Private Access:** Tailscale VPN
- **Version Control & CI/CD:** Git, GitHub, & GitHub Actions (Auto-Deploy)

---

## 🚀 Dokumentasi Setup Server dari Awal

### 1. Cloudflare Quick Tunnel (Background Service)
Systemd service dibuat di `/etc/systemd/system/quick-tunnel.service` agar tunnel tetap aktif tanpa domain:
```bash
sudo systemctl enable --now quick-tunnel.service
# Cek URL Publik:
sudo journalctl -u quick-tunnel.service | grep trycloudflare.com
```

### 2. Database PostgreSQL Setup
```sql
CREATE DATABASE laravel_db;
CREATE USER laravel_user WITH PASSWORD 'password_super_aman';
GRANT ALL PRIVILEGES ON DATABASE laravel_db TO laravel_user;
ALTER DATABASE laravel_db OWNER TO laravel_user;
```

### 3. Setup Project Laravel & Extension PHP
- Install ekstensi PHP & Composer: `php-fpm`, `php-pgsql`, `php-curl`, `php-mbstring`, `php-xml`, `php-zip`, `php-gd`, `php-cli`.
- Setup environment `.env`: `DB_CONNECTION=pgsql`
- Hak akses direktori storage:
  ```bash
  sudo chown -R home:www-data /var/www/my-app
  sudo chmod -R 775 /var/www/my-app/storage /var/www/my-app/bootstrap/cache
  ```

### 4. Nginx FastCGI Pass Configuration
Mengaitkan Virtual Host Nginx `/etc/nginx/sites-available/my-app` ke Socket PHP 8.5 FPM:
```nginx
location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.5-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
}
```

---

## 🎯 Rencana Fitur Aplikasi (Roadmap)

1. **Task & Job Monitoring System:**
   - Visualisasi antrean tugas background process via Laravel Queue Worker & Supervisor.
   - Monitoring resource server real-time (Laravel Pulse).
2. **File Management System:**
   - Upload, penyimpanan, dan pengolahan file lampiran tugas ke PostgreSQL & Local Storage (`storage/app/public`).
3. **Automated CI/CD Pipeline:**
   - Alur pengodean bertingkat: `on-dev-*` ➡️ `staging` ➡️ `main` (Auto Deploy via SSH GitHub Actions).

---

## 🌿 Branching Strategy

- **`main`**: Branch production stabil (terintegrasi Auto-Deploy ke server).
- **`staging`**: Branch integrasi dan testing sebelum ke production.
- **`on-dev-*`**: Branch pengembangan fitur individual.
