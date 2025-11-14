<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminSettingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        $topupConfig = config('topup');
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

    public function update(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        $data = $request->validate([
            'bca_number' => ['required', 'string', 'max:255'],
            'bca_name' => ['required', 'string', 'max:255'],
            'mandiri_number' => ['required', 'string', 'max:255'],
            'mandiri_name' => ['required', 'string', 'max:255'],
        ]);

        $configPath = config_path('topup.php');
        
        // Escape single quotes untuk keamanan
        $bcaNumber = addslashes($data['bca_number']);
        $bcaName = addslashes($data['bca_name']);
        $mandiriNumber = addslashes($data['mandiri_number']);
        $mandiriName = addslashes($data['mandiri_name']);
        
        $configContent = "<?php\n\nreturn [\n    'bca' => [\n        'number' => env('TOPUP_BCA_NUMBER', '{$bcaNumber}'),\n        'name' => env('TOPUP_BCA_NAME', '{$bcaName}'),\n    ],\n    'mandiri' => [\n        'number' => env('TOPUP_MANDIRI_NUMBER', '{$mandiriNumber}'),\n        'name' => env('TOPUP_MANDIRI_NAME', '{$mandiriName}'),\n    ],\n];\n";

        File::put($configPath, $configContent);

        // Clear config cache
        \Artisan::call('config:clear');

        return back()->with('status', 'Pengaturan rekening berhasil diperbarui.');
    }
}

