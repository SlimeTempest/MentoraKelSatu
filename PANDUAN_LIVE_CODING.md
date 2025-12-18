# 📚 Panduan Live Coding - MentoraKelSatu

Dokumentasi lengkap untuk melakukan perubahan, penambahan, dan penghapusan fitur pada project Laravel ini. **Dibuat khusus untuk persiapan live coding tanpa akses AI**.

> 💡 **Tips untuk Pemula:** Dokumentasi ini dibuat dengan penjelasan detail step-by-step. Baca dengan teliti setiap langkah, dan jangan panik jika tidak langsung paham. Latihan adalah kunci!

---

## CARA DIV CENTER

<div class="flex justify-center">

## CARA 2 x 2 antara 2 ni

<div class="grid gap-6 grid-cols-1 sm:grid-cols-2 max-w-2xl w-full">
<div class="rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700">

## 📑 Daftar Isi

1. [📋 Analisa Project Ini](#-analisa-project-ini)
2. [🔍 Tambah Fitur Filter](#1-tambah-fitur-filter)
3. [📄 Tambah Pagination](#2-tambah-pagination)
4. [🗄️ Tambah Kolom Database](#3-tambah-kolom-database)
5. [🎨 Ubah Style/Tampilan](#4-ubah-styletampilan)
6. [⚙️ Perintah Artisan Laravel](#5-perintah-artisan-laravel)
7. [💪 List Latihan](#-list-latihan)

---

## 📋 Analisa Project Ini

### Apa itu Project Ini?

Project ini adalah aplikasi **freelance marketplace** bernama "MentoraKelSatu" yang menghubungkan:

-   **Mahasiswa/Dosen** (yang membuat job/tugas)
-   **Freelancer** (yang mengambil dan mengerjakan job)

### 🎯 Quick Start untuk Pemula

**Jika kamu pemula dan bingung mulai dari mana:**

1. **Pahami dulu struktur project:**

    - `app/Http/Controllers/` → Logika aplikasi
    - `resources/views/` → Tampilan HTML
    - `database/migrations/` → Struktur database
    - `routes/web.php` → Routing (URL ke Controller)

2. **Baca contoh yang sudah ada:**

    - Filter: `app/Http/Controllers/AdminUserController.php` (baris 11-43)
    - Pagination: `app/Http/Controllers/JobController.php` (baris 24, 38-40)
    - Form: `resources/views/jobs/partials/form-fields.blade.php`

3. **Mulai latihan dari yang mudah:**

    - Latihan 1: Tambah filter search
    - Latihan 2: Ubah warna button
    - Latihan 4: Ubah jumlah pagination

4. **Gunakan dokumentasi ini sebagai panduan step-by-step**

### Struktur Database Utama:

1. **users** - Data user (mahasiswa, dosen, admin)

    - Kolom penting: `user_id`, `name`, `email`, `role`, `balance`, `avg_rating`

2. **jobs** - Data job/tugas

    - Kolom penting: `job_id`, `title`, `description`, `created_by`, `assigned_to`, `status`, `price`, `deadline`

3. **categories** - Kategori job (misal: Web Development, Design, dll)

4. **feedbacks** - Rating dan komentar setelah job selesai

5. **topups** - Permintaan topup saldo (user request → admin approve)

6. **reports** - Laporan masalah dari user

### Controller yang Ada di Project:

-   **JobController** - CRUD job (create, read, update, delete job)
-   **AuthController** - Login, register, forgot password
-   **ProfileController** - Lihat & edit profile user
-   **AdminUserController** - Admin kelola user (sudah ada filter!)
-   **AdminCategoryController** - Admin kelola kategori (sudah ada search!)
-   **TopupController** - User request topup
-   **FeedbackController** - User berikan rating/feedback
-   **ReportController** - User membuat laporan

### Teknologi yang Digunakan:

-   **Backend:** Laravel (PHP framework)
-   **Frontend:** Blade Templates (PHP templating) + Tailwind CSS
-   **Database:** SQLite (atau bisa MySQL/PostgreSQL)

---

## 1. Tambah Fitur Filter

### 🤔 Apa itu Filter?

Filter adalah fitur untuk **menyaring/memfilter data** berdasarkan kriteria tertentu.

**Contoh sederhana:**

-   Di halaman jobs, user ingin melihat hanya job dengan status "Belum Diambil"
-   Di halaman users, admin ingin melihat hanya user dengan role "Mahasiswa"
-   Di halaman categories, admin ingin mencari kategori dengan nama "Web"

### 💡 Konsep Dasar Filter:

1. **User pilih filter** di form (dropdown, input text, dll)
2. **Form dikirim** ke controller (method GET)
3. **Controller cek filter** dari request
4. **Query database** disesuaikan dengan filter
5. **Data yang sudah difilter** ditampilkan

### Langkah-langkah Implementasi (Step-by-Step untuk Pemula)

### Langkah-langkah Implementasi

#### **Step 1: Modifikasi Controller**

**📍 Lokasi:** Buka file controller yang ingin ditambahkan filter (misal: `app/Http/Controllers/JobController.php`)

**🎯 Tujuan:** Menambahkan logika untuk membaca filter dari URL dan menyaring data

**Penjelasan Kode untuk Pemula:**

```php
// app/Http/Controllers/JobController.php
public function index(Request $request)
{
    // 1. Buat query builder (belum dijalankan ke database)
    $query = Job::query();

    // 2. Filter berdasarkan status
    // $request->filled('status') = cek apakah ada parameter 'status' di URL
    // $request->get('status') = ambil nilai parameter 'status'
    // !== 'all' = jangan filter jika user pilih "Semua"
    if ($request->filled('status') && $request->get('status') !== 'all') {
        // Tambahkan kondisi WHERE ke query
        $query->where('status', $request->get('status'));
    }

    // 3. Filter berdasarkan kategori (relasi many-to-many)
    // whereHas = filter berdasarkan relasi
    if ($request->filled('category_id')) {
        $query->whereHas('categories', function($q) use ($request) {
            $q->where('categories.category_id', $request->get('category_id'));
        });
    }

    // 4. Filter berdasarkan pencarian (search)
    // like = mencari yang mengandung teks tertentu
    // %{$search}% = mengandung teks di bagian mana saja
    if ($request->filled('search')) {
        $search = $request->get('search');
        $query->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
    }

    // 5. Eksekusi query dan dapatkan hasil (dengan pagination)
    $jobs = $query->orderBy('created_at', 'desc')->paginate(10);

    // 6. Kirim data ke view
    // Kirim juga nilai filter agar form tetap terisi setelah submit
    return view('jobs.index', [
        'jobs' => $jobs,
        'statusFilter' => $request->get('status', 'all'), // 'all' adalah default
        'categoryFilter' => $request->get('category_id', ''),
        'search' => $request->get('search', ''),
    ]);
}
```

**📝 Penjelasan Istilah untuk Pemula:**

-   `$request->filled('field')` → Cek apakah ada input dengan nama "field" DAN tidak kosong
-   `$request->get('field', 'default')` → Ambil nilai input "field", jika tidak ada gunakan "default"
-   `$query->where(...)` → Tambahkan kondisi WHERE ke query SQL
-   `like "%text%"` → Mencari yang mengandung "text" di bagian mana saja
-   `whereHas(...)` → Filter berdasarkan relasi (untuk many-to-many)

**✅ Referensi Contoh di Project Ini:**

-   File: `app/Http/Controllers/AdminUserController.php` (baris 11-43)
-   Sudah ada contoh filter untuk `search`, `role`, dan `suspended` - **BISA DICOPY PASTE!**

#### **Step 2: Tambahkan Form Filter di View**

**📍 Lokasi:** Buka file view yang ingin ditambahkan filter (misal: `resources/views/jobs/index.blade.php`)

**🎯 Tujuan:** Membuat form HTML untuk user memilih filter, kemudian mengirim ke controller

**Penjelasan Kode untuk Pemula:**

```blade
{{-- resources/views/jobs/index.blade.php --}}
{{-- Tambahkan di bagian ATAS sebelum tabel/list data --}}

<div class="rounded-lg border border-gray-700 bg-gray-800 p-4 shadow-lg mb-6">
    {{-- Form dengan method GET (data dikirim via URL) --}}
    <form action="{{ route('jobs.index') }}" method="GET" class="space-y-4">
        <div class="grid gap-4 md:grid-cols-4">

            {{-- 1. Search Input (Text Box) --}}
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-400">Cari</label>
                <input
                    type="text"
                    name="search"  {{-- Nama ini akan menjadi parameter di URL: ?search=... --}}
                    value="{{ $search ?? '' }}"  {{-- Nilai dari controller, jika tidak ada pakai string kosong --}}
                    placeholder="Cari judul..."
                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                >
            </div>

            {{-- 2. Status Filter (Dropdown) --}}
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-400">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    {{-- Opsi "Semua" - tidak ada filter --}}
                    <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>
                        Semua
                    </option>
                    {{-- Opsi lainnya dengan nilai sesuai database --}}
                    <option value="belum_diambil" {{ ($statusFilter ?? '') === 'belum_diambil' ? 'selected' : '' }}>
                        Belum Diambil
                    </option>
                    <option value="on_progress" {{ ($statusFilter ?? '') === 'on_progress' ? 'selected' : '' }}>
                        On Progress
                    </option>
                    <option value="selesai" {{ ($statusFilter ?? '') === 'selesai' ? 'selected' : '' }}>
                        Selesai
                    </option>
                </select>
            </div>

            {{-- 3. Category Filter (Dropdown dengan Data Dinamis) --}}
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-400">Kategori</label>
                <select name="category_id" class="w-full rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Semua Kategori</option>
                    {{-- Loop semua kategori --}}
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}" {{ ($categoryFilter ?? '') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 4. Submit Button --}}
            <div class="flex items-end">
                <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-gray-50 hover:bg-blue-500 transition-all duration-200">
                    Filter
                </button>
            </div>
        </div>
    </form>
</div>
```

**📝 Penjelasan Istilah untuk Pemula:**

-   `method="GET"` → Data dikirim via URL (bisa dilihat di address bar browser)
-   `name="search"` → Nama parameter yang dikirim (di URL jadi: `?search=...`)
-   `value="{{ $search ?? '' }}"` → Nilai dari controller, jika tidak ada pakai `''` (string kosong)
-   `{{ ... ? 'selected' : '' }}` → Ternary operator: jika kondisi benar, tambahkan `selected`, jika tidak tambahkan `''`
-   `@foreach` → Loop untuk menampilkan opsi kategori secara dinamis
-   `{{ $category->category_id }}` → Tampilkan ID kategori sebagai value
-   `{{ $category->name }}` → Tampilkan nama kategori sebagai teks yang terlihat user

**⚠️ PENTING:**

-   Pastikan controller sudah mengirim variable `$search`, `$statusFilter`, `$categoryFilter`, dan `$categories` ke view
-   Jika belum ada, tambahkan di method `index()` controller

**✅ Contoh Real di Project:**

-   File: `resources/views/admin/users/index.blade.php` (baris 16-54)
-   Sudah ada contoh lengkap dengan form filter - **BISA DICOPY PASTE!**

**Tips:**

-   Gunakan `$request->filled('field')` untuk cek apakah field ada dan tidak kosong
-   Gunakan `$request->get('field', 'default')` untuk mendapatkan value dengan default value
-   Jangan lupa pass filter values ke view agar form tetap terisi setelah submit

#### **Step 3: Maintain Filter pada Pagination**

Agar filter tidak hilang saat pindah halaman, gunakan `appends()`:

```php
// Di view
{{ $jobs->appends(request()->query())->links() }}

// Atau di controller, bisa juga kirim query params ke view
$jobs = $query->paginate(10)->appends(request()->query());
```

---

## 2. Tambah Pagination

### 🤔 Apa itu Pagination?

Pagination adalah **membagi data menjadi beberapa halaman**.

**Contoh:**

-   Ada 100 data job
-   Dengan pagination 10 per halaman
-   Maka akan ada 10 halaman (halaman 1 = data 1-10, halaman 2 = data 11-20, dst)

**Kenapa perlu pagination?**

-   Performa lebih cepat (tidak load semua data sekaligus)
-   User experience lebih baik (tidak scroll panjang-panjang)
-   Menghemat resource server

### 💡 Konsep Dasar Pagination:

1. **Controller:** Gunakan `paginate(n)` bukan `get()` atau `all()`
2. **View:** Tampilkan links pagination dengan `{{ $data->links() }}`
3. **Laravel otomatis** membuat link halaman 1, 2, 3, dst

### Langkah-langkah Implementasi

#### **Step 1: Modifikasi Controller**

Ganti `get()`, `all()`, atau `latest()->get()` dengan `paginate(n)`.

**Sebelum (tanpa pagination):**

```php
$jobs = Job::latest()->get();
```

**Sesudah (dengan pagination):**

```php
$jobs = Job::latest()->paginate(10); // 10 data per halaman
```

**Contoh Lengkap:**

```php
public function index(Request $request)
{
    $jobs = Job::with(['creator', 'categories'])
        ->latest()
        ->paginate(15); // 15 item per halaman

    return view('jobs.index', compact('jobs'));
}
```

**Catatan:**

-   Angka di `paginate()` adalah jumlah data per halaman
-   Bisa juga gunakan `simplePaginate()` untuk pagination sederhana (tanpa nomor halaman)

#### **Step 2: Tampilkan Pagination di View**

Tambahkan links pagination di bagian bawah data.

**Contoh Dasar:**

```blade
{{-- resources/views/jobs/index.blade.php --}}
<div class="mt-6">
    {{ $jobs->links() }}
</div>
```

**Contoh dengan Custom View (sudah ada di project ini):**

```blade
{{-- resources/views/jobs/index.blade.php --}}
<div class="border-t border-gray-700 bg-gray-700/30 px-4 py-3">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        {{-- Info Jumlah Data --}}
        <div class="text-sm text-gray-400">
            Menampilkan
            <span class="font-semibold text-gray-300">{{ $jobs->firstItem() ?? 0 }}</span>
            sampai
            <span class="font-semibold text-gray-300">{{ $jobs->lastItem() ?? 0 }}</span>
            dari
            <span class="font-semibold text-gray-300">{{ $jobs->total() }}</span>
            data
        </div>

        {{-- Pagination Links --}}
        <div class="flex items-center gap-2">
            {{ $jobs->appends(request()->query())->links('pagination::default') }}
        </div>
    </div>
</div>
```

**Method yang Bisa Digunakan:**

-   `$jobs->links()` - Tampilkan pagination links
-   `$jobs->firstItem()` - Nomor item pertama di halaman ini
-   `$jobs->lastItem()` - Nomor item terakhir di halaman ini
-   `$jobs->total()` - Total semua data
-   `$jobs->currentPage()` - Halaman aktif sekarang
-   `$jobs->perPage()` - Jumlah item per halaman
-   `$jobs->hasMorePages()` - Cek apakah ada halaman selanjutnya
-   `$jobs->isEmpty()` - Cek apakah kosong

**Referensi Contoh di Project Ini:**

-   File: `resources/views/admin/users/index.blade.php` (baris 160-178)
-   File: `app/Http/Controllers/JobController.php` (baris 24, 38-40)
-   File: `resources/views/vendor/pagination/default.blade.php` (custom pagination view)

---

## 3. Tambah Kolom Database

### 🤔 Apa itu Migration?

Migration adalah **cara Laravel untuk mengubah struktur database** (tambah kolom, hapus kolom, buat tabel, dll).

**Kenapa pakai migration?**

-   Bisa di-track (ada history perubahan)
-   Bisa di-rollback (undo perubahan)
-   Konsisten di semua environment (development, production)
-   Lebih aman daripada edit database manual

### 💡 Konsep Dasar Tambah Kolom:

1. **Buat Migration** → File yang berisi perintah untuk tambah kolom
2. **Edit Migration** → Tuliskan kolom apa yang mau ditambah
3. **Jalankan Migration** → Laravel akan mengubah database
4. **Update Model** → Tambah kolom ke `$fillable`
5. **Update Form** → Tambah input field untuk kolom baru
6. **Update Controller** → Tambah validasi dan simpan kolom baru
7. **Update View** → Tampilkan kolom baru

**⚠️ PENTING:** Urutan ini HARUS diikuti! Jangan skip langkah.

### Langkah-langkah Implementasi

#### **Step 1: Buat Migration File**

**📍 Lokasi:** Buka terminal/command prompt di folder project

**Perintah Dasar:**

```bash
php artisan make:migration add_nama_kolom_to_nama_tabel_table --table=nama_tabel
```

**Penjelasan Perintah:**

-   `php artisan make:migration` → Perintah untuk buat file migration
-   `add_nama_kolom_to_nama_tabel_table` → Nama file migration (ganti dengan nama yang sesuai)
-   `--table=nama_tabel` → Nama tabel yang akan diubah (misal: users, jobs, categories)

**Contoh Real:**

```bash
# Tambah kolom 'phone' ke tabel 'users'
php artisan make:migration add_phone_to_users_table --table=users

# Tambah kolom 'location' ke tabel 'jobs'
php artisan make:migration add_location_to_jobs_table --table=jobs

# Tambah kolom 'website' ke tabel 'users'
php artisan make:migration add_website_to_users_table --table=users
```

**File akan dibuat di:**

-   `database/migrations/YYYY_MM_DD_HHMMSS_add_phone_to_users_table.php`
-   Format nama: `YYYY_MM_DD_HHMMSS_nama_migration.php`

**✅ Setelah perintah dijalankan, akan muncul pesan:**

```
Created Migration: YYYY_MM_DD_HHMMSS_add_phone_to_users_table
```

#### **Step 2: Edit File Migration**

**📍 Lokasi:** Buka file migration yang baru dibuat (misal: `database/migrations/YYYY_MM_DD_HHMMSS_add_phone_to_users_table.php`)

**🎯 Tujuan:** Tuliskan kolom apa yang mau ditambah ke database

**Penjelasan Kode untuk Pemula:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Method up() = dijalankan saat migration di-run
    // Isinya: perintah untuk TAMBAH kolom
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom 'phone' dengan tipe string, panjang 20 karakter
            // nullable() = boleh kosong (NULL)
            // after('email') = posisikan setelah kolom 'email'
            $table->string('phone', 20)->nullable()->after('email');

            // CATATAN: Jika tidak pakai after(), kolom akan ditambah di akhir tabel
        });
    }

    // Method down() = dijalankan saat migration di-rollback (undo)
    // Isinya: perintah untuk HAPUS kolom (kebalikan dari up())
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom 'phone' jika migration di-rollback
            $table->dropColumn('phone');
        });
    }
};
```

**📝 Penjelasan Istilah untuk Pemula:**

-   `up()` → Method yang dijalankan saat `php artisan migrate` (TAMBAH kolom)
-   `down()` → Method yang dijalankan saat `php artisan migrate:rollback` (HAPUS kolom)
-   `Schema::table('users', ...)` → Edit tabel 'users'
-   `$table->string('phone', 20)` → Tambah kolom dengan tipe string, max 20 karakter
-   `nullable()` → Kolom boleh kosong (NULL), jika tidak pakai ini kolom WAJIB diisi
-   `after('email')` → Posisi kolom setelah kolom 'email' (opsional)
-   `dropColumn('phone')` → Hapus kolom 'phone'

**⚠️ PENTING:**

-   Selalu isi method `down()` untuk bisa rollback
-   Nama kolom di `up()` dan `down()` harus SAMA

**Jenis-jenis Kolom yang Sering Dipakai:**

```php
// 1. STRING - Untuk teks pendek (nama, email, dll)
$table->string('name', 255);        // String max 255 karakter (default)
$table->string('phone', 20);        // String max 20 karakter

// 2. TEXT - Untuk teks panjang (deskripsi, alamat lengkap, dll)
$table->text('description');        // Text panjang (tidak ada batas max)

// 3. INTEGER - Untuk angka bulat (umur, jumlah, dll)
$table->integer('age');             // Integer (biasa)
$table->bigInteger('user_id');      // Integer besar (untuk ID yang panjang)

// 4. DECIMAL - Untuk angka desimal (harga, rating, dll)
$table->decimal('price', 10, 2);    // Decimal: 10 digit total, 2 digit di belakang koma
                                     // Contoh: 12345678.90
$table->float('rating');            // Float (angka dengan desimal)

// 5. BOOLEAN - Untuk true/false (aktif/tidak aktif, dll)
$table->boolean('is_active');       // Boolean: true atau false

// 6. DATE & TIME
$table->date('birth_date');         // Date saja (YYYY-MM-DD)
$table->dateTime('published_at');   // Date + Time (YYYY-MM-DD HH:MM:SS)
$table->timestamp('deleted_at');    // Timestamp (sama seperti dateTime)

// 7. ENUM - Untuk pilihan terbatas (status, role, dll)
$table->enum('status', ['active', 'inactive']);  // Hanya bisa: 'active' atau 'inactive'

// 8. JSON - Untuk data JSON (array/object)
$table->json('metadata');           // Menyimpan data JSON
```

**Modifier yang Bisa Ditambahkan (Opsional):**

```php
// nullable() - Kolom boleh kosong (NULL)
$table->string('phone')->nullable();

// default('value') - Nilai default jika tidak diisi
$table->boolean('is_active')->default(true);

// after('column_name') - Posisi kolom setelah kolom tertentu
$table->string('phone')->after('email');

// unique() - Nilai harus unik (tidak boleh ada yang sama)
$table->string('email')->unique();

// index() - Tambah index untuk mempercepat pencarian (untuk kolom yang sering di-query)
$table->string('name')->index();
```

**💡 Tips untuk Pemula:**

-   Gunakan `string()` untuk teks pendek (nama, email, phone)
-   Gunakan `text()` untuk teks panjang (deskripsi, alamat lengkap)
-   Gunakan `decimal()` untuk uang/harga
-   Gunakan `boolean()` untuk status aktif/tidak aktif
-   Gunakan `date()` atau `dateTime()` untuk tanggal/waktu
-   Jangan lupa pakai `nullable()` jika kolom tidak wajib diisi

#### **Step 3: Jalankan Migration**

**📍 Lokasi:** Buka terminal/command prompt di folder project

**Perintah:**

```bash
php artisan migrate
```

**Apa yang terjadi?**

-   Laravel akan membaca file migration yang belum dijalankan
-   Laravel akan menjalankan method `up()` dari migration tersebut
-   Database akan diubah (kolom baru ditambahkan)

**✅ Jika berhasil, akan muncul pesan:**

```
Migrating: YYYY_MM_DD_HHMMSS_add_phone_to_users_table
Migrated:  YYYY_MM_DD_HHMMSS_add_phone_to_users_table (XX.XXms)
```

**❌ Jika ada error:**

-   Baca error message dengan teliti
-   Cek apakah syntax migration sudah benar
-   Cek apakah nama tabel sudah benar
-   Cek apakah kolom yang mau ditambah sudah ada (jangan duplikat)

**🔄 Perintah Rollback (Undo Migration):**

```bash
# Rollback 1 batch terakhir (undo 1 migration terakhir)
php artisan migrate:rollback

# Rollback 3 batch terakhir (undo 3 migration terakhir)
php artisan migrate:rollback --step=3

# HAPUS SEMUA TABEL & jalankan ulang semua migration (HATI-HATI! SEMUA DATA AKAN HILANG!)
php artisan migrate:fresh
```

**⚠️ PENTING:**

-   `migrate:fresh` akan **HAPUS SEMUA DATA** di database
-   Hanya gunakan saat development/testing
-   JANGAN gunakan di production!

**💡 Tips:**

-   Selalu backup database sebelum migrate di production
-   Test migration di development dulu sebelum production

#### **Step 4: Update Model**

**📍 Lokasi:** Buka file model yang sesuai (misal: `app/Models/User.php`)

**🎯 Tujuan:** Memberitahu Laravel bahwa kolom baru ini bisa diisi (mass assignment)

**Penjelasan untuk Pemula:**

```php
// app/Models/User.php
class User extends Model
{
    // $fillable = daftar kolom yang BISA diisi secara mass assignment
    // Mass assignment = mengisi banyak kolom sekaligus (contoh: User::create([...]))
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',  // ← TAMBAHKAN KOLOM BARU DI SINI
    ];

    // $casts = mengubah tipe data kolom saat diambil dari database
    // (opsional, hanya jika perlu casting khusus)
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone' => 'string', // opsional, tidak wajib
    ];
}
```

**📝 Penjelasan Istilah untuk Pemula:**

-   `$fillable` → Daftar kolom yang **BISA** diisi saat menggunakan `create()` atau `update()`
-   Mass Assignment → Mengisi banyak kolom sekaligus dalam satu perintah
-   `$casts` → Mengubah tipe data kolom (opsional, biasanya tidak perlu)

**⚠️ PENTING:**

-   Jika kolom tidak ada di `$fillable`, kolom tersebut **TIDAK BISA** diisi dengan `create()` atau `update()`
-   Ini adalah fitur keamanan Laravel untuk mencegah mass assignment vulnerability
-   Jika kolom ada di `$fillable`, artinya kolom tersebut "aman" untuk diisi user

**💡 Contoh:**

```php
// Jika 'phone' ada di $fillable, ini bisa jalan:
User::create([
    'name' => 'John',
    'email' => 'john@example.com',
    'phone' => '081234567890',  // ✅ BISA
]);

// Jika 'phone' TIDAK ada di $fillable, ini akan error:
User::create([
    'name' => 'John',
    'phone' => '081234567890',  // ❌ ERROR: phone tidak ada di $fillable
]);
```

#### **Step 5: Update Form (Create/Edit)**

Tambahkan input field di form create dan edit.

**Contoh di Form Create:**

```blade
{{-- resources/views/users/create.blade.php --}}
<div>
    <label for="phone" class="mb-2 block text-sm font-medium text-gray-300">
        Nomor Telepon
    </label>
    <input
        type="text"
        id="phone"
        name="phone"
        value="{{ old('phone') }}"
        class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
        placeholder="081234567890"
    >
    @error('phone')
        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>
```

**Contoh di Form Edit:**

```blade
{{-- resources/views/users/edit.blade.php --}}
<div>
    <label for="phone" class="mb-2 block text-sm font-medium text-gray-300">
        Nomor Telepon
    </label>
    <input
        type="text"
        id="phone"
        name="phone"
        value="{{ old('phone', $user->phone) }}"
        class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
        placeholder="081234567890"
    >
    @error('phone')
        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>
```

#### **Step 6: Update Controller (Store/Update)**

Tambahkan validasi dan simpan kolom baru di method `store()` dan `update()`.

**Contoh di Controller:**

```php
// app/Http/Controllers/UserController.php

public function store(Request $request)
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'unique:users,email'],
        'password' => ['required', 'confirmed', 'min:8'],
        'phone' => ['nullable', 'string', 'max:20'], // ← TAMBAHKAN VALIDASI
    ]);

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'phone' => $data['phone'] ?? null, // ← TAMBAHKAN INI
    ]);

    return redirect()->route('users.index')->with('status', 'User berhasil dibuat.');
}

public function update(Request $request, User $user)
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'unique:users,email,' . $user->user_id . ',user_id'],
        'phone' => ['nullable', 'string', 'max:20'], // ← TAMBAHKAN VALIDASI
    ]);

    $user->update([
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'] ?? null, // ← TAMBAHKAN INI
    ]);

    return redirect()->route('users.index')->with('status', 'User berhasil diperbarui.');
}
```

#### **Step 7: Tampilkan Kolom Baru di View (Index/Show)**

Tambahkan kolom baru di tabel atau di halaman detail.

**Contoh di Tabel Index:**

```blade
{{-- resources/views/users/index.blade.php --}}
<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Telepon</th>  {{-- ← TAMBAHKAN INI --}}
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?? '-' }}</td>  {{-- ← TAMBAHKAN INI --}}
                <td>...</td>
            </tr>
        @endforeach
    </tbody>
</table>
```

**Contoh di Halaman Show/Detail:**

```blade
{{-- resources/views/users/show.blade.php --}}
<div>
    <label class="text-sm font-medium text-gray-400">Nomor Telepon</label>
    <p class="text-white">{{ $user->phone ?? '-' }}</p>
</div>
```

**Referensi Contoh di Project Ini:**

-   File: `database/migrations/2025_11_15_071026_add_photo_and_phone_to_users_table.php`
-   File: `app/Models/User.php` - cek property `$fillable`
-   File: `resources/views/profile/edit.blade.php` - contoh form edit

---

## 4. Ubah Style/Tampilan

### 🤔 Apa itu Tailwind CSS?

Tailwind CSS adalah **framework CSS** yang menggunakan **utility classes** (class yang langsung memberikan style).

**Contoh:**

-   `bg-blue-600` → Background warna biru
-   `text-white` → Text warna putih
-   `p-4` → Padding 4 (1rem)
-   `rounded-lg` → Border radius besar

**Keuntungan Tailwind:**

-   Tidak perlu tulis CSS manual
-   Cukup tambahkan class di HTML
-   Konsisten dan cepat

### 💡 Cara Kerja Styling di Project Ini

**File CSS utama:** `resources/css/app.css` (menggunakan Tailwind CSS)
**Layout utama:** `resources/views/layouts/app.blade.php`
**Theme:** Dark mode (warna gelap: bg-gray-900, bg-gray-800, text-white, dll)

**Cara Ubah Style:**

1. Buka file Blade (misal: `resources/views/jobs/index.blade.php`)
2. Cari element yang ingin diubah (button, card, dll)
3. Ubah class Tailwind yang sesuai
4. Refresh browser untuk lihat perubahan

**⚠️ PENTING:**

-   Tidak perlu edit file CSS manual
-   Semua styling dilakukan melalui class di file Blade
-   Perubahan langsung terlihat (tidak perlu compile CSS)

### Langkah-langkah Ubah Style

#### **Step 1: Ubah Warna**

**Warna Primary (Biru):**
Cari dan ganti class warna biru dengan warna lain.

**Sebelum:**

```blade
<button class="bg-blue-600 hover:bg-blue-500 text-white">
    Submit
</button>
```

**Sesudah (misal jadi hijau):**

```blade
<button class="bg-green-600 hover:bg-green-500 text-white">
    Submit
</button>
```

**Palet Warna Tailwind yang Sering Dipakai:**

-   `blue` → `green`, `red`, `yellow`, `purple`, `indigo`, `pink`, `orange`
-   Contoh: `bg-blue-600` → `bg-green-600`
-   Variasi: `50` (paling terang) sampai `900` (paling gelap)

**Contoh Lengkap:**

```blade
<!-- Button dengan warna berbeda -->
<button class="bg-green-600 hover:bg-green-500">Hijau</button>
<button class="bg-red-600 hover:bg-red-500">Merah</button>
<button class="bg-purple-600 hover:bg-purple-500">Ungu</button>
<button class="bg-yellow-600 hover:bg-yellow-500">Kuning</button>
```

#### **Step 2: Ubah Jumlah Card yang Ditampilkan**

**Mengurangi Jumlah Card:**

-   Gunakan `take()` atau `limit()` di controller
-   Atau gunakan CSS `grid-cols-2` menjadi `grid-cols-1`

**Contoh di Controller:**

```php
// Sebelum: tampilkan semua
$jobs = Job::latest()->get();

// Sesudah: hanya tampilkan 6 card
$jobs = Job::latest()->take(6)->get();
```

**Contoh di View (Grid Layout):**

```blade
<!-- Sebelum: 3 kolom -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @foreach($jobs as $job)
        <!-- Card -->
    @endforeach
</div>

<!-- Sesudah: 2 kolom -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($jobs as $job)
        <!-- Card -->
    @endforeach
</div>

<!-- Atau 1 kolom (full width) -->
<div class="grid grid-cols-1 gap-4">
    @foreach($jobs as $job)
        <!-- Card -->
    @endforeach
</div>
```

#### **Step 3: Membuat Card Rata Tengah**

**Menggunakan Flexbox:**

```blade
<!-- Container dengan justify-center -->
<div class="flex flex-wrap justify-center gap-4">
    @foreach($jobs as $job)
        <div class="w-full max-w-sm">
            <!-- Card content -->
        </div>
    @endforeach
</div>
```

**Menggunakan Grid dengan max-width:**

```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 justify-items-center">
    @foreach($jobs as $job)
        <div class="w-full max-w-sm">
            <!-- Card content -->
        </div>
    @endforeach
</div>
```

**Atau dengan mx-auto:**

```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($jobs as $job)
        <div class="mx-auto w-full max-w-sm">
            <!-- Card content -->
        </div>
    @endforeach
</div>
```

#### **Step 4: Ubah Ukuran Card**

**Contoh Card dengan Ukuran Berbeda:**

```blade
<!-- Card kecil -->
<div class="rounded-lg border border-gray-700 bg-gray-800 p-4 max-w-xs">
    <!-- Content -->
</div>

<!-- Card sedang (default) -->
<div class="rounded-lg border border-gray-700 bg-gray-800 p-6 max-w-md">
    <!-- Content -->
</div>

<!-- Card besar -->
<div class="rounded-lg border border-gray-700 bg-gray-800 p-8 max-w-lg">
    <!-- Content -->
</div>
```

#### **Step 5: Ubah Spacing & Padding**

```blade
<!-- Padding lebih kecil -->
<div class="p-2">  <!-- padding: 0.5rem -->
<!-- Padding sedang -->
<div class="p-4">  <!-- padding: 1rem -->
<!-- Padding besar -->
<div class="p-8">  <!-- padding: 2rem -->

<!-- Margin -->
<div class="m-4">  <!-- margin: 1rem -->
<div class="mt-4"> <!-- margin-top: 1rem -->
<div class="mb-4"> <!-- margin-bottom: 1rem -->
<div class="mx-auto"> <!-- margin kiri-kanan: auto (center) -->
```

#### **Step 6: Ubah Border Radius (Kemiringan Sudut)**

```blade
<!-- Sudut tajam -->
<div class="rounded-none">

<!-- Sudut sedikit -->
<div class="rounded">

<!-- Sudut sedang -->
<div class="rounded-lg">

<!-- Sudut sangat melengkung -->
<div class="rounded-xl">

<!-- Bulat penuh -->
<div class="rounded-full">
```

**Referensi Contoh di Project Ini:**

-   File: `resources/views/jobs/index.blade.php` - contoh grid layout dan card
-   File: `resources/views/layouts/app.blade.php` - layout utama dengan dark theme
-   File: `resources/views/admin/users/index.blade.php` - contoh tabel dengan styling

---

## 5. Perintah Artisan Laravel

### Daftar Perintah untuk Membuat File

#### **1. Membuat Controller**

```bash
php artisan make:controller NamaController
```

**Contoh:**

```bash
php artisan make:controller ProductController
php artisan make:controller AdminCategoryController
```

**File akan dibuat di:** `app/Http/Controllers/NamaController.php`

**Dengan Resource (CRUD lengkap):**

```bash
php artisan make:controller ProductController --resource
```

Akan membuat method: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`

**Dengan Model:**

```bash
php artisan make:controller ProductController --model=Product
```

---

#### **2. Membuat Model**

```bash
php artisan make:model NamaModel
```

**Contoh:**

```bash
php artisan make:model Product
php artisan make:model Category
```

**File akan dibuat di:** `app/Models/NamaModel.php`

**Dengan Migration:**

```bash
php artisan make:model Product -m
```

**Dengan Migration + Factory + Seeder:**

```bash
php artisan make:model Product -mfs
```

---

#### **3. Membuat Migration**

```bash
php artisan make:migration nama_migration
```

**Contoh untuk membuat tabel baru:**

```bash
php artisan make:migration create_products_table
```

**Contoh untuk menambah kolom:**

```bash
php artisan make:migration add_price_to_products_table --table=products
```

**Contoh untuk menghapus kolom:**

```bash
php artisan make:migration remove_price_from_products_table --table=products
```

**File akan dibuat di:** `database/migrations/YYYY_MM_DD_HHMMSS_nama_migration.php`

**Jalankan Migration:**

```bash
php artisan migrate
```

**Rollback:**

```bash
php artisan migrate:rollback
```

---

#### **4. Membuat Middleware**

```bash
php artisan make:middleware NamaMiddleware
```

**Contoh:**

```bash
php artisan make:middleware EnsureUserIsAdmin
php artisan make:middleware CheckAge
```

**File akan dibuat di:** `app/Http/Middleware/NamaMiddleware.php`

**Daftarkan Middleware di:** `bootstrap/app.php` (Laravel 11) atau `app/Http/Kernel.php` (Laravel 10)

**Contoh Pendaftaran di Laravel 11:**

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
    ]);
})
```

**Penggunaan di Route:**

```php
Route::middleware('admin')->group(function () {
    // routes yang memerlukan admin
});
```

**Referensi Contoh di Project Ini:**

-   File: `app/Http/Middleware/EnsureUserIsAdmin.php`
-   File: `bootstrap/app.php` (baris 22-24)

---

#### **5. Membuat Request (Form Request Validation)**

```bash
php artisan make:request NamaRequest
```

**Contoh:**

```bash
php artisan make:request StoreProductRequest
php artisan make:request UpdateProductRequest
```

**File akan dibuat di:** `app/Http/Requests/NamaRequest.php`

**Penggunaan di Controller:**

```php
public function store(StoreProductRequest $request)
{
    // $request sudah divalidasi
    $data = $request->validated();
    // ...
}
```

---

#### **6. Membuat Seeder**

```bash
php artisan make:seeder NamaSeeder
```

**Contoh:**

```bash
php artisan make:seeder ProductSeeder
php artisan make:seeder UserSeeder
```

**File akan dibuat di:** `database/seeders/NamaSeeder.php`

**Jalankan Seeder:**

```bash
php artisan db:seed --class=NamaSeeder
```

**Jalankan semua seeder:**

```bash
php artisan db:seed
```

---

#### **7. Membuat Factory**

```bash
php artisan make:factory NamaFactory
```

**Contoh:**

```bash
php artisan make:factory ProductFactory
```

**File akan dibuat di:** `database/factories/NamaFactory.php`

**Dengan Model:**

```bash
php artisan make:factory ProductFactory --model=Product
```

---

#### **8. Membuat Policy**

```bash
php artisan make:policy NamaPolicy
```

**Contoh:**

```bash
php artisan make:policy ProductPolicy --model=Product
```

**File akan dibuat di:** `app/Policies/NamaPolicy.php`

**Daftarkan di:** `app/Providers/AuthServiceProvider.php`

**Referensi Contoh di Project Ini:**

-   File: `app/Policies/JobPolicy.php`

---

#### **9. Membuat Service**

**Laravel tidak punya command khusus untuk Service, tapi bisa dibuat manual:**

**Lokasi:** `app/Services/NamaService.php`

**Contoh Struktur:**

```php
<?php

namespace App\Services;

class NamaService
{
    public function methodSatu()
    {
        // logic here
    }
}
```

**Referensi Contoh di Project Ini:**

-   File: `app/Services/JobExpirationService.php`
-   File: `app/Services/RedeemCodeExpirationService.php`

---

#### **10. Perintah Lainnya yang Berguna**

**Menampilkan Daftar Routes:**

```bash
php artisan route:list
```

**Clear Cache:**

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

**Optimasi:**

```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Membuat User Baru (jika ada seeder):**

```bash
php artisan tinker
# Kemudian di tinker:
User::create(['name' => 'Test', 'email' => 'test@test.com', 'password' => Hash::make('password')]);
```

---

## 📝 Quick Reference Checklist

### Checklist Filter

-   [ ] Tambah logika filter di Controller (method `index()`)
-   [ ] Tambah form filter di View
-   [ ] Pass filter values ke View
-   [ ] Maintain filter pada pagination dengan `appends()`

### Checklist Pagination

-   [ ] Ganti `get()` dengan `paginate(n)` di Controller
-   [ ] Tambah `{{ $data->links() }}` di View
-   [ ] (Opsional) Tambah info jumlah data

### Checklist Kolom Database

-   [ ] Buat migration: `php artisan make:migration add_nama_kolom_to_nama_tabel_table --table=nama_tabel`
-   [ ] Edit migration (tambah kolom di `up()`, hapus di `down()`)
-   [ ] Jalankan: `php artisan migrate`
-   [ ] Update Model (tambah ke `$fillable`)
-   [ ] Update Form (tambah input field)
-   [ ] Update Controller (tambah validasi & save)
-   [ ] Update View (tampilkan kolom baru)

### Checklist Style

-   [ ] Cari class warna yang ingin diubah
-   [ ] Ganti dengan warna baru (contoh: `bg-blue-600` → `bg-green-600`)
-   [ ] Ubah grid layout jika perlu (contoh: `grid-cols-3` → `grid-cols-2`)
-   [ ] Ubah spacing/padding jika perlu

### Checklist File Baru

-   [ ] Controller: `php artisan make:controller NamaController`
-   [ ] Model: `php artisan make:model NamaModel`
-   [ ] Migration: `php artisan make:migration nama_migration`
-   [ ] Middleware: `php artisan make:middleware NamaMiddleware`
-   [ ] Request: `php artisan make:request NamaRequest`
-   [ ] Seeder: `php artisan make:seeder NamaSeeder`
-   [ ] Factory: `php artisan make:factory NamaFactory`
-   [ ] Policy: `php artisan make:policy NamaPolicy`

---

## 🎯 Tips & Best Practices

1. **Selalu gunakan migration untuk perubahan database** - jangan edit manual di database
2. **Test perubahan setelah migration** - pastikan tidak ada error
3. **Gunakan transaction untuk operasi penting** - contoh: transfer saldo, update job
4. **Validasi selalu di controller atau Form Request** - jangan percaya input user
5. **Gunakan `$request->filled()` untuk cek filter** - lebih aman dari `$request->has()`
6. **Maintain filter di pagination** - gunakan `appends(request()->query())`
7. **Gunakan eager loading** - `with()` untuk menghindari N+1 query
8. **Style konsisten** - ikuti pattern yang sudah ada di project

---

## 📂 Struktur File Penting

```
app/
├── Http/
│   ├── Controllers/     # Semua controller ada di sini
│   ├── Middleware/      # Middleware custom
│   └── Requests/        # Form Request validation
├── Models/              # Eloquent models
├── Policies/            # Authorization policies
└── Services/            # Business logic services

database/
├── migrations/          # Database migrations
├── seeders/            # Database seeders
└── factories/          # Model factories

resources/
└── views/
    ├── layouts/        # Layout templates
    └── [folder]/       # View per fitur (jobs, users, dll)
    └── vendor/
        └── pagination/ # Custom pagination view

routes/
└── web.php            # Web routes
```

---

## 💪 List Latihan

Berikut adalah list latihan yang bisa dicoba berdasarkan analisa project ini. **Latihan ini disusun dari yang paling mudah ke yang lebih sulit.**

### 🟢 Level Pemula (Wajib Dikuasai!)

#### **Latihan 1: Tambah Filter Search di Halaman Jobs**

**Tujuan:** Tambahkan search box untuk mencari job berdasarkan judul/deskripsi

**Langkah:**

1. Buka `app/Http/Controllers/JobController.php` → method `index()`
2. Tambahkan logika search (copy dari `AdminUserController.php`)
3. Buka `resources/views/jobs/index.blade.php`
4. Tambahkan form search di bagian atas
5. Test dengan mencari job tertentu

**File yang perlu diedit:**

-   `app/Http/Controllers/JobController.php`
-   `resources/views/jobs/index.blade.php`

**Referensi:** `AdminUserController.php` (baris 20-23)

---

#### **Latihan 2: Ubah Warna Button**

**Tujuan:** Ubah warna button "Job Baru" dari biru menjadi hijau

**Langkah:**

1. Buka `resources/views/jobs/index.blade.php`
2. Cari button dengan class `bg-blue-600`
3. Ganti menjadi `bg-green-600`
4. Jangan lupa ganti hover color juga (`hover:bg-blue-500` → `hover:bg-green-500`)

**File yang perlu diedit:**

-   `resources/views/jobs/index.blade.php`

---

#### **Latihan 3: Tambah Kolom "Location" ke Tabel Jobs**

**Tujuan:** Tambahkan field lokasi di job (misal: "Jakarta", "Bandung", dll)

**Langkah:**

1. Buat migration: `php artisan make:migration add_location_to_jobs_table --table=jobs`
2. Edit migration, tambah kolom `location` (string, nullable)
3. Jalankan: `php artisan migrate`
4. Update model `Job.php`, tambah `location` ke `$fillable`
5. Update form create/edit job, tambah input location
6. Update controller, tambah validasi untuk location
7. Update view index, tampilkan location di card job

**File yang perlu dibuat/diedit:**

-   `database/migrations/YYYY_MM_DD_HHMMSS_add_location_to_jobs_table.php` (baru)
-   `app/Models/Job.php`
-   `resources/views/jobs/partials/form-fields.blade.php`
-   `app/Http/Controllers/JobController.php`
-   `resources/views/jobs/index.blade.php`

**Referensi:** Lihat bagian "Tambah Kolom Database" di dokumentasi ini

---

#### **Latihan 4: Ubah Jumlah Item per Halaman Pagination**

**Tujuan:** Ubah dari 10 item per halaman menjadi 15 item per halaman

**Langkah:**

1. Buka `app/Http/Controllers/JobController.php`
2. Cari `paginate(10)` → ubah menjadi `paginate(15)`
3. Test dengan membuat lebih dari 15 job

**File yang perlu diedit:**

-   `app/Http/Controllers/JobController.php` (baris 24, 38-40)

---

### 🟡 Level Menengah

#### **Latihan 5: Tambah Filter Status di Halaman Jobs (User)**

**Tujuan:** Tambahkan filter dropdown untuk filter job berdasarkan status

**Langkah:**

1. Di controller `JobController@index()`, tambah logika filter status
2. Di view `jobs/index.blade.php`, tambah dropdown filter status
3. Pastikan filter tetap aktif saat pagination

**File yang perlu diedit:**

-   `app/Http/Controllers/JobController.php`
-   `resources/views/jobs/index.blade.php`

**Referensi:** `AdminUserController.php` (baris 26-28)

---

#### **Latihan 6: Tambah Filter Kategori di Halaman Jobs**

**Tujuan:** Tambahkan filter dropdown untuk filter job berdasarkan kategori

**Langkah:**

1. Di controller, tambah logika filter kategori (gunakan `whereHas`)
2. Di view, tambah dropdown kategori (loop dari `$categories`)
3. Pastikan controller mengirim `$categories` ke view

**File yang perlu diedit:**

-   `app/Http/Controllers/JobController.php`
-   `resources/views/jobs/index.blade.php`

**Referensi:** Lihat contoh filter kategori di bagian "Tambah Fitur Filter"

---

#### **Latihan 7: Ubah Layout Card Jobs dari 3 Kolom menjadi 2 Kolom**

**Tujuan:** Ubah tampilan grid job cards

**Langkah:**

1. Buka `resources/views/jobs/index.blade.php`
2. Cari class `grid-cols-3` atau `md:grid-cols-3`
3. Ubah menjadi `grid-cols-2` atau `md:grid-cols-2`

**File yang perlu diedit:**

-   `resources/views/jobs/index.blade.php`

---

#### **Latihan 8: Tambah Kolom "Website" ke Tabel Users**

**Tujuan:** Tambahkan field website/portfolio di profile user

**Langkah:**

1. Buat migration untuk tambah kolom `website` (string, nullable)
2. Update model `User.php`
3. Update form edit profile
4. Update controller `ProfileController@update()`
5. Update view show profile untuk tampilkan website

**File yang perlu dibuat/diedit:**

-   Migration file (baru)
-   `app/Models/User.php`
-   `resources/views/profile/edit.blade.php`
-   `app/Http/Controllers/ProfileController.php`
-   `resources/views/profile/show.blade.php`

---

### 🔴 Level Lanjutan

#### **Latihan 9: Tambah Fitur Sort (Urutkan) di Halaman Jobs**

**Tujuan:** User bisa urutkan job berdasarkan: Terbaru, Terlama, Harga Tertinggi, Harga Terendah

**Langkah:**

1. Di controller, tambah parameter `sort` dari request
2. Gunakan switch/case untuk menentukan orderBy berdasarkan sort
3. Di view, tambah dropdown untuk pilihan sort
4. Default: Terbaru (`orderBy('created_at', 'desc')`)

**File yang perlu diedit:**

-   `app/Http/Controllers/JobController.php`
-   `resources/views/jobs/index.blade.php`

---

#### **Latihan 10: Buat Controller & View Baru untuk "Notifications"**

**Tujuan:** Buat fitur notifikasi sederhana (hanya tampilan, belum real-time)

**Langkah:**

1. Buat controller: `php artisan make:controller NotificationController`
2. Buat method `index()` untuk tampilkan notifikasi
3. Tambah route di `routes/web.php`
4. Buat view `resources/views/notifications/index.blade.php`
5. Tambah link di sidebar untuk akses notifikasi

**File yang perlu dibuat:**

-   `app/Http/Controllers/NotificationController.php` (baru)
-   `resources/views/notifications/index.blade.php` (baru)

**File yang perlu diedit:**

-   `routes/web.php`
-   `resources/views/layouts/partials/sidebar.blade.php`

---

#### **Latihan 11: Tambah Middleware "CheckBalance" untuk Topup**

**Tujuan:** Buat middleware yang cek apakah user sudah punya saldo minimal sebelum bisa topup

**Langkah:**

1. Buat middleware: `php artisan make:middleware CheckBalance`
2. Di middleware, cek `auth()->user()->balance`
3. Jika balance > 100000, redirect dengan error
4. Daftarkan middleware di `bootstrap/app.php`
5. Apply middleware ke route topup create

**File yang perlu dibuat:**

-   `app/Http/Middleware/CheckBalance.php` (baru)

**File yang perlu diedit:**

-   `bootstrap/app.php`
-   `routes/web.php`

**Referensi:** `app/Http/Middleware/EnsureUserIsAdmin.php`

---

#### **Latihan 12: Tambah Fitur Export Data Jobs ke CSV**

**Tujuan:** Admin bisa export semua job ke file CSV

**Langkah:**

1. Di `AdminJobController` (atau buat baru), tambah method `export()`
2. Gunakan Laravel Excel atau buat manual dengan fputcsv
3. Tambah route untuk export
4. Tambah button di halaman admin jobs

**File yang perlu diedit:**

-   Controller admin (atau buat baru)
-   `routes/web.php`
-   View admin jobs

---

### 📋 Latihan Tambahan (Opsional)

#### **Latihan 13: Ubah Style Dashboard Cards**

-   Ubah warna card dashboard
-   Ubah layout dari 4 kolom menjadi 3 kolom
-   Ubah border radius

#### **Latihan 14: Tambah Kolom "Notes" di Topup**

-   Admin bisa kasih catatan saat approve/reject topup

#### **Latihan 15: Filter Reports berdasarkan Status**

-   Tambah filter di halaman admin reports

#### **Latihan 16: Tambah Kolom "Last Login" di Users Table**

-   Track kapan user terakhir login

#### **Latihan 17: Buat View untuk Riwayat Transaksi User**

-   Tampilkan semua topup dan payment history

---

## 📝 Tips Mengerjakan Latihan

1. **Mulai dari yang Paling Mudah** - Latihan 1-4 wajib dikuasai dulu
2. **Test Setiap Perubahan** - Jangan langsung ke latihan berikutnya sebelum yakin latihan sebelumnya sudah benar
3. **Copy-Paste dari Contoh** - Gunakan contoh yang sudah ada di project sebagai referensi
4. **Jangan Panik** - Jika error, baca error message dengan teliti
5. **Gunakan Dokumentasi** - Balik lagi ke bagian dokumentasi yang relevan
6. **Latihan = Kunci** - Semakin sering latihan, semakin cepat dan lancar

---

## 🚀 Good Luck untuk Live Coding!

**Ingat:**

-   ✅ Baca instruksi dengan teliti
-   ✅ Fokus pada yang diminta (jangan over-engineering)
-   ✅ Test setelah setiap perubahan
-   ✅ Gunakan dokumentasi ini sebagai reference cepat
-   ✅ Tetap tenang dan sistematis
-   ✅ Latihan, latihan, dan latihan!

**Selamat mengerjakan! 🎉**
