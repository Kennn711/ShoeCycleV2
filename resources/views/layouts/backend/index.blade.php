<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/upload/logo/logo.png') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/css/all.min.css') }}">

    @vite('resources/css/app.css')
</head>

<body class="bg-gray-50">
    <div class="flex flex-col min-h-screen">
        @include('layouts.backend.partial.sidebar')

        <!-- Main Content -->
        <div id="main-content" class="ml-64 transition-all duration-300 flex-1 flex flex-col">
            @include('layouts.backend.partial.navbar')

            <!-- Content Area -->
            <main class="pt-16 flex-1 flex flex-col">
                <div class="p-6 flex-1">
                    <!-- Breadcrumb -->
                    <div class="flex items-center gap-2 text-md mb-6">
                        <span id="breadcrumb" class="text-gray-900 font-medium">@yield('breadcrumb')</span>
                    </div>

                    <!-- Page Content -->
                    <div id="page-content" class="flex-1">
                        {{-- Content --}}
                        @yield('backend-content')
                    </div>
                </div>

                <!-- Footer - akan selalu di bawah -->
                <footer class="bg-white border-t border-gray-200 py-6 mt-auto">
                    <div class="px-6">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="text-sm text-gray-600">
                                © 2025 ShoeCycle. All rights reserved.
                            </div>
                            <div class="flex items-center gap-6 text-sm text-gray-600">
                                <a href="#" class="hover:text-blue-600">Privacy Policy</a>
                                <a href="#" class="hover:text-blue-600">Terms of Service</a>
                                <a href="#" class="hover:text-blue-600">Support</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/fontawesome/js/all.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>

    @stack('scripts')

    {{-- Sidebar --}}
    <script>
        // Tabel submenu toggle with animation
        const tabelToggle = document.getElementById("tabel-toggle");
        const tabelSubmenu = document.getElementById("tabel-submenu");
        const tabelIcon = document.getElementById("tabel-icon");

        if (tabelToggle && tabelSubmenu && tabelIcon) {
            let isSubmenuOpen = false;

            tabelToggle.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();

                isSubmenuOpen = !isSubmenuOpen;

                if (isSubmenuOpen) {
                    // Open submenu with smooth animation
                    tabelSubmenu.style.maxHeight = tabelSubmenu.scrollHeight + "px";
                    tabelIcon.style.transform = "rotate(90deg)";
                    tabelToggle.classList.add("bg-gray-50");
                } else {
                    // Close submenu with smooth animation
                    tabelSubmenu.style.maxHeight = "0";
                    tabelIcon.style.transform = "rotate(0deg)";
                    tabelToggle.classList.remove("bg-gray-50");
                }
            });
        }

        // Menu navigation - exclude parent menu
        const menuItems = document.querySelectorAll(".menu-item, .submenu-item");
        const breadcrumb = document.getElementById("breadcrumb");
    </script>
</body>

</html>
