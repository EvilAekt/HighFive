<div x-data="{
    open: false,
    hasOpened: false,
    messages: [],
    newMessage: '',
    sending: false,
    pollInterval: null,
    greeting: '',
    init() {
        const hour = new Date().getHours();
        let timeGreeting = 'Selamat Pagi';
        if (hour >= 11 && hour < 15) timeGreeting = 'Selamat Siang';
        else if (hour >= 15 && hour < 18) timeGreeting = 'Selamat Sore';
        else if (hour >= 18) timeGreeting = 'Selamat Malam';
        
        this.greeting = timeGreeting + ' kak! Ada yang bisa kami bantu seputar koleksi HIGH FIVE?';

        setTimeout(() => {
            if(!this.hasOpened && this.messages.length === 0) {
                this.open = true;
                this.hasOpened = true;
            }
        }, 5000);
        
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
                }
            }
        } catch(e) {}
    },
    async sendMessage() {
        if(!this.newMessage.trim() || this.sending) return;
        this.sending = true;
        const tempMsg = this.newMessage;
        this.newMessage = '';
        
        this.messages.push({ content: tempMsg, is_admin: false, created_at: new Date().toISOString() });
        this.scrollToBottom();
        
        try {
            const formData = new FormData();
            formData.append('content', tempMsg);
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
    scrollToBottom() {
        this.$nextTick(() => {
            const container = this.$refs.chatContainer;
            if(container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    }
}" class="fixed bottom-6 right-6 z-[90] flex flex-col items-end pointer-events-none">

    <!-- Chat Window -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300 transform origin-bottom-right"
         x-transition:enter-start="opacity-0 translate-y-4 scale-50"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform origin-bottom-right"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-50"
         class="bg-white dark:bg-onyx-900 w-80 sm:w-[350px] shadow-2xl rounded-2xl border border-primary-200 dark:border-onyx-700 mb-4 overflow-hidden pointer-events-auto flex flex-col h-[450px] max-h-[80vh]"
         style="display: none;"
         @click.outside="open = false">
        
        <!-- Header -->
        <div class="bg-black dark:bg-white text-white dark:text-black p-4 flex items-center justify-between shadow-md z-10 relative">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-100 dark:bg-onyx-100 rounded-full flex items-center justify-center text-black">
                    <i data-lucide="headphones" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm tracking-widest uppercase">Live Chat</h4>
                    <p class="text-[10px] opacity-80 flex items-center gap-1 uppercase tracking-widest mt-0.5">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full inline-block animate-pulse"></span>
                        Admin Online
                    </p>
                </div>
            </div>
            <button @click="open = false" class="opacity-70 hover:opacity-100 transition-opacity">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Chat Area -->
        <div x-ref="chatContainer" class="flex-1 p-4 bg-primary-50 dark:bg-onyx-800 overflow-y-auto flex flex-col gap-3">
            
            <!-- Default Greeting -->
            <div class="flex items-end gap-2 max-w-[85%] self-start animate-fade-up">
                <div class="w-6 h-6 rounded-full bg-black dark:bg-white text-white dark:text-black flex-shrink-0 flex items-center justify-center">
                    <i data-lucide="bot" class="w-3.5 h-3.5"></i>
                </div>
                <div class="bg-white dark:bg-onyx-700 p-3 rounded-2xl rounded-bl-none shadow-sm border border-primary-100 dark:border-onyx-600">
                    <p class="text-xs text-primary-900 dark:text-white leading-relaxed" x-text="greeting"></p>
                </div>
            </div>

            <!-- Messages Loop -->
            <template x-for="msg in messages" :key="msg.id || msg.created_at">
                <div class="flex items-end gap-2 max-w-[85%]" :class="msg.is_admin ? 'self-start' : 'self-end flex-row-reverse'">
                    <div x-show="msg.is_admin" class="w-6 h-6 rounded-full bg-black dark:bg-white text-white dark:text-black flex-shrink-0 flex items-center justify-center">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                    </div>
                    <div class="p-3 rounded-2xl shadow-sm border"
                         :class="msg.is_admin ? 'bg-white dark:bg-onyx-700 border-primary-100 dark:border-onyx-600 rounded-bl-none' : 'bg-black dark:bg-white text-white dark:text-black border-black dark:border-white rounded-br-none'">
                        <p class="text-xs leading-relaxed" x-text="msg.content"></p>
                        <p class="text-[9px] mt-1 text-right opacity-60" 
                           x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})">
                        </p>
                    </div>
                </div>
            </template>
        </div>

        <!-- Input Area -->
        <div class="p-3 bg-white dark:bg-onyx-900 border-t border-primary-200 dark:border-onyx-700 z-10">
            <form @submit.prevent="sendMessage" class="relative flex items-center">
                <input type="text" 
                       x-model="newMessage"
                       placeholder="Ketik pesan Anda..." 
                       class="w-full bg-primary-50 dark:bg-onyx-800 border border-primary-200 dark:border-onyx-700 rounded-full py-2.5 pl-4 pr-12 text-sm text-black dark:text-white outline-none focus:border-black dark:focus:border-white transition-colors"
                       :disabled="sending"
                       autocomplete="off">
                <button type="submit" 
                        class="absolute right-1 top-1 bottom-1 w-8 flex items-center justify-center text-black dark:text-white bg-white dark:bg-onyx-900 rounded-full hover:bg-primary-100 dark:hover:bg-onyx-800 transition-colors disabled:opacity-50"
                        :disabled="!newMessage.trim() || sending">
                    <i data-lucide="send" class="w-4 h-4" x-show="!sending"></i>
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin" x-show="sending" style="display: none;"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- FAB Button -->
    <button @click="open = !open; hasOpened = true" 
            class="w-14 h-14 bg-black dark:bg-white text-white dark:text-black rounded-full flex items-center justify-center shadow-2xl hover:scale-110 transition-transform pointer-events-auto relative group z-[90]">
        <div class="absolute inset-0 bg-black dark:bg-white rounded-full animate-ping opacity-20"></div>
        <i data-lucide="message-square-text" class="w-6 h-6" x-show="!open"></i>
        <i data-lucide="x" class="w-6 h-6" x-show="open" style="display: none;"></i>
        
        <!-- Notification dot -->
        <span x-show="messages.filter(m => m.is_admin && !m.is_read).length > 0 && !open" 
              class="absolute top-0 right-0 w-3.5 h-3.5 bg-red-500 border-2 border-white rounded-full"></span>
    </button>
</div>
