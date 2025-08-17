@extends('backend.layouts.base', [
    'jsModule' => 'penilai/index.js',
    'sidebarComponent' => 'backend.components.sidebar-penilai-universitas',
    'role' => 'penilai-universitas'
])

@section('title', 'Dashboard Penilai Universitas')

@section('description', 'Dashboard untuk Penilai Universitas - Sistem Kepegawaian UNMUL')

@push('styles')
<style>
    /* Penilai specific styles */
    .penilai-dashboard {
        /* Custom styles for penilai dashboard */
    }

    .assessment-card {
        transition: all 0.3s ease;
    }

    .assessment-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .score-input {
        border: 2px solid #e5e7eb;
        border-radius: 0.375rem;
        padding: 0.5rem;
        transition: border-color 0.2s;
    }

    .score-input:focus {
        border-color: #3b82f6;
        outline: none;
    }
</style>
@endpush
