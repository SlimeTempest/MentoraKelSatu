# Setup Login dengan Google OAuth - Step by Step

## 📋 Daftar Isi

1. [Overview](#overview)
2. [Step-by-Step Setup](#step-by-step-setup)
3. [Penjelasan Kolom Database](#penjelasan-kolom-database)
4. [Testing](#testing)

---

## 🎯 Overview

Fitur login dengan Google menggunakan Laravel Socialite untuk mengintegrasikan Google OAuth 2.0. User dapat login menggunakan akun Google mereka tanpa perlu membuat password.

---

## 📝 Step-by-Step Setup

### **Step 1: Install Laravel Socialite**

```bash
composer require laravel/socialite
```

✅ Package sudah diinstall dan siap digunakan.

---

### **Step 2: Setup Google OAuth Credentials**

#### 2.1. Buat Google OAuth Project

1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Buat project baru atau pilih project yang sudah ada
3. Aktifkan **Google+ API** (jika belum aktif)

#### 2.2. Buat OAuth 2.0 Credentials

1. Masuk ke **APIs & Services** > **Credentials**
2. Klik **Create Credentials** > **OAuth client ID**
3. Pilih **Web application**
4. Isi:
    - **Name**: Nama aplikasi Anda (contoh: "MentoraKelSatu")
    - **Authorized JavaScript origins**:
        ```
        http://localhost:8000
        http://127.0.0.1:8000
        ```
    - **Authorized redirect URIs**:
        ```
        http://localhost:8000/auth/google/callback
        http://127.0.0.1:8000/auth/google/callback
        ```
        ⚠️ **Untuk production**, ganti dengan domain Anda:
        ```
        https://yourdomain.com/auth/google/callback
        ```
5. Klik **Create**
6. **Copy Client ID dan Client Secret**

#### 2.3. Tambahkan ke File .env

Tambahkan ke file `.env` Anda:

```env
GOOGLE_CLIENT_ID=your-client-id-here
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

⚠️ **Untuk production**, ganti `GOOGLE_REDIRECT_URI` dengan domain production Anda.

---

### **Step 3: Update Database**

#### 3.1. Jalankan Migration

```bash
php artisan migrate
```

Migration ini akan:

-   Menambahkan kolom `google_id` ke tabel `users`
-   Mengubah kolom `password` menjadi nullable (karena user Google tidak punya password)

---

### **Step 4: Konfigurasi Services**

File `config/services.php` sudah diupdate dengan konfigurasi Google:

```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/auth/google/callback'),
],
```

---

### **Step 5: Update User Model**

File `app/Models/User.php` sudah diupdate dengan menambahkan `google_id` ke `$fillable`:

```php
protected $fillable = [
    // ... kolom lainnya
    'google_id',
    // ...
];
```

---

### **Step 6: Update AuthController**

Method baru sudah ditambahkan:

-   `redirectToGoogle()`: Redirect user ke Google OAuth
-   `handleGoogleCallback()`: Handle callback dari Google

---

### **Step 7: Update Routes**

Routes sudah ditambahkan di `routes/web.php`:

```php
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
```

---

### **Step 8: Update Login View**

Button "Masuk dengan Google" sudah ditambahkan di `resources/views/auth/login.blade.php` dengan styling dark theme yang konsisten.

---

## 🗄️ Penjelasan Kolom Database

### **Kolom yang DIISI saat Login dengan Google:**

| Kolom           | Nilai        | Keterangan                                            |
| --------------- | ------------ | ----------------------------------------------------- |
| `google_id`     | ✅ **Diisi** | ID unik dari Google (contoh: "123456789012345678901") |
| `name`          | ✅ **Diisi** | Nama dari Google account                              |
| `email`         | ✅ **Diisi** | Email dari Google account (harus unique)              |
| `photo`         | ✅ **Diisi** | URL foto profil dari Google (jika tersedia)           |
| `role`          | ✅ **Diisi** | Default: `'mahasiswa'`                                |
| `recovery_code` | ✅ **Diisi** | Auto-generate 8 karakter alphanumeric                 |
| `balance`       | ✅ **Diisi** | Default: `0`                                          |
| `avg_rating`    | ✅ **Diisi** | Default: `0`                                          |
| `is_suspended`  | ✅ **Diisi** | Default: `false`                                      |
| `created_at`    | ✅ **Diisi** | Timestamp saat registrasi                             |
| `updated_at`    | ✅ **Diisi** | Timestamp saat update                                 |

### **Kolom yang TIDAK DIISI (NULL) saat Login dengan Google:**

| Kolom            | Nilai       | Keterangan                                                       |
| ---------------- | ----------- | ---------------------------------------------------------------- |
| `password`       | ❌ **NULL** | User Google tidak punya password di sistem                       |
| `phone`          | ❌ **NULL** | Tidak diambil dari Google (optional, bisa diisi manual nanti)    |
| `remember_token` | ❌ **NULL** | Akan diisi otomatis oleh Laravel jika user memilih "Remember Me" |

### **Skenario Login dengan Google:**

#### **Skenario 1: User Baru (Email belum terdaftar)**

-   ✅ Buat user baru dengan data dari Google
-   ✅ `google_id` diisi
-   ✅ `password` = NULL
-   ✅ `role` = 'mahasiswa' (default)

#### **Skenario 2: User Sudah Ada (Email sudah terdaftar via registrasi biasa)**

-   ✅ Link Google account ke user yang sudah ada
-   ✅ Update `google_id` dan `photo` (jika ada)
-   ✅ `password` tetap ada (user bisa login dengan password atau Google)

#### **Skenario 3: User Sudah Login dengan Google Sebelumnya**

-   ✅ Login langsung menggunakan `google_id`
-   ✅ Update `photo` jika ada perubahan

---

## 🧪 Testing

### **Cara Test:**

1. **Pastikan .env sudah diisi dengan credentials Google**

    ```env
    GOOGLE_CLIENT_ID=your-client-id
    GOOGLE_CLIENT_SECRET=your-client-secret
    GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
    ```

2. **Jalankan migration (jika belum)**

    ```bash
    php artisan migrate
    ```

3. **Akses halaman login**

    ```
    http://localhost:8000/login
    ```

4. **Klik "Masuk dengan Google"**

    - Akan redirect ke Google OAuth
    - Pilih akun Google
    - Authorize aplikasi
    - Akan redirect kembali ke aplikasi

5. **Cek Database**
    - User baru akan dibuat dengan `google_id` terisi
    - `password` akan NULL untuk user Google

---

## ⚠️ Catatan Penting

1. **Password NULL untuk Google Users**

    - User yang login via Google tidak bisa login dengan password biasa
    - Mereka harus selalu login via Google

2. **Email Harus Unique**

    - Jika email sudah terdaftar, Google account akan di-link ke user yang sudah ada
    - User bisa login dengan password ATAU Google

3. **Role Default**

    - User baru dari Google akan mendapat role `'mahasiswa'` secara default
    - Admin bisa mengubah role jika diperlukan

4. **Recovery Code**

    - Recovery code tetap di-generate untuk user Google
    - Bisa digunakan untuk reset password jika user ingin menambahkan password

5. **Production Setup**
    - Pastikan `GOOGLE_REDIRECT_URI` di `.env` sesuai dengan domain production
    - Update **Authorized redirect URIs** di Google Cloud Console dengan domain production

---

## 🔧 Troubleshooting

### **Error: "Invalid credentials"**

-   Pastikan `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET` benar
-   Pastikan redirect URI di Google Console sesuai dengan di `.env`

### **Error: "Redirect URI mismatch"**

-   Pastikan redirect URI di Google Console sama persis dengan di `.env`
-   Pastikan tidak ada trailing slash atau perbedaan http/https

### **User tidak terbuat**

-   Cek log Laravel untuk error detail
-   Pastikan migration sudah dijalankan
-   Pastikan kolom `google_id` sudah ada di tabel `users`

---

## ✅ Checklist Setup

-   [x] Install Laravel Socialite
-   [x] Buat migration untuk `google_id`
-   [x] Update `config/services.php`
-   [x] Update `User` model
-   [x] Tambahkan method di `AuthController`
-   [x] Update routes
-   [x] Update login view dengan button Google
-   [ ] Setup Google OAuth credentials di Google Cloud Console
-   [ ] Tambahkan credentials ke `.env`
-   [ ] Jalankan migration
-   [ ] Test login dengan Google

---

**Selamat! Fitur login dengan Google sudah siap digunakan! 🎉**
