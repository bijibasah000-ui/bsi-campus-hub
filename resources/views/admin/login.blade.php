{{-- resources/views/admin/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator — BSI Campus Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .admin-gradient {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
    </style>
</head>
<body class="admin-gradient min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-500 rounded-2xl mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Panel Administrator</h1>
            <p class="text-indigo-300 text-sm mt-1">BSI Campus Hub — Akses Terbatas</p>
        </div>

        {{-- Card Login --}}
        <div class="glass-card rounded-3xl p-8 shadow-2xl">

            @if(session('error'))
                <div class="bg-red-500/20 border border-red-400/30 rounded-xl px-4 py-3 mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-red-300 text-sm">{{ session('error') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-indigo-200 text-sm font-medium mb-2">Email Administrator</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-indigo-300/60 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition"
                        placeholder="admin@bsicampushub.ac.id">
                    @error('email')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-indigo-200 text-sm font-medium mb-2">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="adminPassword" required
                            class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-indigo-300/60 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition pr-12"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-3 text-indigo-300 hover:text-white transition">
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-500 hover:bg-indigo-600 active:bg-indigo-700 text-white font-semibold rounded-xl py-3 transition-all duration-200 shadow-lg hover:shadow-indigo-500/30 mt-2">
                    Masuk sebagai Administrator
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-white/10 text-center">
                <p class="text-indigo-400 text-sm">Bukan admin?</p>
                <a href="{{ route('login') }}" class="text-indigo-300 hover:text-white text-sm font-medium transition mt-1 inline-block">
                    ← Kembali ke Login Mahasiswa
                </a>
            </div>
        </div>

        <p class="text-center text-indigo-400/60 text-xs mt-6">
            BSI Campus Hub &copy; {{ date('Y') }} — Universitas BSI
        </p>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('adminPassword');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
