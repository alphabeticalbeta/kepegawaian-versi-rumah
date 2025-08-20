<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BackendUnivUsulan\Usulan;

echo "=== DEBUG SUBMISSION ISSUE ===\n";

// Check usulan 15 current state
$usulan = Usulan::find(15);
if ($usulan) {
    echo "=== USULAN 15 STATE ===\n";
    echo "ID: {$usulan->id}\n";
    echo "Status: {$usulan->status_usulan}\n";
    
    if (isset($usulan->validasi_data['admin_fakultas'])) {
        $adminFakultasData = $usulan->validasi_data['admin_fakultas'];
        echo "Admin Fakultas Keys: " . implode(', ', array_keys($adminFakultasData)) . "\n";
        
        if (isset($adminFakultasData['dokumen_pendukung'])) {
            $dokumenPendukung = $adminFakultasData['dokumen_pendukung'];
            echo "Dokumen Pendukung Keys: " . implode(', ', array_keys($dokumenPendukung)) . "\n";
            echo "Is Empty: " . (empty($dokumenPendukung) ? 'YES' : 'NO') . "\n";
            
            if (!empty($dokumenPendukung)) {
                echo "Dokumen Pendukung Content:\n";
                foreach ($dokumenPendukung as $key => $value) {
                    echo "  $key: $value\n";
                }
            }
        } else {
            echo "Dokumen Pendukung: NOT SET\n";
        }
    }
}

// Test the logic from controller
echo "\n=== TESTING CONTROLLER LOGIC ===\n";

$currentValidasi = $usulan->validasi_data ?? [];
$dokumenPendukung = $currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];
$isInitialSubmission = empty($dokumenPendukung);

echo "Is Initial Submission: " . ($isInitialSubmission ? 'YES' : 'NO') . "\n";

// Test validation rules that would be applied
$rules = [
    'validation' => 'required|array',
    'dokumen_pendukung.nomor_surat_usulan' => 'required|string|max:255',
    'dokumen_pendukung.nomor_berita_senat' => 'required|string|max:255',
];

$messages = [
    'dokumen_pendukung.nomor_surat_usulan.required' => 'Nomor surat usulan wajib diisi.',
    'dokumen_pendukung.nomor_berita_senat.required' => 'Nomor berita senat wajib diisi.',
];

// Only require files for initial submission
if ($isInitialSubmission) {
    $rules['dokumen_pendukung.file_surat_usulan'] = 'required|file|mimes:pdf|max:1024';
    $rules['dokumen_pendukung.file_berita_senat'] = 'required|file|mimes:pdf|max:1024';
    $messages['dokumen_pendukung.file_surat_usulan.required'] = 'File surat usulan wajib diunggah.';
    $messages['dokumen_pendukung.file_berita_senat.required'] = 'File berita senat wajib diunggah.';
}

echo "Validation Rules:\n";
foreach ($rules as $field => $rule) {
    echo "  $field: $rule\n";
}

// Test with sample data (simulating what frontend sends)
$testData = [
    'action_type' => 'forward_to_university',
    'validation' => [
        'dokumen_profil' => [
            'ijazah_terakhir' => ['status' => 'sesuai', 'keterangan' => ''],
        ]
    ],
    'dokumen_pendukung' => [
        'nomor_surat_usulan' => '001/UNMUL/2024',
        'nomor_berita_senat' => '002/UNMUL/2024'
    ]
];

echo "\nTest Data:\n";
print_r($testData);

$validator = \Illuminate\Support\Facades\Validator::make($testData, $rules, $messages);

if ($validator->fails()) {
    echo "\n❌ Validation FAILED:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "  - $error\n";
    }
    
    echo "\nDetailed Errors:\n";
    foreach ($validator->errors()->toArray() as $field => $errors) {
        echo "  $field: " . implode(', ', $errors) . "\n";
    }
} else {
    echo "\n✅ Validation PASSED\n";
}

// Test the final check logic
echo "\n=== TESTING FINAL CHECK LOGIC ===\n";

// Simulate what happens after validation passes
$currentValidasi = $usulan->validasi_data ?? [];
$currentDokumenPendukung = $currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];

// Update text fields (simulating what controller does)
$currentDokumenPendukung['nomor_surat_usulan'] = '001/UNMUL/2024';
$currentDokumenPendukung['nomor_berita_senat'] = '002/UNMUL/2024';

echo "Current Dokumen Pendukung after text update:\n";
foreach ($currentDokumenPendukung as $key => $value) {
    echo "  $key: $value\n";
}

// Final check (from controller)
$hasFileSurat = !empty($currentDokumenPendukung['file_surat_usulan_path']);
$hasFileBerita = !empty($currentDokumenPendukung['file_berita_senat_path']);

echo "\nFile Check:\n";
echo "  file_surat_usulan_path exists: " . ($hasFileSurat ? 'YES' : 'NO') . "\n";
echo "  file_berita_senat_path exists: " . ($hasFileBerita ? 'YES' : 'NO') . "\n";

if (empty($currentDokumenPendukung['file_surat_usulan_path']) || empty($currentDokumenPendukung['file_berita_senat_path'])) {
    echo "❌ FINAL CHECK FAILED: File surat usulan dan file berita senat harus diunggah\n";
} else {
    echo "✅ FINAL CHECK PASSED\n";
}

echo "\n=== DONE ===\n";
