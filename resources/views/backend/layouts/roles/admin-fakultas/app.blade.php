@extends('backend.layouts.base', [
    'jsModule' => 'admin-fakultas/index.js',
    'sidebarComponent' => 'backend.components.sidebar-admin-fakultas',
    'role' => 'admin-fakultas'
])

@section('title', 'Dashboard Admin Fakultas')

@section('description', 'Dashboard untuk Admin Fakultas - Sistem Kepegawaian UNMUL')

@push('styles')
<style>
    /* Admin Fakultas specific styles */
    .admin-fakultas-dashboard {
        /* Custom styles for admin fakultas dashboard */
    }

    .usulan-card {
        transition: all 0.3s ease;
    }

    .usulan-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush
