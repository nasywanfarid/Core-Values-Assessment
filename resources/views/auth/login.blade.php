<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name', 'Core Values') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        inter: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        body { 
            font-family: 'Outfit', sans-serif; 
            background: linear-gradient(rgba(15, 23, 42, 0.4), rgba(15, 23, 42, 0.5)), url('/images/bg-batching.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
        .glass-panel {
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }
        .blob-1 {
            position: absolute; width: 500px; height: 500px; background: rgba(236, 72, 153, 0.15); 
            border-radius: 50%; filter: blur(100px); top: -200px; left: -100px; z-index: 0;
        }
        .blob-2 {
            position: absolute; width: 400px; height: 400px; background: rgba(190, 24, 93, 0.15); 
            border-radius: 50%; filter: blur(100px); bottom: -100px; right: -100px; z-index: 0;
        }
        .input-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        .input-glass:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #ec4899;
            outline: none;
            box-shadow: 0 0 15px rgba(236, 72, 153, 0.2);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(236, 72, 153, 0.5);
            background: linear-gradient(135deg, #db2777 0%, #ec4899 100%);
        }
    </style>
</head>
<body class="text-white min-h-screen relative flex items-center justify-center p-4">

    <!-- Decorative Elements -->
    <div class="blob-1 opacity-20"></div>
    <div class="blob-2 opacity-20"></div>

    <div class="w-full max-w-md z-10">
        <!-- Logo or Header Area -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-white mb-2">Masuk</h1>
            <p class="text-slate-400">Silakan masuk ke akun Anda</p>
        </div>

        <!-- Login Form Card -->
        <div class="glass-panel rounded-2xl p-8 shadow-2xl relative overflow-hidden">
            @if(session('success'))
            <div class="mb-5 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Input -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                           class="w-full input-glass rounded-xl px-4 py-3 text-base @error('email') border-red-500 @enderror" 
                           placeholder="nama@email.com">
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-medium text-slate-300">Kata Sandi</label>
                    </div>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="w-full input-glass rounded-xl px-4 py-3 pr-12 text-base @error('password') border-red-500 @enderror"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-200">
                            <svg id="icon-password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-6 flex items-center">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-600 text-[#0ea5e9] bg-gray-700/50 focus:ring-[#0ea5e9] focus:ring-offset-gray-900">
                    <label for="remember" class="ml-2 block text-sm text-slate-300">
                        Ingat Saya
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full btn-gradient text-white font-semibold rounded-xl px-4 py-3 shadow-lg">
                        Masuk
                    </button>
                </div>
                
                @if (Route::has('register'))
                <div class="mt-6 text-center text-sm text-slate-400">
                    Belum punya akun? <a href="{{ route('register') }}" class="text-[#ec4899] font-semibold hover:text-[#f472b6] transition-colors">Daftar sekarang</a>
                </div>
                @endif
            </form>
        </div>
        
        <div class="text-center mt-6 text-sm text-slate-500">
            <a href="{{ url('/') }}" class="hover:text-slate-300 transition-colors">&larr; Kembali ke Beranda</a>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('icon-' + inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />';
            }
        }
    </script>
</body>
</html>
