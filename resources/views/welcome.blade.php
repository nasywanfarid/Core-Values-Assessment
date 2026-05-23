<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Penilaian Core Values - PT Bangun Anugrah Beton Nusantara</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        inter: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            500: '#ec4899', // pink-500
                            600: '#db2777',
                            900: '#831843',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { 
            font-family: 'Outfit', sans-serif; 
            background: linear-gradient(rgba(15, 23, 42, 0.35), rgba(15, 23, 42, 0.45)), url('/images/bg-batching.jpg');
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
        .text-gradient {
            background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        /* Background decorative blobs */
        .blob-1 {
            position: absolute; width: 600px; height: 600px; background: rgba(236, 72, 153, 0.15); 
            border-radius: 50%; filter: blur(100px); top: -200px; left: -100px;
        }
        .blob-2 {
            position: absolute; width: 500px; height: 500px; background: rgba(190, 24, 93, 0.15); 
            border-radius: 50%; filter: blur(100px); bottom: -100px; right: -100px;
        }
    </style>
</head>
<body class="text-white min-h-screen relative flex items-center justify-center py-10">

    <!-- Decorative Elements (Optional, kept subtle) -->
    <div class="blob-1 opacity-20"></div>
    <div class="blob-2 opacity-20"></div>

    <div class="container mx-auto px-4 z-10 relative">
        <div class="max-w-4xl mx-auto glass-panel rounded-3xl p-8 md:p-16 text-center shadow-2xl relative overflow-hidden">
            <!-- Subtle noise overlay -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>
            
            <!-- Content -->
            <div class="relative z-10">
                <div class="inline-block mb-6 px-5 py-2 rounded-full border border-white/10 bg-white/5 shadow-sm animate-float" style="animation-duration: 5s;">
                    <span class="text-sm font-semibold tracking-widest text-[#ec4899] uppercase">Sistem Informasi Penilaian Kinerja</span>
                </div>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 leading-tight tracking-tight text-white">
                    Selamat Datang Di <br />
                    <span class="text-gradient">Penilaian Core Values</span>
                </h1>
                
               <p class="text-xl md:text-2xl text-slate-600 font-light mb-12 tracking-wide">
                    PT BANGUN ANUGRAH BETON NUSANTARA
                </p> 
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-5">
                    @auth
                        <form id="force-login-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                            <input type="hidden" name="force_login" value="1">
                        </form>
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('force-login-form').submit();"
                           class="btn-gradient text-white font-semibold text-lg px-10 py-4 rounded-full shadow-lg flex items-center gap-3 group w-full sm:w-auto justify-center">
                            Mulai Penilaian
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-gradient text-white font-semibold text-lg px-10 py-4 rounded-full shadow-lg flex items-center gap-3 group w-full sm:w-auto justify-center">
                            Mulai Penilaian
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
        
        <!-- Footer / Credits -->
        <div class="text-center mt-12 text-slate-400 text-sm font-inter">
            &copy; {{ date('Y') }} PT Bangun Anugrah Beton Nusantara.
        </div>
    </div>
</body>
</html>
