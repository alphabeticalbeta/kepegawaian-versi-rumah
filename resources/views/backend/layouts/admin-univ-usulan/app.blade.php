<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Kepegawaian UNMUL</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        /* Transisi untuk animasi yang lebih mulus */
        #sidebar, #main-content {
            transition: all 0.3s ease-in-out;
        }
        /* Tooltip sederhana saat sidebar di-collapse */
        [data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 10px;
            padding: 4px 8px;
            background-color: #1f2937;
            color: white;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 50;
        }

        /* Table styling improvements */
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

        /* Button styling */
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

        /* Card styling */
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
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen">
        @include('backend.components.sidebar-admin-universitas-usulan')

        <div id="main-content" class="flex-1 flex flex-col overflow-hidden ml-64">
            @include('backend.components.header')

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons
            lucide.createIcons();

            // Toggle sidebar function
            window.toggleSidebar = function() {
                const sidebar = document.getElementById('sidebar');
                const mainContent = document.getElementById('main-content');

                const isCollapsed = sidebar.classList.toggle('w-20');
                sidebar.classList.toggle('w-64', !isCollapsed);

                mainContent.style.marginLeft = isCollapsed ? '5rem' : '16rem';

                document.querySelectorAll('.sidebar-text').forEach(text => {
                    text.classList.toggle('hidden', isCollapsed);
                });

                if (isCollapsed) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.add('hidden');
                    });
                }
            };
        });
    </script>

</body>
</html>
