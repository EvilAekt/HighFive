@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-primary-900">Live Chat</h1>
        <p class="text-primary-500 mt-1">Kelola pesan dan pertanyaan dari pelanggan secara langsung.</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-primary-200 overflow-hidden flex w-full" style="height: calc(100vh - 160px);"
     x-data="{
         activeSession: null,
         messages: [],
         replyText: '',
         sending: false,
         pollInterval: null,
         chats: {{ Js::from($activeChats) }},
         
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
                     }
                 }
             } catch(e) {}
         },
         
         async sendReply() {
             if(!this.replyText.trim() || !this.activeSession || this.sending) return;
             
             this.sending = true;
             const tempText = this.replyText;
             this.replyText = '';
             
             this.messages.push({ content: tempText, is_admin: true, created_at: new Date().toISOString() });
             this.scrollToBottom();
             
             try {
                 const formData = new FormData();
                 formData.append('content', tempText);
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
                        <div class="flex items-end gap-2 max-w-[70%]" :class="msg.is_admin ? 'self-end flex-row-reverse ml-auto' : 'self-start mr-auto'">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                 :class="msg.is_admin ? 'bg-primary-900 text-white' : 'bg-white border text-primary-600'">
                                <i :data-lucide="msg.is_admin ? 'headphones' : 'user'" class="w-4 h-4"></i>
                            </div>
                            <div class="p-3 rounded-2xl shadow-sm border"
                                 :class="msg.is_admin ? 'bg-primary-900 text-white border-primary-900 rounded-br-none' : 'bg-white text-primary-900 border-primary-200 rounded-bl-none'">
                                <p class="text-sm leading-relaxed" x-text="msg.content"></p>
                                <p class="text-[10px] mt-1 opacity-70" :class="msg.is_admin ? 'text-right' : 'text-left'"
                                   x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></p>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Input Form -->
                <div class="p-4 bg-white border-t border-primary-200">
                    <form @submit.prevent="sendReply" class="flex gap-2">
                        <input type="text" 
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
