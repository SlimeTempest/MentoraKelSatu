<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

/**
 * Controller untuk pengaturan sistem oleh admin
 * 
 * Dibuat oleh: Reffael (Role: Admin)
 * Fitur:
 * - Pengaturan rekening bank untuk topup (BCA, Mandiri)
 * - Update konfigurasi sistem
 * 
 * BEST PRACTICE:
 * - Validasi authorization di setiap method
 * - Menggunakan config file untuk menyimpan pengaturan
 * - Clear config cache setelah update untuk memastikan perubahan langsung terlihat
 * - Escape input untuk keamanan saat menulis ke file config
 */
class AdminSettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan sistem
     * 
     * BEST PRACTICE:
     * - Membaca konfigurasi dari config file
     * - Menyusun data dalam format yang mudah digunakan di view
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // BEST PRACTICE: Validasi authorization di awal
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        // BEST PRACTICE: Membaca konfigurasi dari config file
        $topupConfig = config('topup');
        
        // Menyusun data dalam format yang mudah digunakan di view
        $settings = [
            'bca' => [
                'number' => $topupConfig['bca']['number'],
                'name' => $topupConfig['bca']['name'],
            ],
            'mandiri' => [
                'number' => $topupConfig['mandiri']['number'],
                'name' => $topupConfig['mandiri']['name'],
            ],
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Mengupdate pengaturan rekening bank untuk topup
     * 
     * BEST PRACTICE:
     * - Validasi input menggunakan Laravel validation
     * - Escape input menggunakan addslashes() untuk keamanan saat menulis ke file
     * - Menggunakan File facade untuk operasi file system
     * - Clear config cache setelah update untuk memastikan perubahan langsung terlihat
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // BEST PRACTICE: Validasi authorization di awal
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        // BEST PRACTICE: Validasi input menggunakan Laravel validation
        $data = $request->validate([
            'bca_number' => ['required', 'string', 'max:255'],
            'bca_name' => ['required', 'string', 'max:255'],
            'mandiri_number' => ['required', 'string', 'max:255'],
            'mandiri_name' => ['required', 'string', 'max:255'],
        ]);

        $configPath = config_path('topup.php');
        
        // BEST PRACTICE: Escape single quotes untuk keamanan saat menulis ke file
        // addslashes() mencegah injection jika ada single quote dalam input
        $bcaNumber = addslashes($data['bca_number']);
        $bcaName = addslashes($data['bca_name']);
        $mandiriNumber = addslashes($data['mandiri_number']);
        $mandiriName = addslashes($data['mandiri_name']);
        
        // Membuat konten config file dengan format PHP array
        $configContent = "<?php\n\nreturn [\n    'bca' => [\n        'number' => env('TOPUP_BCA_NUMBER', '{$bcaNumber}'),\n        'name' => env('TOPUP_BCA_NAME', '{$bcaName}'),\n    ],\n    'mandiri' => [\n        'number' => env('TOPUP_MANDIRI_NUMBER', '{$mandiriNumber}'),\n        'name' => env('TOPUP_MANDIRI_NAME', '{$mandiriName}'),\n    ],\n];\n";

        // BEST PRACTICE: Menggunakan File facade untuk operasi file system
        File::put($configPath, $configContent);

        // BEST PRACTICE: Clear config cache setelah update untuk memastikan perubahan langsung terlihat
        // Tanpa clear cache, perubahan mungkin tidak langsung terlihat karena Laravel cache config
        \Artisan::call('config:clear');

        return back()->with('status', 'Pengaturan rekening berhasil diperbarui.');
    }
}

