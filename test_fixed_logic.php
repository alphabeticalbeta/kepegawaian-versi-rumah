<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BackendUnivUsulan\Usulan;

echo "=== TEST FIXED LOGIC ===\n";

// Check usulan 15 current state
$usulan = Usulan::find(15);
if ($usulan) {
    echo "=== USULAN 15 STATE ===\n";
    echo "ID: {$usulan->id}\n";
    echo "Status: {$usulan->status_usulan}\n";
}

// Test the fixed logic
echo "\n=== TESTING FIXED LOGIC ===\n";

$currentValidasi = $usulan->validasi_data ?? [];
$dokumenPendukung = $currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];
$isInitialSubmission = empty($dokumenPendukung);

echo "Is Initial Submission: " . ($isInitialSubmission ? 'YES' : 'NO') . "\n";

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

// Final check (from controller) - FIXED LOGIC
$hasFileSurat = !empty($currentDokumenPendukung['file_surat_usulan_path']);
$hasFileBerita = !empty($currentDokumenPendukung['file_berita_senat_path']);

echo "\nFile Check:\n";
echo "  file_surat_usulan_path exists: " . ($hasFileSurat ? 'YES' : 'NO') . "\n";
echo "  file_berita_senat_path exists: " . ($hasFileBerita ? 'YES' : 'NO') . "\n";

// OLD LOGIC (would fail)
if (empty($currentDokumenPendukung['file_surat_usulan_path']) || empty($currentDokumenPendukung['file_berita_senat_path'])) {
    echo "❌ OLD LOGIC: Would fail - File surat usulan dan file berita senat harus diunggah\n";
} else {
    echo "✅ OLD LOGIC: Would pass\n";
}

// NEW LOGIC (only check for initial submission)
if ($isInitialSubmission && (empty($currentDokumenPendukung['file_surat_usulan_path']) || empty($currentDokumenPendukung['file_berita_senat_path']))) {
    echo "❌ NEW LOGIC: Would fail - File surat usulan dan file berita senat harus diunggah (initial submission)\n";
} else {
    echo "✅ NEW LOGIC: Would pass\n";
}

// Test what happens if we manually set some file paths
echo "\n=== TESTING WITH MANUAL FILE PATHS ===\n";

$currentDokumenPendukung['file_surat_usulan_path'] = 'dokumen-fakultas/surat-usulan/test.pdf';
$currentDokumenPendukung['file_berita_senat_path'] = 'dokumen-fakultas/berita-senat/test.pdf';

echo "After setting file paths:\n";
foreach ($currentDokumenPendukung as $key => $value) {
    echo "  $key: $value\n";
}

// Test final check again
$hasFileSurat = !empty($currentDokumenPendukung['file_surat_usulan_path']);
$hasFileBerita = !empty($currentDokumenPendukung['file_berita_senat_path']);

echo "\nFile Check (with manual paths):\n";
echo "  file_surat_usulan_path exists: " . ($hasFileSurat ? 'YES' : 'NO') . "\n";
echo "  file_berita_senat_path exists: " . ($hasFileBerita ? 'YES' : 'NO') . "\n";

if ($isInitialSubmission && (empty($currentDokumenPendukung['file_surat_usulan_path']) || empty($currentDokumenPendukung['file_berita_senat_path']))) {
    echo "❌ NEW LOGIC: Would fail\n";
} else {
    echo "✅ NEW LOGIC: Would pass\n";
}

echo "\n=== DONE ===\n";
