# MentoraKelSatu

Platform untuk menghubungkan mahasiswa dan dosen dalam pengerjaan tugas/proyek.

## Quick Start

### 1. Clone Repository
```bash
git clone <repository-url>
cd MentoraKelSatu
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Setup Database
- Buat database baru
- Update konfigurasi database di `.env`
- Jalankan migration:
```bash
php artisan migrate
```

### 5. **⚠️ PENTING: Setup Storage Link**
**Symlink HARUS dibuat agar foto profil dan bukti pembayaran bisa diakses:**

```bash
php artisan storage:link
```

**Catatan:** 
- Symlink `public/storage` tidak ikut ke repository (ada di `.gitignore`)
- Setiap developer **HARUS** menjalankan command ini setelah clone
- Di Windows, pastikan menjalankan command prompt/PowerShell sebagai Administrator jika symlink gagal dibuat

### 6. Build Assets
```bash
npm run build
```

### 7. Jalankan Server
```bash
php artisan serve
```

## Setup Lengkap

Lihat [SETUP.md](SETUP.md) untuk panduan setup lengkap dan troubleshooting.

## Troubleshooting Foto Profil Tidak Tampil

1. **Pastikan symlink sudah dibuat:**
   ```bash
   php artisan storage:link
   ```

2. **Cek permission folder (Linux/Mac):**
   ```bash
   chmod -R 775 storage
   chmod -R 775 bootstrap/cache
   ```

3. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

Lihat [SETUP.md](SETUP.md) untuk troubleshooting lengkap.

## Tech Stack

- Laravel 11
- Tailwind CSS
- Alpine.js
- MySQL/PostgreSQL

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
