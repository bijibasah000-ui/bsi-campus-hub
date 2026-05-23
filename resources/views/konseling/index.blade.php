@extends('layouts.app')
@section('title', 'Konseling Chat')
@section('page-title', 'Konseling Chat')

@section('content')
<div class="chat-wrap">

    {{-- Header konselor --}}
    <div class="chat-header">
        <div class="chat-av" style="background:linear-gradient(135deg,#4F46E5,#7C3AED); color:white; font-size:16px;">🧑‍💼</div>
        <div style="flex:1">
            <div style="font-size:15px; font-weight:700; color:var(--text);">Kak Sari — Konselor BSI</div>
            <div class="chat-status">&#9679; AI Konselor Online &bull; Didukung Gemini AI</div>
        </div>
        <div style="font-size:11px; color:var(--text-3); background:var(--bg); padding:5px 12px; border-radius:12px; border:1px solid var(--border);">
            🔒 Percakapan bersifat rahasia
        </div>
    </div>

    {{-- Area pesan --}}
    <div class="chat-messages" id="chatMessages">
        {{-- Pesan sambutan awal --}}
        <div class="msg bot">
            <div class="msg-av" style="background:linear-gradient(135deg,#4F46E5,#7C3AED); color:white;">🧑‍💼</div>
            <div class="bubble">
                Halo! Aku Kak Sari, konselor mahasiswa BSI Campus Hub 😊<br><br>
                Aku di sini untuk membantu kamu melewati berbagai tantangan kuliah — mulai dari tekanan akademik, deadline menumpuk, hingga masalah motivasi belajar.<br><br>
                Ceritakan saja apa yang sedang kamu rasakan. Semua percakapan di sini bersifat rahasia. 💙
            </div>
        </div>
    </div>

    {{-- Typing indicator --}}
    <div id="typingIndicator" style="display:none; padding:8px 0; margin-bottom:4px;">
        <div style="display:flex; align-items:center; gap:9px;">
            <div class="msg-av" style="background:linear-gradient(135deg,#4F46E5,#7C3AED); color:white; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px;">🧑‍💼</div>
            <div style="background:var(--white); border:1px solid var(--border); border-radius:14px; padding:10px 16px; display:flex; gap:4px; align-items:center;">
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
            </div>
        </div>
    </div>

    {{-- Input area --}}
    <div class="chat-input-row">
        <input type="text" id="chatInput" placeholder="Ceritakan apa yang kamu rasakan..." autocomplete="off">
        <button class="btn-send" id="chatSend">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="margin-right:6px;"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            Kirim
        </button>
    </div>

</div>
@endsection

@push('styles')
<style>
/* Typing animation */
.typing-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: var(--text-3);
    display: inline-block;
    animation: typingBounce 1.2s infinite ease-in-out;
}
.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes typingBounce {
    0%, 60%, 100% { transform: translateY(0); opacity:.5; }
    30%            { transform: translateY(-6px); opacity:1; }
}

/* Bubble AI support markdown-like line break */
.bubble { white-space: pre-line; }

/* Send button icon alignment */
.btn-send { display:flex; align-items:center; justify-content:center; }
</style>
@endpush

@push('scripts')
<script>
// Token CSRF untuk request POST ke Laravel
var CSRF_TOKEN = "{{ csrf_token() }}";
// URL endpoint backend
var CHAT_URL   = "{{ route('konseling.chat') }}";
</script>
<script src="{{ asset('js/konseling.js') }}"></script>
@endpush
