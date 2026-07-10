<footer class="bg-black text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-white/10">
            <div class="md:col-span-2">
                <a href="{{ url('/') }}" class="text-2xl font-black tracking-tight uppercase">
                    HIGH<span class="font-thin">FIVE</span>
                </a>
                <p class="mt-4 text-white/50 max-w-sm text-sm leading-relaxed">
                    Premium Direct-to-Consumer fashion brand. Kami menyediakan pakaian berkualitas tinggi
                    dengan desain minimalis dan modern untuk gaya hidup Anda.
                </p>
            </div>

            <div>
                <h3 class="text-xs font-semibold uppercase tracking-widest mb-5 text-white/70">Shop</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('catalog', ['category' => 'atasan']) }}" class="text-sm text-white/50 hover:text-white transition-colors">
                            Atasan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('catalog', ['category' => 'bawahan']) }}" class="text-sm text-white/50 hover:text-white transition-colors">
                            Bawahan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('catalog', ['category' => 'outerwear']) }}" class="text-sm text-white/50 hover:text-white transition-colors">
                            Outerwear
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('catalog') }}" class="text-sm text-white/50 hover:text-white transition-colors">
                            Semua Produk
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-xs font-semibold uppercase tracking-widest mb-5 text-white/70">Bantuan</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('page.faq') }}" class="text-sm text-white/50 hover:text-white transition-colors">FAQ</a>
                    </li>
                    <li>
                        <a href="{{ route('page.shipping') }}" class="text-sm text-white/50 hover:text-white transition-colors">Pengiriman</a>
                    </li>
                    <li>
                        <a href="{{ route('page.returns') }}" class="text-sm text-white/50 hover:text-white transition-colors">Pengembalian</a>
                    </li>
                    <li>
                        <a href="{{ route('page.contact') }}" class="text-sm text-white/50 hover:text-white transition-colors">Hubungi Kami</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="pt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-xs text-white/30 uppercase tracking-widest">
                &copy; {{ date('Y') }} HIGH FIVE. All rights reserved.
            </p>
            <div class="flex gap-6 text-xs text-white/30 uppercase tracking-widest">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
