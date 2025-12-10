# Setup Guide - MentoraKelSatu

## Instalasi Awal

Setelah clone repository, jalankan langkah-langkah berikut:

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Setup Database
- Buat database baru
- Update konfigurasi database di `.env`
- Jalankan migration:
```bash
php artisan migrate
```

### 4. **PENTING: Setup Storage Link**
Symlink untuk storage **HARUS** dibuat agar foto profil dan bukti pembayaran bisa diakses:

```bash
php artisan storage:link
```

**Catatan:** 
- Symlink `public/storage` tidak ikut ke repository (ada di `.gitignore`)
- Setiap developer **HARUS** menjalankan command ini setelah clone
- Di Windows, pastikan menjalankan command prompt/PowerShell sebagai Administrator jika symlink gagal dibuat

### 5. Build Assets
```bash
npm run build
```

### 6. Jalankan Server
```bash
php artisan serve
```

## Troubleshooting

### Foto Profil Tidak Tampil

1. **Cek apakah symlink sudah dibuat:**
   ```bash
   php artisan storage:link
   ```

2. **Cek apakah folder storage ada:**
   - Pastikan folder `storage/app/public/profiles/` ada
   - Pastikan folder `storage/app/public/topups/` ada

3. **Cek permission folder (Linux/Mac):**
   ```bash
   chmod -R 775 storage
   chmod -R 775 bootstrap/cache
   ```

4. **Cek apakah file benar-benar ter-upload:**
   - Cek folder `storage/app/public/profiles/`
   - Cek database, kolom `photo` di table `users`

5. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

### Windows: Symlink Gagal Dibuat

Jika `php artisan storage:link` gagal di Windows:

1. **Jalankan sebagai Administrator:**
   - Buka Command Prompt atau PowerShell sebagai Administrator
   - Jalankan `php artisan storage:link`

2. **Atau gunakan mklink manual:**
   ```cmd
   mklink /D "public\storage" "storage\app\public"
   ```

3. **Atau copy folder (tidak disarankan untuk production):**
   - Copy seluruh isi `storage/app/public/` ke `public/storage/`
   - **Catatan:** Ini bukan solusi permanen, gunakan hanya untuk testing

## Struktur Storage

```
storage/
└── app/
    └── public/
        ├── profiles/     # Foto profil user
        └── topups/       # Bukti pembayaran topup

public/
└── storage/              # Symlink ke storage/app/public (dibuat dengan storage:link)
    ├── profiles/
    └── topups/
```

## Checklist Setup

- [ ] `composer install` selesai
- [ ] `npm install` selesai
- [ ] File `.env` sudah dibuat dan dikonfigurasi
- [ ] `php artisan key:generate` sudah dijalankan
- [ ] **`php artisan storage:link` sudah dijalankan** ⚠️ PENTING
- [ ] Database sudah dibuat dan dikonfigurasi
- [ ] `php artisan migrate` sudah dijalankan
- [ ] `npm run build` sudah dijalankan
- [ ] Server bisa dijalankan dengan `php artisan serve`

