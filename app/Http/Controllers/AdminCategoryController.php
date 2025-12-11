<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * AdminCategoryController
 * 
 * BEST PRACTICES:
 * 1. Authorization: Hanya admin yang bisa CRUD kategori
 * 2. Validation: Gunakan Form Request untuk validasi
 * 3. Error Handling: Cek apakah kategori digunakan sebelum delete
 * 4. User Experience: Berikan pesan error/success yang jelas
 * 5. Search & Filter: Implementasi search untuk kemudahan admin
 */
class AdminCategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori
     * 
     * BEST PRACTICES:
     * - Authorization check
     * - Search functionality
     * - Pagination untuk performa
     * - Order by name untuk konsistensi
     */
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        $query = Category::query();

        // BEST PRACTICE: Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // BEST PRACTICE: Order by name untuk konsistensi
        $categories = $query->orderBy('name')->paginate(15);

        return view('admin.categories.index', [
            'categories' => $categories,
            'search' => $request->get('search', ''),
        ]);
    }

    /**
     * Menampilkan form untuk membuat kategori baru
     */
    public function create(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        return view('admin.categories.create');
    }

    /**
     * Menyimpan kategori baru
     * 
     * BEST PRACTICES:
     * - Gunakan Form Request untuk validasi
     * - Berikan feedback yang jelas
     */
    public function store(StoreCategoryRequest $request)
    {
        Category::create([
            'name' => $request->validated()['name'],
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', "✅ Kategori <strong>{$request->validated()['name']}</strong> berhasil ditambahkan.");
    }

    /**
     * Menampilkan form untuk edit kategori
     */
    public function edit(Request $request, Category $category)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update kategori
     * 
     * BEST PRACTICES:
     * - Gunakan Form Request untuk validasi
     * - Berikan feedback yang jelas
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update([
            'name' => $request->validated()['name'],
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', "✅ Kategori berhasil diubah menjadi <strong>{$request->validated()['name']}</strong>.");
    }

    /**
     * Hapus kategori
     * 
     * BEST PRACTICES:
     * - Cek apakah kategori digunakan di job sebelum delete
     * - Berikan pesan error yang jelas jika kategori sedang digunakan
     * - Cascade delete sudah di-handle oleh database (onDelete cascade)
     */
    public function destroy(Request $request, Category $category)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        // BEST PRACTICE: Cek apakah kategori digunakan di job
        $jobsCount = $category->jobs()->count();
        
        if ($jobsCount > 0) {
            return back()->withErrors([
                'category' => "Kategori <strong>{$category->name}</strong> tidak bisa dihapus karena sedang digunakan oleh {$jobsCount} job. Hapus atau ubah kategori pada job tersebut terlebih dahulu.",
            ]);
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', "✅ Kategori <strong>{$categoryName}</strong> berhasil dihapus.");
    }
}

