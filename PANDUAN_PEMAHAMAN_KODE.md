# 📚 Panduan Pemahaman Kode Project MentoraKelSatu

## 🎯 Gambaran Umum Project

Project ini adalah **platform marketplace untuk job/tugas** berbasis Laravel 12. Sistem ini memungkinkan:
- **Mahasiswa/Dosen** untuk membuat job dan mencari pekerja
- **Mahasiswa/Dosen** untuk mengambil job dan mendapatkan bayaran
- **Admin** untuk mengelola sistem, topup, laporan, dan user

---

## 🏗️ Arsitektur Aplikasi

Project ini menggunakan **MVC (Model-View-Controller) Pattern**:

```
┌─────────────────────────────────────────┐
│         ROUTES (web.php)                │  ← Menentukan URL dan Controller
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│      CONTROLLERS (app/Http/Controllers) │  ← Logika bisnis & validasi
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│         MODELS (app/Models)             │  ← Interaksi dengan database
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│         VIEWS (resources/views)         │  ← Tampilan HTML untuk user
└─────────────────────────────────────────┘
```

---

## 📊 Struktur Database

### Tabel Utama:

1. **users** - Menyimpan data user (mahasiswa, dosen, admin)
   - `user_id` (primary key)
   - `name`, `email`, `password`
   - `role` (mahasiswa/dosen/admin)
   - `balance` (saldo)
   - `avg_rating` (rating rata-rata)
   - `is_suspended` (status suspend)

2. **jobs** - Menyimpan data job/tugas
   - `job_id` (primary key)
   - `title`, `description`
   - `created_by` (user yang membuat job)
   - `assigned_to` (user yang mengambil job)
   - `deadline` (batas waktu)
   - `status` (belum_diambil/on_progress/selesai/kadaluarsa)
   - `price` (harga job)

3. **categories** - Kategori job
4. **job_categories** - Relasi many-to-many antara job dan kategori
5. **feedbacks** - Rating dan komentar setelah job selesai
6. **topups** - Permintaan topup saldo
7. **reports** - Laporan dari user
8. **payments** - Riwayat pembayaran

---

## 🔑 Konsep Penting dalam Kode

### 1. **Eloquent Relationships** (Relasi Model)

Laravel menggunakan Eloquent ORM untuk menghubungkan tabel. Contoh di `User.php`:

```php
// User memiliki banyak job yang dibuatnya
public function jobsCreated() {
    return $this->hasMany(Job::class, 'created_by', 'user_id');
}

// User memiliki banyak job yang diambilnya
public function jobsAssigned() {
    return $this->hasMany(Job::class, 'assigned_to', 'user_id');
}
```

**Cara kerja:**
- `hasMany` = satu user bisa punya banyak job
- `belongsTo` = satu job punya satu creator/assignee
- `belongsToMany` = relasi many-to-many (job bisa punya banyak kategori)

### 2. **Middleware** (Filter Request)

Middleware adalah "penjaga" yang memfilter request sebelum sampai ke controller:

```php
// routes/web.php
Route::middleware('auth')->group(function () {
    // Hanya user yang sudah login yang bisa akses
});

Route::middleware('admin')->group(function () {
    // Hanya admin yang bisa akses
});
```

### 3. **Policy** (Authorization)

Policy menentukan siapa yang boleh melakukan aksi tertentu. Contoh di `JobPolicy.php`:

```php
public function update(User $user, Job $job): bool {
    // Admin bisa update semua job
    if ($user->role === 'admin') {
        return true;
    }
    
    // User hanya bisa update job miliknya yang masih pending
    return $job->created_by === $user->user_id
        && $job->status === Job::STATUS_PENDING;
}
```

### 4. **Service Class** (Logika Bisnis Kompleks)

Service class memisahkan logika bisnis yang kompleks dari controller. Contoh `JobExpirationService.php`:

```php
public function expireJobs(): int {
    // Cari job yang sudah melewati deadline
    $expiredJobs = Job::where('deadline', '<', now())
        ->whereIn('status', [Job::STATUS_PENDING, Job::STATUS_PROGRESS])
        ->get();
    
    // Update status dan kembalikan saldo
    foreach ($expiredJobs as $job) {
        $job->update(['status' => Job::STATUS_EXPIRED]);
        $job->creator->increment('balance', $job->price);
    }
}
```

---

## 🔄 Alur Kerja Aplikasi

### Contoh 1: User Membuat Job

```
1. User klik "Buat Job Baru"
   ↓
2. Route: GET /jobs/create
   ↓
3. JobController@create
   - Cek apakah user adalah admin (admin tidak bisa buat job)
   - Ambil semua kategori dari database
   - Tampilkan form create job
   ↓
4. User isi form dan submit
   ↓
5. Route: POST /jobs
   ↓
6. JobController@store
   - Validasi input (title, description, price, deadline)
   - Cek saldo user cukup
   - Gunakan DB::transaction (semua operasi harus berhasil atau rollback)
   - Buat job baru
   - Sync kategori ke job
   - Potong saldo user
   ↓
7. Redirect ke halaman jobs dengan pesan sukses
```

**Kode penting di `JobController@store`:**

```php
DB::transaction(function () use ($request, $data, $user) {
    // Semua operasi ini harus berhasil, kalau ada yang gagal, semua di-rollback
    $job = Job::create([...]);
    $job->categories()->sync($data['categories']);
    $user->decrement('balance', $data['price']);
});
```

### Contoh 2: User Mengambil Job

```
1. User klik "Ambil Job" di job yang tersedia
   ↓
2. Route: POST /jobs/{job}/take
   ↓
3. JobAssignmentController@take
   - Cek user bukan admin
   - Cek job masih PENDING
   - Cek user bukan creator job tersebut
   - Cek user belum mengambil 2 job aktif (batas maksimal)
   - Update job: assigned_to = user_id, status = PROGRESS
   ↓
4. Redirect dengan pesan sukses
```

**Kode penting di `JobAssignmentController@take`:**

```php
$activeAssignments = $user->jobsAssigned()
    ->where('status', Job::STATUS_PROGRESS)
    ->count();

if ($activeAssignments >= 2) {
    return back()->withErrors(['job' => 'Maksimal 2 job sekaligus']);
}
```

### Contoh 3: Job Selesai dan Pembayaran

```
1. Worker klik "Tandai Selesai"
   ↓
2. Route: POST /jobs/{job}/complete
   ↓
3. JobAssignmentController@complete
   - Cek user adalah assignee atau admin
   - Cek job status adalah PROGRESS
   - Gunakan DB::transaction
   - Update status job menjadi DONE
   - Transfer saldo ke worker (increment balance)
   ↓
4. Redirect dengan pesan sukses
```

**Kode penting di `JobAssignmentController@complete`:**

```php
DB::transaction(function () use ($job) {
    $job->update(['status' => Job::STATUS_DONE]);
    
    if ($job->assignee) {
        $job->assignee->increment('balance', $job->price);
    }
});
```

---

## 📁 Penjelasan File-File Penting

### 1. **routes/web.php**
File ini menentukan semua URL dan routing aplikasi. Setiap URL dihubungkan ke method di Controller tertentu.

**Contoh:**
```php
Route::resource('jobs', JobController::class)->except(['show']);
// Ini otomatis membuat route:
// GET    /jobs          → JobController@index
// GET    /jobs/create   → JobController@create
// POST   /jobs          → JobController@store
// GET    /jobs/{job}/edit → JobController@edit
// PUT    /jobs/{job}    → JobController@update
// DELETE /jobs/{job}    → JobController@destroy
```

### 2. **app/Models/User.php**
Model untuk tabel `users`. Berisi:
- Definisi kolom yang bisa diisi (`$fillable`)
- Definisi tipe data (`$casts`)
- Relasi dengan model lain (jobs, topups, feedbacks, reports)
- Method helper seperti `updateAvgRating()`

### 3. **app/Models/Job.php**
Model untuk tabel `jobs`. Berisi:
- Konstanta status: `STATUS_PENDING`, `STATUS_PROGRESS`, `STATUS_DONE`, `STATUS_EXPIRED`
- Relasi dengan User (creator, assignee) dan Category
- Scope query: `scopeAvailable()` untuk filter job yang masih tersedia

### 4. **app/Http/Controllers/JobController.php**
Controller utama untuk mengelola job. Method penting:
- `index()` - Menampilkan daftar job (beda untuk admin vs user)
- `create()` - Form membuat job baru
- `store()` - Simpan job baru (dengan validasi saldo)
- `edit()` - Form edit job
- `update()` - Update job (handle perubahan harga)
- `destroy()` - Hapus job (kembalikan saldo jika belum selesai)

### 5. **app/Http/Controllers/AuthController.php**
Controller untuk autentikasi:
- `login()` - Proses login (cek suspended)
- `register()` - Daftar user baru (generate recovery code)
- `resetPassword()` - Reset password dengan recovery code

### 6. **app/Services/JobExpirationService.php**
Service untuk handle job yang expired:
- `expireJobs()` - Otomatis expire job yang melewati deadline
- Dipanggil setiap kali ada yang akses halaman jobs
- Kembalikan saldo ke creator

### 7. **app/Policies/JobPolicy.php**
Authorization policy untuk job:
- `update()` - Siapa yang boleh edit job
- `delete()` - Siapa yang boleh hapus job

---

## 🔐 Sistem Keamanan

### 1. **Authentication (Login/Logout)**
- User harus login untuk akses fitur utama
- Password di-hash dengan bcrypt
- Session management

### 2. **Authorization (Hak Akses)**
- **Admin**: Bisa akses semua fitur admin
- **Mahasiswa/Dosen**: Tidak bisa akses fitur admin
- **Policy**: Cek ownership sebelum edit/delete

### 3. **Validasi Input**
Setiap form input divalidasi:
```php
$request->validate([
    'title' => ['required', 'string', 'max:255'],
    'price' => ['required', 'numeric', 'min:0'],
]);
```

### 4. **Database Transaction**
Operasi penting menggunakan transaction untuk menjaga konsistensi:
```php
DB::transaction(function () {
    // Jika salah satu gagal, semua di-rollback
});
```

---

## 💰 Sistem Saldo & Pembayaran

### Flow Saldo:

1. **User Topup** → Admin approve → Saldo bertambah
2. **User Buat Job** → Saldo dipotong (hold)
3. **Job Selesai** → Saldo ditransfer ke worker
4. **Job Dihapus (belum selesai)** → Saldo dikembalikan
5. **Job Expired** → Saldo dikembalikan ke creator

### Kode Penting:

```php
// Potong saldo
$user->decrement('balance', $amount);

// Tambah saldo
$user->increment('balance', $amount);

// Cek saldo cukup
if ($user->balance < $amount) {
    return back()->withErrors(['price' => 'Saldo tidak cukup']);
}
```

---

## 🎨 Frontend (Blade Templates)

### Struktur View:

```
resources/views/
├── layouts/
│   ├── app.blade.php      ← Layout utama
│   └── partials/
│       └── header.blade.php
├── jobs/
│   ├── index.blade.php    ← Daftar job
│   ├── create.blade.php   ← Form buat job
│   └── edit.blade.php     ← Form edit job
├── dashboard.blade.php     ← Halaman dashboard
└── auth/
    ├── login.blade.php
    └── register.blade.php
```

### Blade Syntax:

```blade
{{-- Tampilkan data --}}
{{ $user->name }}

{{-- Conditional --}}
@if (auth()->user()->role === 'admin')
    {{-- Admin content --}}
@else
    {{-- User content --}}
@endif

{{-- Loop --}}
@foreach ($jobs as $job)
    {{ $job->title }}
@endforeach

{{-- Include partial --}}
@include('layouts.partials.header')
```

---

## 🚀 Tips Memahami Kode Lebih Lanjut

### 1. **Mulai dari Routes**
Lihat `routes/web.php` untuk memahami semua fitur yang ada.

### 2. **Ikuti Flow Request**
Pilih satu fitur (misal: buat job), lalu ikuti alurnya:
- Route → Controller → Model → View

### 3. **Baca Model Relationships**
Pahami relasi antar model untuk tahu bagaimana data saling terhubung.

### 4. **Gunakan Debug**
Tambahkan `dd($variable)` untuk melihat isi variable di tengah-tengah eksekusi.

### 5. **Baca Dokumentasi Laravel**
- [Laravel Documentation](https://laravel.com/docs)
- Konsep penting: Eloquent, Routing, Middleware, Validation

---

## 📝 Kesimpulan

Project ini adalah **marketplace job platform** dengan fitur:
- ✅ Autentikasi & Authorization
- ✅ CRUD Job dengan kategori
- ✅ Sistem saldo & pembayaran
- ✅ Topup dengan approval admin
- ✅ Rating & feedback
- ✅ Laporan (reporting)
- ✅ Auto-expire job yang melewati deadline

**Pola yang digunakan:**
- MVC Architecture
- Eloquent ORM untuk database
- Policy untuk authorization
- Service class untuk logika kompleks
- Database transaction untuk konsistensi data

---

## 🔍 Latihan untuk Memahami

1. **Trace flow "User mengambil job"** dari klik button sampai database update
2. **Cari di mana saldo user dikembalikan** ketika job dihapus
3. **Pahami bagaimana rating rata-rata dihitung** (lihat method `updateAvgRating()`)
4. **Lihat bagaimana admin berbeda dari user biasa** di setiap controller

---

**Selamat belajar! 🎓**

Jika ada pertanyaan tentang bagian tertentu, fokus pada bagian tersebut dan trace kodenya step by step.

