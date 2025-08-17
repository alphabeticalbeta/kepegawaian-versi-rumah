@extends('backend.layouts.base', [
    'jsModule' => 'admin-universitas/index.js',
    'sidebarComponent' => 'backend.components.sidebar-admin-universitas-usulan',
    'role' => 'admin-univ-usulan'
])

@section('title', 'Admin Dashboard')

@section('description', 'Dashboard untuk Admin Universitas Usulan - Sistem Kepegawaian UNMUL')

@push('styles')
<style>
    /* Admin Univ Usulan specific styles */
    .admin-univ-usulan-dashboard {
        /* Custom styles for admin univ usulan dashboard */
    }

    .data-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }

    .data-table th {
        background-color: #f9fafb;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #6b7280;
        padding: 0.75rem 1rem;
        text-align: left;
    }

    .data-table td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        border-top: 1px solid #e5e7eb;
    }

    .data-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.2s;
    }

    .btn-primary {
        background-color: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background-color: #2563eb;
    }

    .card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-body {
        padding: 1.5rem;
    }
</style>
@endpush

@section('content')
    @yield('content')
@endsection
