@extends('layouts.admin')

@section('content')
<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
    <div>
        <h1 class="text-2xl font-bold text-black uppercase tracking-[0.1em]">Live Chat</h1>
        <p class="text-gray-500 mt-1 text-sm">Kelola pesan dan pertanyaan dari pelanggan secara langsung.</p>
    </div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <!-- AI Toggle -->
        <div x-data="{ 
                active: {{ $aiActive ? 'true' : 'false' }},
                async toggle() {
                    try {
                        await fetch('{{ route('admin.chat.toggle', 'ai') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest' },
                            body: JSON.stringify({ active: this.active })
                        });
                    } catch(e) {}
                }
            }" class="flex items-center gap-3 bg-white border border-gray-200 px-3 py-2 w-full sm:w-auto">
            <label class="text-xs font-bold uppercase tracking-widest text-gray-500">Gemini AI</label>
            <div class="relative inline-block w-10 align-middle select-none transition duration-200 ease-in">
                <input type="checkbox" id="toggleAiChat" x-model="active" @change="toggle()" class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-2 border-gray-300 appearance-none cursor-pointer transition-transform duration-200 ease-in-out" :class="active ? 'translate-x-5 border-black' : 'translate-x-0'"/>
                <label for="toggleAiChat" class="toggle-label block overflow-hidden h-5 rounded-full cursor-pointer transition-colors duration-200 ease-in-out" :class="active ? 'bg-black' : 'bg-gray-300'"></label>
            </div>
            <span x-text="active ? 'ON' : 'OFF'" class="text-xs font-black w-6 text-center" :class="active ? 'text-black' : 'text-gray-400'"></span>
        </div>

        <!-- Bot Toggle -->
        <div x-data="{ 
                active: {{ $botActive ? 'true' : 'false' }},
                async toggle() {
                    try {
                        await fetch('{{ route('admin.chat.toggle', 'bot') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest' },
                            body: JSON.stringify({ active: this.active })
                        });
                    } catch(e) {}
                }
            }" class="flex items-center gap-3 bg-white border border-gray-200 px-3 py-2 w-full sm:w-auto">
            <label class="text-xs font-bold uppercase tracking-widest text-gray-500">Rule Bot</label>
            <div class="relative inline-block w-10 align-middle select-none transition duration-200 ease-in">
                <input type="checkbox" id="toggleBotChat" x-model="active" @change="toggle()" class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-2 border-gray-300 appearance-none cursor-pointer transition-transform duration-200 ease-in-out" :class="active ? 'translate-x-5 border-black' : 'translate-x-0'"/>
                <label for="toggleBotChat" class="toggle-label block overflow-hidden h-5 rounded-full cursor-pointer transition-colors duration-200 ease-in-out" :class="active ? 'bg-black' : 'bg-gray-300'"></label>
            </div>
            <span x-text="active ? 'ON' : 'OFF'" class="text-xs font-black w-6 text-center" :class="active ? 'text-black' : 'text-gray-400'"></span>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-primary-200 overflow-hidden flex w-full" style="height: calc(100vh - 160px);"
     x-data="{
         activeSession: null,
         messages: [],
         replyText: '',
         sending: false,
         pollInterval: null,
         replyingTo: null,
         selectedProduct: null,
         productModalOpen: false,
         searchQuery: '',
         chats: {{ Js::from($activeChats) }},
         products: {{ Js::from($products) }},
         
         selectSession(sessionId) {
             this.activeSession = sessionId;
             this.fetchMessages();
             
             if(this.pollInterval) clearInterval(this.pollInterval);
             this.pollInterval = setInterval(() => {
                 this.fetchMessages();
             }, 3000);
             
             const chat = this.chats.find(c => c.session_id === sessionId);
             if(chat) chat.unread_count = 0;
         },
         
         async fetchMessages() {
             if(!this.activeSession) return;
             try {
                 const res = await fetch(`/admin/chat/${this.activeSession}`);
                 if(res.ok) {
                     const data = await res.json();
                     if(JSON.stringify(data) !== JSON.stringify(this.messages)) {
                         this.messages = data;
                         this.scrollToBottom();
                         setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 50);
                     }
                 }
             } catch(e) {}
         },
         
         async sendReply() {
             if(!this.replyText.trim() || !this.activeSession || this.sending) return;
             
             this.sending = true;
             const tempText = this.replyText;
             const replyContext = this.replyingTo;
             const productContext = this.selectedProduct;
             this.replyText = '';
             this.replyingTo = null;
             this.selectedProduct = null;
             
             this.messages.push({ content: tempText, is_admin: true, created_at: new Date().toISOString(), reply_to: replyContext, product: productContext });
             this.scrollToBottom();
             setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 50);
             
             try {
                 const formData = new FormData();
                 formData.append('content', tempText);
                 if (replyContext) {
                     formData.append('reply_to_id', replyContext.id);
                 }
                 if (productContext) {
                     formData.append('product_id', productContext.id);
                 }
                 formData.append('_token', document.querySelector('meta[name=csrf-token]').content);
                 
                 await fetch(`/admin/chat/${this.activeSession}/reply`, {
                     method: 'POST',
                     body: formData,
                     headers: { 'X-Requested-With': 'XMLHttpRequest' }
                 });
                 
                 await this.fetchMessages();
             } catch(e) {} finally {
                 this.sending = false;
             }
         },
         
         async deleteMessage(id) {
             if(!confirm('Hapus pesan ini?')) return;
             try {
                 await fetch(`/admin/chat/messages/${id}`, {
                     method: 'DELETE',
                     headers: { 
                         'X-Requested-With': 'XMLHttpRequest',
                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                     }
                 });
                 // Hapus dari state lokal
                 this.messages = this.messages.filter(m => m.id !== id);
             } catch(e) {}
         },
         
         scrollToBottom() {
             this.$nextTick(() => {
                 const container = this.$refs.chatContainer;
                 if(container) {
                     container.scrollTop = container.scrollHeight;
                 }
             });
         }
     }">
    
    <!-- Left Sidebar: Session List -->
    <div class="w-80 flex-shrink-0 border-r border-primary-200 bg-primary-50 flex flex-col min-h-0">
        <div class="p-4 border-b border-primary-200 bg-white font-bold text-primary-900 flex items-center justify-between">
            <span>Daftar Obrolan Aktif</span>
            <span class="bg-primary-900 text-white text-xs px-2 py-0.5 rounded-full" x-text="chats.length"></span>
        </div>
        
        <div class="flex-1 overflow-y-auto p-2 space-y-1">
            <template x-if="chats.length === 0">
                <div class="text-center p-8 text-primary-400 text-sm">
                    Belum ada obrolan masuk.
                </div>
            </template>
            
            <template x-for="chat in chats" :key="chat.session_id">
                <button @click="selectSession(chat.session_id)" 
                        class="w-full text-left p-3 rounded-xl transition-all border flex gap-3"
                        :class="activeSession === chat.session_id ? 'bg-primary-900 text-white border-primary-900 shadow-md' : 'bg-white border-primary-100 hover:border-primary-300 text-primary-900'">
                    
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                         :class="activeSession === chat.session_id ? 'bg-white text-primary-900' : 'bg-primary-100 text-primary-600'">
                        <i data-lucide="user" class="w-5 h-5"></i>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start mb-0.5">
                            <h5 class="font-bold text-sm truncate" x-text="chat.user ? chat.user.name : 'Guest User'"></h5>
                            <span class="text-[10px] whitespace-nowrap opacity-70" 
                                  x-text="new Date(chat.last_activity).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                        </div>
                        <p class="text-xs truncate opacity-80" x-text="chat.latest_message"></p>
                    </div>
                    
                    <div x-show="chat.unread_count > 0" class="flex-shrink-0 flex items-center justify-center">
                        <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full" x-text="chat.unread_count"></span>
                    </div>
                </button>
            </template>
        </div>
    </div>
    
    <!-- Right Area: Chat Window -->
    <div class="flex-1 flex flex-col bg-white min-w-0 min-h-0">
        
        <template x-if="!activeSession">
            <div class="flex-1 flex flex-col items-center justify-center text-primary-400">
                <i data-lucide="message-square-dashed" class="w-16 h-16 mb-4 opacity-50"></i>
                <p>Pilih obrolan dari daftar untuk mulai membalas.</p>
            </div>
        </template>
        
        <template x-if="activeSession">
            <div class="flex-1 flex flex-col h-full w-full min-w-0 min-h-0">
                <!-- Header -->
                <div class="p-4 border-b border-primary-200 bg-white flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-600">
                        <i data-lucide="user" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-primary-900" x-text="chats.find(c => c.session_id === activeSession)?.user?.name || 'Guest User'"></h3>
                        <p class="text-xs text-primary-500 font-mono" x-text="'Session: ' + activeSession.substring(0, 8) + '...'"></p>
                    </div>
                </div>
                
                <!-- Messages -->
                <div x-ref="chatContainer" class="flex-1 overflow-y-auto p-6 bg-primary-50 space-y-4 min-h-0">
                    <template x-for="msg in messages" :key="msg.id || msg.created_at">
                        <div class="flex items-end gap-2 max-w-[85%] group" :class="msg.is_admin ? 'self-end flex-row-reverse ml-auto' : 'self-start mr-auto'">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                 :class="msg.is_admin ? 'bg-primary-900 text-white' : 'bg-white border text-primary-600'">
                                <i :data-lucide="msg.is_admin ? 'headphones' : 'user'" class="w-4 h-4"></i>
                            </div>
                            
                            <div class="flex items-center gap-2" :class="msg.is_admin ? 'flex-row' : 'flex-row-reverse'">
                                <div class="p-3 rounded-2xl shadow-sm border max-w-[400px]"
                                     :class="msg.is_admin ? 'bg-primary-900 text-white border-primary-900 rounded-br-none' : 'bg-white text-primary-900 border-primary-200 rounded-bl-none'">
                                    
                                    <!-- Reply Context -->
                                    <template x-if="msg.reply_to || msg.reply_to_id">
                                        <div class="mb-2 p-2 rounded-lg text-xs opacity-80 border-l-2" 
                                             :class="msg.is_admin ? 'bg-white/20 border-white text-white/90' : 'bg-primary-50 border-primary-500 text-primary-900'">
                                            <p class="font-bold mb-0.5 truncate" x-text="(msg.reply_to && msg.reply_to.is_admin) ? 'Admin' : 'Pelanggan'"></p>
                                            <p class="truncate" x-text="msg.reply_to ? msg.reply_to.content : 'Membalas pesan...'"></p>
                                        </div>
                                    </template>

                                    <p class="text-sm leading-relaxed" x-html="msg.content"></p>
                                    
                                    <!-- Product Card -->
                                    <template x-if="msg.product">
                                        <div class="mt-3 bg-white rounded-lg border border-primary-100 overflow-hidden shadow-sm p-2 flex gap-3 text-primary-900 w-full max-w-sm">
                                            <div class="w-16 h-16 bg-gray-100 rounded-md overflow-hidden flex-shrink-0">
                                                <img :src="msg.product.thumbnail ? (msg.product.thumbnail.startsWith('http') ? msg.product.thumbnail : (msg.product.thumbnail.startsWith('/') ? msg.product.thumbnail : '/' + msg.product.thumbnail)) : (msg.product.images && msg.product.images.length > 0 ? '/storage/' + msg.product.images[0].image_path : 'https://placehold.co/100')" 
                                                     class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-1 flex flex-col justify-center min-w-0">
                                                <p class="font-bold text-xs truncate" x-text="msg.product.name"></p>
                                                <p class="text-primary-600 font-bold text-xs mt-1" 
                                                   x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(msg.product.price)"></p>
                                            </div>
                                        </div>
                                    </template>

                                    <p class="text-[10px] mt-1 opacity-70" :class="msg.is_admin ? 'text-right' : 'text-left'"
                                       x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></p>
                                </div>

                                <!-- Actions (on hover) -->
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 bg-white border border-primary-200 shadow-sm rounded-full px-1.5 py-1"
                                     :class="msg.is_admin ? 'flex-row-reverse' : 'flex-row'">
                                     
                                    <!-- Reply Button -->
                                    <button @click="replyingTo = msg; $refs.adminReplyInput.focus()" class="p-1.5 rounded-full hover:bg-primary-100 text-primary-500" title="Balas">
                                        <i data-lucide="reply" class="w-3.5 h-3.5"></i>
                                    </button>

                                    <!-- More Options (3 dots) -->
                                    <div class="relative" x-data="{ menuOpen: false }" @click.outside="menuOpen = false">
                                        <button @click="menuOpen = !menuOpen" class="p-1.5 rounded-full hover:bg-primary-100 text-primary-500" title="Lebih banyak">
                                            <i data-lucide="more-horizontal" class="w-3.5 h-3.5"></i>
                                        </button>
                                        
                                        <div x-show="menuOpen" 
                                             x-transition.opacity
                                             class="absolute bottom-full mb-1 w-28 bg-white rounded-lg shadow-lg border border-primary-100 py-1 z-[100]"
                                             :class="msg.is_admin ? 'left-0' : 'right-0'"
                                             style="display: none;">
                                             
                                            <template x-if="msg.id">
                                                <button @click="deleteMessage(msg.id); menuOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-red-500 hover:bg-primary-50 flex items-center gap-2">
                                                    <i data-lucide="trash-2" class="w-3 h-3"></i> Hapus
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Input Form -->
                <div class="bg-white border-t border-primary-200 flex flex-col relative">
                    <!-- Replying to banner -->
                    <div x-show="replyingTo" class="px-6 py-2 bg-primary-50 flex items-center justify-between border-b border-primary-100 text-sm" style="display: none;">
                        <div class="flex-1 truncate flex items-center">
                            <i data-lucide="reply" class="w-4 h-4 mr-2 text-primary-500"></i>
                            <span class="font-bold mr-2 text-primary-900">Membalas:</span>
                            <span class="text-primary-700 truncate" x-text="replyingTo?.content"></span>
                        </div>
                        <button type="button" @click="replyingTo = null" class="ml-4 text-primary-400 hover:text-primary-900 p-1 rounded-full hover:bg-primary-200">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Selected Product banner -->
                    <div x-show="selectedProduct" class="px-6 py-2 bg-blue-50 flex items-center justify-between border-b border-blue-100 text-sm" style="display: none;">
                        <div class="flex-1 truncate flex items-center">
                            <i data-lucide="shopping-bag" class="w-4 h-4 mr-2 text-blue-500"></i>
                            <span class="font-bold mr-2 text-blue-900">Melampirkan Produk:</span>
                            <span class="text-blue-700 truncate" x-text="selectedProduct?.name"></span>
                        </div>
                        <button type="button" @click="selectedProduct = null" class="ml-4 text-blue-400 hover:text-blue-900 p-1 rounded-full hover:bg-blue-200">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Fixed Centered Modal for Product Selector -->
                    <template x-teleport="body">
                        <div x-show="productModalOpen" 
                             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                             x-transition.opacity
                             style="display: none;">
                            
                            <!-- Modal Content -->
                            <div @click.outside="productModalOpen = false" 
                                 class="bg-white rounded-2xl w-full max-w-4xl max-h-[85vh] flex flex-col shadow-2xl overflow-hidden"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-8 scale-95">
                                
                                <!-- Search Header -->
                                <div class="p-6 border-b border-gray-100 flex flex-col gap-6">
                                    <div class="relative">
                                        <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                                        <input type="text" x-model="searchQuery" 
                                               class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all text-sm"
                                               placeholder="Search products...">
                                        
                                        <button @click="productModalOpen = false" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-900">
                                            <i data-lucide="x" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                    <h2 class="font-bold text-gray-900">All Product</h2>
                                </div>
                                
                                <!-- Product Grid -->
                                <div class="flex-1 overflow-y-auto p-6 bg-gray-50/50">
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                        <template x-for="prod in products.filter(p => p.name.toLowerCase().includes(searchQuery.toLowerCase()))" :key="prod.id">
                                            <button type="button" @click="
                                                    selectedProduct = prod; 
                                                    const tempDiv = document.createElement('div');
                                                    tempDiv.innerHTML = prod.description || '';
                                                    let plainDesc = tempDiv.textContent || tempDiv.innerText || '';
                                                    plainDesc = plainDesc.substring(0, 80) + (plainDesc.length > 80 ? '...' : '');

                                                    replyText = `Halo kak! Cek produk ini: ${prod.name}. Harganya cuma Rp ${new Intl.NumberFormat('id-ID').format(prod.price)}. ${plainDesc} Yuk buruan dicek sebelum kehabisan!`;
                                                    productModalOpen = false;
                                                    $refs.adminReplyInput.focus();
                                                "
                                                class="group text-left bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary-500 relative flex flex-col">
                                                
                                                <!-- Image -->
                                                <div class="aspect-square bg-gray-100 w-full overflow-hidden relative">
                                                    <img :src="prod.thumbnail ? (prod.thumbnail.startsWith('http') ? prod.thumbnail : (prod.thumbnail.startsWith('/') ? prod.thumbnail : '/' + prod.thumbnail)) : (prod.images && prod.images.length > 0 ? '/storage/' + prod.images[0].image_path : 'https://placehold.co/300')" 
                                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                         
                                                    <!-- Flash Sale Tag (contoh) -->
                                                    <template x-if="prod.is_flash_sale">
                                                        <div class="absolute top-2 right-2 bg-red-600 text-white text-[10px] font-bold px-2 py-1 uppercase tracking-wider rounded">
                                                            Promo
                                                        </div>
                                                    </template>
                                                </div>
                                                
                                                <!-- Info -->
                                                <div class="p-4 flex-1 flex flex-col justify-center text-center gap-1.5">
                                                    <p class="text-xs font-bold text-gray-900 line-clamp-2 leading-tight uppercase tracking-wide" x-text="prod.name"></p>
                                                    <p class="text-xs font-bold text-gray-500" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(prod.price)"></p>
                                                </div>
                                            </button>
                                        </template>
                                        
                                        <!-- Empty State -->
                                        <div x-show="products.filter(p => p.name.toLowerCase().includes(searchQuery.toLowerCase())).length === 0" 
                                             class="col-span-full py-12 text-center text-gray-400">
                                            <i data-lucide="package-x" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                                            <p>Tidak ada produk yang cocok dengan pencarian.</p>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </template>

                    <form @submit.prevent="sendReply" class="p-4 flex gap-2 items-center">
                        <button type="button" @click="productModalOpen = !productModalOpen" 
                                class="p-3 text-primary-500 hover:text-primary-900 hover:bg-primary-100 rounded-xl transition-colors"
                                title="Lampirkan Produk">
                            <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                        </button>

                        <input type="text" 
                               x-ref="adminReplyInput"
                               x-model="replyText" 
                               class="flex-1 bg-primary-50 border border-primary-200 rounded-xl px-4 py-3 focus:border-primary-900 outline-none transition-colors"
                               placeholder="Ketik balasan untuk pelanggan..."
                               :disabled="sending"
                               autocomplete="off">
                        <button type="submit" 
                                class="bg-primary-900 hover:bg-black text-white px-6 py-3 rounded-xl font-bold transition-colors disabled:opacity-50 flex items-center gap-2"
                                :disabled="!replyText.trim() || sending">
                            <span>Kirim</span>
                            <i data-lucide="send" class="w-4 h-4" x-show="!sending"></i>
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin" x-show="sending" style="display: none;"></i>
                        </button>
                    </form>
                </div>
            </div>
        </template>
        
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:initialized', () => {
        Alpine.effect(() => {
            setTimeout(() => {
                lucide.createIcons();
            }, 100);
        });
    });
</script>
@endpush
