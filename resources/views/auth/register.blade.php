<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - {{ config('app.name', 'Core Values') }}</title>

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
            position: absolute; width: 600px; height: 600px; background: rgba(236, 72, 153, 0.15); 
            border-radius: 50%; filter: blur(120px); top: -200px; right: -100px; z-index: 0;
        }
        .blob-2 {
            position: absolute; width: 500px; height: 500px; background: rgba(190, 24, 93, 0.15); 
            border-radius: 50%; filter: blur(120px); bottom: -100px; left: -100px; z-index: 0;
        }
        .input-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
            font-family: 'Outfit', sans-serif !important;
            font-weight: 400 !important;
        }
        .input-glass option {
            background-color: #1e293b;
            color: white;
            font-family: 'Outfit', sans-serif !important;
            font-weight: 400 !important;
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

    <div class="w-full max-w-md z-10 py-8">
        <!-- Logo or Header Area -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-white mb-2">Daftar Akun</h1>
            <p class="text-slate-400">Buat akun untuk memulai penilaian</p>
        </div>

        <!-- Register Form Card -->
        <div class="glass-panel rounded-2xl p-8 shadow-2xl relative overflow-hidden">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- 1. Name Input -->
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Nama Lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                           class="w-full input-glass rounded-xl px-4 py-3 text-base @error('name') border-red-500 @enderror" 
                           placeholder="Nama Anda">
                    @error('name')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 2. Branch & Division -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="branch_id" class="block text-sm font-medium text-slate-300 mb-2">Cabang</label>
                        <select id="branch_id" name="branch_id" required
                                class="w-full input-glass rounded-xl px-4 py-3 text-base @error('branch_id') border-red-500 @enderror appearance-none bg-slate-800 text-white cursor-pointer focus:ring-2 focus:ring-purple-500 outline-none">
                            <option value="" class="bg-slate-800 text-white">Pilih Cabang</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }} class="bg-slate-800 text-white">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="division_id" class="block text-sm font-medium text-slate-300 mb-2">Divisi</label>
                        <select id="division_id" name="division_id" required
                                class="w-full input-glass rounded-xl px-4 py-3 text-base @error('division_id') border-red-500 @enderror appearance-none bg-slate-800 text-white cursor-pointer focus:ring-2 focus:ring-purple-500 outline-none">
                            <option value="" class="bg-slate-800 text-white">Pilih Divisi</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }} class="bg-slate-800 text-white">{{ $division->name }}</option>
                            @endforeach
                        </select>
                        @error('division_id')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- 3. Position Input -->
                <div class="mb-5">
                    <label for="position_id" class="block text-sm font-medium text-slate-300 mb-2">Jabatan</label>
                    <select id="position_id" name="position_id"
                            class="w-full input-glass rounded-xl px-4 py-3 text-base @error('position_id') border-red-500 @enderror appearance-none bg-slate-800 text-white cursor-pointer focus:ring-2 focus:ring-purple-500 outline-none">
                        <option value="" class="bg-slate-800 text-white">Pilih Jabatan</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }} class="bg-slate-800 text-white">{{ $position->name }}</option>
                        @endforeach
                    </select>
                    @error('position_id')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 4. Email Input -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                           class="w-full input-glass rounded-xl px-4 py-3 text-base @error('email') border-red-500 @enderror" 
                           placeholder="nama@email.com">
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 5. Password Input -->
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Kata Sandi</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="new-password"
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
                
                <!-- 6. Password Confirm Input -->
                <div class="mb-6">
                    <label for="password-confirm" class="block text-sm font-medium text-slate-300 mb-2">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="w-full input-glass rounded-xl px-4 py-3 pr-12 text-base"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePassword('password-confirm')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-200">
                            <svg id="icon-password-confirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full btn-gradient text-white font-semibold rounded-xl px-4 py-3 shadow-lg">
                        Daftar
                    </button>
                </div>
                
                @if (Route::has('login'))
                <div class="mt-6 text-center text-sm text-slate-400">
                    Sudah punya akun? <a href="{{ route('login') }}" class="text-[#ec4899] font-semibold hover:text-[#f472b6] transition-colors">Masuk di sini</a>
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
