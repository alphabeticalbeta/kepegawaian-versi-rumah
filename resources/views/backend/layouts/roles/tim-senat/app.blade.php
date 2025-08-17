@extends('backend.layouts.base')

@section('content')
    @yield('content')
@endsection

@push('styles')
    <!-- Tim Senat specific styles -->
    <style>
        :root {
            --tim-senat-primary: #ea580c;
            --tim-senat-secondary: #fb923c;
            --tim-senat-accent: #9a3412;
        }

        .tim-senat-theme {
            --tw-text-opacity: 1;
            color: rgb(154 52 18 / var(--tw-text-opacity));
        }

        .tim-senat-bg {
            background: linear-gradient(135deg, #fb923c 0%, #ea580c 100%);
        }

        .tim-senat-card {
            background: rgba(251, 146, 60, 0.1);
            border: 1px solid rgba(251, 146, 60, 0.2);
        }
    </style>
@endpush

@push('scripts')
    <!-- Tim Senat specific scripts -->
    <script>
        // Tim Senat specific functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Tim Senat Dashboard loaded');
        });
    </script>
@endpush
