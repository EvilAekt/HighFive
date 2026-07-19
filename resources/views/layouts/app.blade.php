<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
          toast: { show: false, message: '', type: 'success' },
          showToast(msg, type = 'success') {
              this.toast.message = msg;
              this.toast.type = type;
              this.toast.show = true;
              setTimeout(() => { this.toast.show = false; }, 3000);
          }
      }"
      x-init="
          @if(session('success')) showToast('{{ session('success') }}', 'success'); @endif
          @if(session('error')) showToast('{{ session('error') }}', 'error'); @endif
      "
      :class="$store.theme.isDark ? 'dark' : ''"
      class="{{ auth()->check() && (auth()->user()->settings['theme'] ?? 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="HIGH FIVE - Premium Direct-to-Consumer Fashion Brand. Koleksi pakaian minimalis dan modern.">

    <title>@yield('title', 'HIGH FIVE - Premium Fashion')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Midtrans Snap -->
    @if(request()->routeIs('checkout.index') || request()->routeIs('orders.show'))
        <script src="{{ env('MIDTRANS_IS_PRODUCTION') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    @endif
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                isDark: {{ auth()->check() && isset(auth()->user()->settings['theme']) ? (auth()->user()->settings['theme'] === 'dark' ? 'true' : 'false') : 'localStorage.getItem(\'theme\') === \'dark\' || (!(\'theme\' in localStorage) && window.matchMedia(\'(prefers-color-scheme: dark)\').matches)' }},
                toggle() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                    if (this.isDark) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            });
        });
    </script>
</head>
<body class="font-sans antialiased bg-white text-primary-900 min-h-screen flex flex-col dark:bg-onyx-800 dark:text-gray-100 transition-colors duration-300">
    @include('components.header')

    <!-- Modern Toast Notification -->
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="fixed bottom-4 right-4 z-50 flex items-center p-4 mb-4 text-gray-500 bg-white shadow-lg border border-gray-100 dark:text-gray-400 dark:bg-onyx-800 dark:border-onyx-700" 
         role="alert" style="display: none;">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg"
             :class="toast.type === 'success' ? 'text-green-500 bg-green-100 dark:bg-green-800 dark:text-green-200' : 'text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200'">
            <i :data-lucide="toast.type === 'success' ? 'check-circle' : 'x-circle'" class="w-5 h-5"></i>
        </div>
        <div class="ms-3 text-sm font-semibold" x-text="toast.message"></div>
        <button type="button" @click="toast.show = false" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-onyx-800 dark:hover:bg-onyx-700">
            <span class="sr-only">Close</span>
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>

    <main class="flex-1 animate-fade-in">
        @yield('content')
    </main>

    @include('components.chatbot-fab')
    @include('components.back-to-top')

    @include('components.footer')

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
