<div x-data="{
    open: false,
    hasOpened: false,
    messages: [],
    newMessage: '',
    sending: false,
    pollInterval: null,
    greeting: '',
    cartIsOpen: false,
    replyingTo: null,
    init() {
        const hour = new Date().getHours();
        let timeGreeting = 'Selamat Pagi';
        if (hour >= 11 && hour < 15) timeGreeting = 'Selamat Siang';
        else if (hour >= 15 && hour < 18) timeGreeting = 'Selamat Sore';
        else if (hour >= 18) timeGreeting = 'Selamat Malam';
        
        this.greeting = timeGreeting + ' kak! Ada yang bisa kami bantu seputar koleksi HIGH FIVE?';
        
        this.$watch('open', value => {
            if (value) {
                this.fetchMessages();
                this.pollInterval = setInterval(() => this.fetchMessages(), 3000);
            } else {
                if(this.pollInterval) clearInterval(this.pollInterval);
            }
        });
    },
    async fetchMessages() {
        try {
            const res = await fetch('/chat/messages');
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
    async sendMessage() {
        if(!this.newMessage.trim() || this.sending) return;
        this.sending = true;
        const tempMsg = this.newMessage;
        const replyContext = this.replyingTo;
        this.newMessage = '';
        this.replyingTo = null;
        
        this.messages.push({ content: tempMsg, is_admin: false, created_at: new Date().toISOString(), reply_to: replyContext });
        this.scrollToBottom();
        setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 50);
        
        try {
            const formData = new FormData();
            formData.append('content', tempMsg);
            if (replyContext) {
                formData.append('reply_to_id', replyContext.id);
            }
            formData.append('_token', document.querySelector('meta[name=csrf-token]').content);
            
            await fetch('/chat/send', {
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
            await fetch(`/chat/messages/${id}`, {
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
    async reportMessage(id) {
        if(!confirm('Laporkan pesan ini?')) return;
        try {
            const res = await fetch(`/chat/messages/${id}/report`, {
                method: 'POST',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                }
            });
            if(res.ok) {
                alert('Pesan telah dilaporkan.');
            }
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
}" 
@cart-toggled.window="cartIsOpen = $event.detail"
x-show="!cartIsOpen"
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0 translate-y-4"
x-transition:enter-end="opacity-100 translate-y-0"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100 translate-y-0"
x-transition:leave-end="opacity-0 translate-y-4"
class="fixed bottom-6 right-6 z-[90] flex flex-col items-end pointer-events-none">

    <!-- Chat Window -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300 transform origin-bottom-right"
         x-transition:enter-start="opacity-0 translate-y-4 scale-50"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform origin-bottom-right"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-50"
         class="bg-white/95 dark:bg-onyx-900/95 backdrop-blur-md w-80 sm:w-[350px] shadow-2xl rounded-3xl border border-black/5 dark:border-white/10 mb-4 overflow-hidden pointer-events-auto flex flex-col h-[500px] max-h-[75vh]"
         style="display: none;"
         @click.outside="open = false">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-gray-900 to-black dark:from-gray-100 dark:to-white text-white dark:text-black p-4 flex items-center justify-between shadow-sm z-10 relative">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 dark:bg-black/10 backdrop-blur-sm rounded-full flex items-center justify-center text-white dark:text-black border border-white/20 dark:border-black/10">
                    <i data-lucide="headphones" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm tracking-widest uppercase">Live Chat</h4>
                    <p class="text-[10px] opacity-90 flex items-center gap-1.5 uppercase tracking-widest mt-0.5 font-medium">
                        <span class="w-2 h-2 bg-green-400 rounded-full inline-block animate-pulse shadow-[0_0_8px_rgba(74,222,128,0.8)]"></span>
                        Admin Online
                    </p>
                </div>
            </div>
            <button @click="open = false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/20 dark:hover:bg-black/10 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Chat Area -->
        <div x-ref="chatContainer" class="flex-1 p-5 bg-gray-50/50 dark:bg-onyx-800/50 overflow-y-auto flex flex-col gap-4">
            
            <!-- Default Greeting -->
            <div class="flex items-end gap-2.5 max-w-[85%] self-start animate-fade-up">
                <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-gray-900 to-black dark:from-gray-100 dark:to-white text-white dark:text-black flex-shrink-0 flex items-center justify-center shadow-md">
                    <i data-lucide="bot" class="w-3.5 h-3.5"></i>
                </div>
                <div class="bg-white dark:bg-onyx-700 p-3.5 rounded-2xl rounded-bl-none shadow-sm border border-black/5 dark:border-white/5">
                    <p class="text-[13px] text-gray-800 dark:text-gray-200 leading-relaxed font-medium" x-text="greeting"></p>
                </div>
            </div>

            <!-- Messages Loop -->
            <template x-for="msg in messages" :key="msg.id || msg.created_at">
                <div class="flex items-end gap-2.5 max-w-[90%] group" :class="msg.is_admin ? 'self-start' : 'self-end flex-row-reverse'">
                    <div x-show="msg.is_admin" class="w-7 h-7 rounded-full bg-gradient-to-tr from-gray-900 to-black dark:from-gray-100 dark:to-white text-white dark:text-black flex-shrink-0 flex items-center justify-center shadow-md">
                        <i data-lucide="headphones" class="w-3.5 h-3.5"></i>
                    </div>
                    
                    <div class="flex items-center gap-2" :class="msg.is_admin ? 'flex-row' : 'flex-row-reverse'">
                        <div class="p-3.5 rounded-2xl shadow-sm relative max-w-[220px]"
                             :class="msg.is_admin ? 'bg-white dark:bg-onyx-700 border border-black/5 dark:border-white/5 rounded-bl-none text-gray-800 dark:text-gray-200' : 'bg-gradient-to-br from-gray-800 to-black dark:from-gray-200 dark:to-white text-white dark:text-black rounded-br-none'">
                            
                            <!-- Reply Context -->
                            <template x-if="msg.reply_to || msg.reply_to_id">
                                <div class="mb-2 p-2 rounded-lg text-[10px] opacity-80 border-l-2" 
                                     :class="msg.is_admin ? 'bg-gray-100 dark:bg-onyx-800 border-primary-500 text-gray-600 dark:text-gray-300' : 'bg-white/20 border-white text-white/90 dark:text-black/90'">
                                    <p class="font-bold mb-0.5 truncate" x-text="(msg.reply_to && msg.reply_to.is_admin) ? 'Admin' : 'Anda'"></p>
                                    <p class="truncate" x-text="msg.reply_to ? msg.reply_to.content : 'Membalas pesan...'"></p>
                                </div>
                            </template>

                            <p class="text-[13px] leading-relaxed font-medium" x-html="msg.content"></p>
                            
                            <!-- Product Card -->
                            <template x-if="msg.product">
                                <div class="mt-2 bg-white dark:bg-onyx-800 rounded-lg overflow-hidden shadow-sm flex flex-col w-full min-w-[200px] border border-black/5 dark:border-white/5">
                                    <div class="w-full h-32 bg-gray-100 dark:bg-onyx-900 relative">
                                        <img :src="msg.product.thumbnail ? (msg.product.thumbnail.startsWith('http') ? msg.product.thumbnail : (msg.product.thumbnail.startsWith('/') ? msg.product.thumbnail : '/' + msg.product.thumbnail)) : (msg.product.images && msg.product.images.length > 0 ? '/storage/' + msg.product.images[0].image_path : 'https://placehold.co/200')" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    <div class="p-3 flex flex-col gap-1">
                                        <p class="font-bold text-xs truncate text-gray-900 dark:text-gray-100" x-text="msg.product.name"></p>
                                        <p class="text-primary-600 dark:text-primary-400 font-bold text-xs" 
                                           x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(msg.product.price)"></p>
                                        <a :href="'/product/' + msg.product.id" class="mt-2 text-center text-[10px] font-bold text-white bg-black dark:bg-white dark:text-black py-1.5 rounded-md hover:opacity-80 transition-opacity">
                                            Lihat Produk
                                        </a>
                                    </div>
                                </div>
                            </template>

                            <p class="text-[9px] mt-1.5 opacity-70 tracking-wide font-medium" 
                               :class="msg.is_admin ? 'text-right' : 'text-right'"
                               x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})">
                            </p>
                        </div>

                        <!-- Actions (on hover) -->
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 bg-white dark:bg-onyx-800 shadow-sm border border-gray-100 dark:border-onyx-700 rounded-full px-1.5 py-1"
                             :class="msg.is_admin ? 'flex-row-reverse' : 'flex-row'">
                             
                            <!-- Reply Button -->
                            <button @click="replyingTo = msg; $refs.chatInput.focus()" class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-onyx-700 text-gray-500 dark:text-gray-400" title="Balas">
                                <i data-lucide="reply" class="w-3.5 h-3.5"></i>
                            </button>

                            <!-- More Options (3 dots) -->
                            <div class="relative" x-data="{ menuOpen: false }" @click.outside="menuOpen = false">
                                <button @click="menuOpen = !menuOpen" class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-onyx-700 text-gray-500 dark:text-gray-400" title="Lebih banyak">
                                    <i data-lucide="more-horizontal" class="w-3.5 h-3.5"></i>
                                </button>
                                
                                <div x-show="menuOpen" 
                                     x-transition.opacity
                                     class="absolute bottom-full mb-1 w-28 bg-white dark:bg-onyx-800 rounded-lg shadow-lg border border-gray-100 dark:border-onyx-700 py-1 z-[100]"
                                     :class="msg.is_admin ? 'left-0' : 'right-0'"
                                     style="display: none;">
                                     
                                    <template x-if="!msg.is_admin && msg.id">
                                        <button @click="deleteMessage(msg.id); menuOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-red-500 hover:bg-gray-50 dark:hover:bg-onyx-700 flex items-center gap-2">
                                            <i data-lucide="trash-2" class="w-3 h-3"></i> Hapus
                                        </button>
                                    </template>
                                    
                                    <template x-if="msg.is_admin && msg.id">
                                        <button @click="reportMessage(msg.id); menuOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-onyx-700 flex items-center gap-2">
                                            <i data-lucide="flag" class="w-3 h-3"></i> Laporkan
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Input Area -->
        <div class="bg-white/90 dark:bg-onyx-900/90 backdrop-blur-md border-t border-black/5 dark:border-white/5 z-10 flex flex-col">
            <!-- Replying to banner -->
            <div x-show="replyingTo" class="px-4 py-2 bg-gray-50 dark:bg-onyx-800 flex items-center justify-between border-b border-black/5 dark:border-white/5 text-[11px]" style="display: none;">
                <div class="flex-1 truncate flex items-center">
                    <i data-lucide="reply" class="w-3 h-3 mr-1.5 text-primary-500"></i>
                    <span class="font-bold mr-1 text-primary-600 dark:text-primary-400">Membalas:</span>
                    <span class="text-gray-600 dark:text-gray-300 truncate" x-text="replyingTo?.content"></span>
                </div>
                <button @click="replyingTo = null" class="ml-2 text-gray-400 hover:text-black dark:hover:text-white p-1 rounded-full hover:bg-gray-200 dark:hover:bg-onyx-700">
                    <i data-lucide="x" class="w-3 h-3"></i>
                </button>
            </div>
            
            <form @submit.prevent="sendMessage" class="p-4 relative flex items-center">
                <input type="text" 
                       x-ref="chatInput"
                       x-model="newMessage"
                       placeholder="Ketik pesan Anda..." 
                       class="w-full bg-gray-100 dark:bg-onyx-800 border-none rounded-full py-3 pl-5 pr-12 text-[13px] font-medium text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-black dark:focus:ring-white transition-all shadow-inner outline-none"
                       :disabled="sending"
                       autocomplete="off">
                <button type="submit" 
                        class="absolute right-5 top-5 bottom-1.5 w-9 h-9 flex items-center justify-center text-white dark:text-black bg-black dark:bg-white rounded-full hover:scale-105 transition-transform disabled:opacity-50 disabled:hover:scale-100 shadow-md"
                        :disabled="!newMessage.trim() || sending">
                    <i data-lucide="send" class="w-4 h-4 ml-0.5" x-show="!sending"></i>
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin" x-show="sending" style="display: none;"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- FAB Button -->
    <button @click="open = !open; hasOpened = true" 
            class="w-14 h-14 bg-gradient-to-tr from-gray-900 to-black dark:from-gray-100 dark:to-white text-white dark:text-black rounded-full flex items-center justify-center shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 pointer-events-auto relative group z-[90]">
        <div class="absolute inset-0 bg-black dark:bg-white rounded-full animate-ping opacity-25"></div>
        <i data-lucide="message-square-text" class="w-6 h-6 transition-transform group-hover:scale-110" x-show="!open"></i>
        <i data-lucide="x" class="w-6 h-6 transition-transform group-hover:rotate-90" x-show="open" style="display: none;"></i>
        
        <!-- Notification dot -->
        <span x-show="messages.filter(m => m.is_admin && !m.is_read).length > 0 && !open" 
              class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 border-2 border-white dark:border-onyx-900 rounded-full animate-bounce"></span>
    </button>
</div>
