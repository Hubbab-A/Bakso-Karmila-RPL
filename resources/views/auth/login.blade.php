<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Kasir Bakso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        * { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-blue-50 flex items-center justify-center p-4">

    <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden flex min-h-[500px]">

        {{-- SISI KIRI - Biru Dekoratif --}}
        <div class="w-5/12 bg-blue-700 relative flex flex-col items-center justify-center p-10 overflow-hidden">

            {{-- Lingkaran dekoratif --}}
            <div class="absolute -top-10 -left-10 w-48 h-48 bg-blue-500 rounded-full opacity-60"></div>
            <div class="absolute top-16 -left-6 w-32 h-32 bg-blue-400 rounded-full opacity-40"></div>
            <div class="absolute -bottom-10 -right-10 w-56 h-56 bg-blue-600 rounded-full opacity-50"></div>
            <div class="absolute bottom-20 right-4 w-24 h-24 bg-blue-400 rounded-full opacity-40"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-40 h-40 bg-blue-500 rounded-full opacity-30"></div>

            {{-- Teks --}}
            <div class="relative z-10 text-center text-white">
                <div class="text-5xl mb-4"><img src="images/logoo.png" alt="Logo"></div>
                <h1 class="text-3xl font-bold tracking-wide">WELCOME</h1>
                <div class="w-12 h-1 bg-white/50 mx-auto my-3 rounded-full"></div>
                <p class="text-blue-100 text-sm font-medium uppercase tracking-widest">Sistem Kasir Bakso</p>
                <p class="text-blue-200 text-xs mt-3 leading-relaxed">
                    Kelola warung lebih mudah<br>dengan sistem kasir digital
                </p>
            </div>
        </div>

        {{-- SISI KANAN - Form Login --}}
        <div class="w-7/12 flex flex-col justify-center px-12 py-10">

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Sign in</h2>
                <p class="text-gray-400 text-sm mt-1">Masuk ke akun kasir Anda</p>
            </div>

            {{-- Error --}}
            @if($errors->any())
            <div class="bg-blue-50 border border-blue-200 text-blue-600 px-4 py-3 rounded-xl text-sm mb-5 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="/login" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div class="relative">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="User Name"
                        class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                {{-- Password --}}
                <div class="relative" x-data="{ show: false }">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <input :type="show ? 'text' : 'password'" name="password" required
                        placeholder="Password"
                        class="w-full pl-11 pr-16 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-4 flex items-center text-xs text-blue-500 font-medium hover:text-blue-700">
                        <span x-text="show ? 'HIDE' : 'SHOW'"></span>
                    </button>
                </div>

                <!-- {{-- Remember & Forgot --}}
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-gray-500 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                        Remember me
                    </label>
                    <a href="#" class="text-blue-500 hover:text-blue-700 font-medium">Forgot Password?</a>
                </div> -->

                {{-- Tombol Login --}}
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition shadow-md shadow-blue-200 text-sm tracking-wide">
                    Sign in
                </button>

                <!-- {{-- Divider --}}
                <div class="flex items-center gap-3">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-gray-400 text-xs">or</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div> -->

                <!-- {{-- Sign in with other --}}
                <button type="button"
                    class="w-full border border-gray-200 text-gray-600 font-medium py-3 rounded-xl hover:bg-gray-50 transition text-sm">
                    Sign in with other
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 mt-6">
                Default: <span class="text-gray-600">admin@bakso.com</span> / <span class="text-gray-600">password</span>
            </p> -->
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
