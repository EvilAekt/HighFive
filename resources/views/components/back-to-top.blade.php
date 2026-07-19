<div x-data="{ show: false }" 
     @scroll.window="show = window.pageYOffset > 500" 
     class="fixed bottom-24 right-6 sm:bottom-6 sm:right-24 z-40 pointer-events-none">
    
    <button @click="window.scrollTo({top: 0, behavior: 'smooth'})"
            x-show="show"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            style="display: none;"
            class="w-12 h-12 bg-black dark:bg-white text-white dark:text-black hover:bg-primary-900 dark:hover:bg-gray-200 flex items-center justify-center shadow-lg transition-colors pointer-events-auto border border-primary-800 dark:border-gray-200"
            aria-label="Back to Top">
        <i data-lucide="arrow-up" class="w-5 h-5"></i>
    </button>
</div>
