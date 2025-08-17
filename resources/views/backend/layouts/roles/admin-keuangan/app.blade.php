@extends('backend.layouts.base')

@section('content')
    @yield('content')
@endsection

@push('styles')
    <!-- Admin Keuangan specific styles -->
    <style>
        :root {
            --admin-keuangan-primary: #f59e0b;
            --admin-keuangan-secondary: #fbbf24;
            --admin-keuangan-accent: #92400e;
        }

        .admin-keuangan-theme {
            --tw-text-opacity: 1;
            color: rgb(146 64 14 / var(--tw-text-opacity));
        }

        .admin-keuangan-bg {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }

        .admin-keuangan-card {
            background: rgba(251, 191, 36, 0.1);
            border: 1px solid rgba(251, 191, 36, 0.2);
        }
    </style>
@endpush

@push('scripts')
    <!-- Admin Keuangan specific scripts -->
    <script>
        // Admin Keuangan specific functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Admin Keuangan Dashboard loaded');
        });
    </script>
@endpush
