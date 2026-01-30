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
    @stack('styles')
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
                                © 2026 ShoeCycle. All rights reserved.
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
    <script src="{{ asset('assets/vendor/sweetalert2/sweetalert.js') }}"></script>

    <script>
        $(document).ready(function() {
            const $input = $('#input-search-backend');
            const $dropdown = $('#search-results-backend');
            const $container = $('#results-container');
            let searchTimer;

            // Shortcut CTRL + K
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    $input.focus();
                }
            });

            // Close on ESC
            $input.on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $dropdown.addClass('hidden');
                    $input.blur();
                }
            });

            // AJAX Search
            $input.on('input focus', function() {
                const query = $(this).val().trim();
                if (query.length < 2) {
                    $dropdown.addClass('hidden');
                    return;
                }

                $dropdown.removeClass('hidden');
                $container.html('<div class="py-6 text-center text-slate-500"><i class="fas fa-spinner fa-spin mr-2"></i>Mencari...</div>');

                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    $.get("{{ route('backend.search-global') }}", {
                        q: query
                    }, function(data) {
                        if (data.length > 0) {
                            let html = '';
                            data.forEach(item => {
                                html += `
                        <a href="${item.url}" class="flex items-center gap-4 p-3 rounded-xl hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100 group">
                            <div class="w-10 h-10 bg-slate-100 rounded-lg overflow-hidden shrink-0">
                                <img src="${item.image}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">${item.category}</div>
                                <h4 class="text-sm font-bold text-slate-800 truncate">${item.name}</h4>
                                <p class="text-[11px] text-slate-500 font-medium">${item.price}</p>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 group-hover:text-blue-500 transition-all mr-2"></i>
                        </a>`;
                            });
                            $container.html(html);
                        } else {
                            $container.html('<div class="py-10 text-center text-slate-400 text-sm">Data tidak ditemukan.</div>');
                        }
                    });
                }, 300);
            });

            // Close when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#search-backend-wrapper').length) {
                    $dropdown.addClass('hidden');
                }
            });
        });
    </script>

    @stack('scripts')

    {{-- Sidebar --}}
    <script src="{{ asset('assets/scripts/sidebar-backend/sidebar.js') }}"></script>
</body>

</html>
