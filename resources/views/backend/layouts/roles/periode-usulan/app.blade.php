@extends('backend.layouts.base', [
    'jsModule' => 'admin-universitas/index.js',
    'sidebarComponent' => 'backend.components.sidebar-admin-universitas-usulan',
    'role' => 'periode-usulan'
])

@section('title', 'Periode Usulan')

@section('description', 'Pengelolaan Periode Usulan - Sistem Kepegawaian UNMUL')

@push('styles')
<style>
    /* Periode Usulan specific styles */
    .periode-usulan-dashboard {
        /* Custom styles for periode usulan dashboard */
    }

    .periode-card {
        transition: all 0.3s ease;
    }

    .periode-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-buka {
        background-color: #dcfce7;
        color: #166534;
    }

    .status-tutup {
        background-color: #fef2f2;
        color: #dc2626;
    }
</style>
@endpush

@section('content')
    @yield('dashboard-content')
@endsection
