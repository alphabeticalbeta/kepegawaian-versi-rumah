@extends('backend.layouts.base')

@section('title', $title ?? 'Admin Universitas - Kepegawaian Unmul')

@section('sidebar')
    @include('backend.components.sidebar-admin-universitas')
@endsection

@section('scripts')
    {{-- Custom scripts for Admin Universitas --}}
    <script>
        // Toggle submenu function for sidebar
        function toggleSubmenu(menuId) {
            const submenu = document.getElementById(menuId + '-submenu');
            const icon = document.getElementById(menuId + '-icon');

            if (submenu.classList.contains('hidden')) {
                submenu.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                submenu.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // Initialize submenu states based on current route
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we're on a periode route and expand the submenu
            @if(request()->routeIs('admin-universitas.periode-usulan.*') || request()->routeIs('admin-universitas.dashboard-usulan.*'))
                const periodeSubmenu = document.getElementById('periode-submenu');
                const periodeIcon = document.getElementById('periode-icon');
                if (periodeSubmenu && periodeIcon) {
                    periodeSubmenu.classList.remove('hidden');
                    periodeIcon.style.transform = 'rotate(180deg)';
                }
            @endif
        });
    </script>
@endsection

@section('styles')
    {{-- Custom styles for Admin Universitas --}}
    <style>
        .admin-universitas-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .periode-card {
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .periode-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
@endsection
