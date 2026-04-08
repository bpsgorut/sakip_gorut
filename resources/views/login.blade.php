<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Login - e-SAKIP</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Container utama -->
        <div class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-6xl bg-white rounded-lg shadow-md p-4 sm:p-6">
                <div class="flex flex-col lg:flex-row">
                    <!-- Bagian kiri: Logo dan Ilustrasi -->
                    <div class="w-full lg:w-2/3 p-2 sm:p-4 order-2 lg:order-1">
                        <!-- Logo -->
                        <div class="flex items-center mb-6 lg:mb-10">
                            <img src="{{ asset('img/logo BPS.png') }}" alt="Badan Pusat Statistik" class="h-8 sm:h-10 lg:h-12 mr-3">
                            <div class="text-sm sm:text-base lg:text-lg font-semibold">Badan Pusat Statistik</div>
                        </div>

                        <!-- Ilustrasi -->
                        <div class="hidden lg:flex justify-center mb-6">
                            <img src="{{ asset('img/Ilustrasi Login.jpg') }}" alt="Ilustrasi" class="w-full max-w-md">
                        </div>

                        <!-- Tagline -->
                        <div class="hidden lg:block text-center text-base font-bold text-gray-800 mt-4">
                            Mewujudkan data statistik berkualitas, untuk Indonesia Maju
                        </div>
                    </div>

                    <!-- Bagian kanan: Form Login -->
                    <div class="w-full lg:w-1/2 p-2 sm:p-4 flex flex-col justify-center order-1 lg:order-2">
                        <h2 class="text-xl sm:text-2xl font-bold mb-2 text-gray-800">Selamat datang di e-SAKIP!</h2>
                        <p class="text-gray-600 mb-6 sm:mb-8">Silakan login untuk melanjutkan</p>

                        {{-- Tampilan Error Global --}}
                        @if ($errors->any())
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                                role="alert">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
                            @csrf

                            <!-- Email -->
                            <div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                                        </svg>
                                    </div>
                                    <input type="email" id="email" name="email"
                                        class="bg-gray-200 text-gray-700 border border-gray-300 rounded-md pl-10 pr-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-transparent"
                                        placeholder="Masukkan email">
                                </div>
                            </div>

                            <!-- Password -->
                            <div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="password" id="password" name="password"
                                        class="bg-gray-200 text-gray-700 border border-gray-300 rounded-md pl-10 pr-12 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-transparent"
                                        placeholder="Masukkan kata sandi">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <button type="button" id="togglePassword" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg id="eyeSlashIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Remember Me -->
                            <div class="flex items-center">
                                <input type="checkbox" id="remember" name="remember"
                                    class="h-4 w-4 text-red-800 focus:ring-red-700 border-gray-300 rounded">
                                <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                            </div>

                            <!-- Lupa Password dan Tombol Login -->
                            <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
                                <a href="{{ route('lupa_password') }}" class="text-blue-500 hover:underline text-sm order-2 sm:order-1">Lupa password?</a>
                                <button type="submit"
                                    class="w-full sm:w-auto bg-[#A51D1F] hover:bg-red-800 text-white font-bold py-2 px-6 sm:px-8 rounded-md transition duration-300 order-1 sm:order-2">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeSlashIcon = document.getElementById('eyeSlashIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        });
    </script>
</body>

</html>
