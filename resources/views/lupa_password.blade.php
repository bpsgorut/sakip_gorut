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
    <title>Lupa Password - e-SAKIP</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Container utama -->
        <div class="flex-grow flex items-center justify-center px-4">
            <div class="w-full max-w-6xl bg-white rounded-lg shadow-md p-6">
                <div class="flex flex-row">
                    <!-- Bagian kiri: Logo dan Ilustrasi -->
                    <div class="w-full md:w-2/3 p-4">
                        <!-- Logo -->
                        <div class="flex mb-10">
                            <img src="{{ asset('img/logo BPS.png') }}" alt="Badan Pusat Statistik" class="h-12">
                            <div class="text-lg font-semibold">Badan Pusat Statistik</div>
                        </div>

                        <!-- Ilustrasi -->
                        <div class="flex justify-center mb-6">
                            <img src="{{ asset('img/Lupa Password.jpg') }}" alt="Ilustrasi Lupa Password" class="w-full max-w-md">
                        </div>

                        <!-- Tagline -->
                        <div class="text-center text-base font-bold text-gray-800 mt-4">
                            Mewujudkan data statistik berkualitas, untuk Indonesia Maju
                        </div>
                    </div>

                    <!-- Bagian kanan: Form Reset Password -->
                    <div class="w-full md:w-1/2 p-4 flex flex-col justify-center">
                        <h2 class="text-2xl font-bold mb-2 text-gray-800">Lupa Password?</h2>
                        <p class="text-gray-600 mb-8">Masukkan email Anda untuk mendapatkan kode verifikasi</p>

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

                        {{-- Success Message --}}
                        @if (session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                                role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Step 1: Email Form -->
                        <div id="emailStep" class="{{ session('step') == 'verify' ? 'hidden' : '' }}">
                            <form method="POST" action="{{ route('lupa_password.send_code') }}" class="space-y-6">
                                @csrf

                                <!-- Email -->
                                <div>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                            </svg>
                                        </div>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                                            class="bg-gray-200 text-gray-700 border border-gray-300 rounded-md pl-10 pr-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-transparent"
                                            placeholder="Masukkan email Anda" required>
                                    </div>
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Tombol Kirim Kode -->
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('login') }}" class="text-blue-500 hover:underline text-sm">Kembali ke Login</a>
                                    <button type="submit"
                                        class="bg-[#A51D1F] hover:bg-red-800 text-white font-bold py-2 px-8 rounded-md transition duration-300">Kirim Kode</button>
                                </div>
                            </form>
                        </div>

                        <!-- Step 2: Verification Code Form -->
                        <div id="verifyStep" class="{{ session('step') != 'verify' ? 'hidden' : '' }}">
                            <form method="POST" action="{{ route('lupa_password.verify_code') }}" class="space-y-6">
                                @csrf
                                <input type="hidden" name="email" value="{{ session('reset_email') }}">

                                <!-- Kode Verifikasi -->
                                <div>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M18 8a6 6 0 01-7.743 5.743L10 14l-0.257-0.257A6 6 0 1118 8zm-2 0a4 4 0 11-8 0 4 4 0 018 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <input type="text" id="verification_code" name="verification_code" maxlength="6"
                                            class="bg-gray-200 text-gray-700 border border-gray-300 rounded-md pl-10 pr-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-transparent text-center tracking-widest"
                                            placeholder="Masukkan kode 6 digit" required>
                                    </div>
                                    @error('verification_code')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-sm text-gray-600 mt-2">Kode verifikasi telah dikirim ke email: <strong>{{ session('reset_email') }}</strong></p>
                                </div>

                                <!-- Password Baru -->
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
                                            class="bg-gray-200 text-gray-700 border border-gray-300 rounded-md pl-10 pr-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-transparent"
                                            placeholder="Password baru" required>
                                    </div>
                                    @error('password')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Konfirmasi Password -->
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
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            class="bg-gray-200 text-gray-700 border border-gray-300 rounded-md pl-10 pr-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-800 focus:border-transparent"
                                            placeholder="Konfirmasi password baru" required>
                                    </div>
                                    @error('password_confirmation')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Tombol Reset Password -->
                                <div class="flex items-center justify-between">
                                    <button type="button" onclick="window.location.href='{{ route('lupa_password') }}'"
                                        class="text-blue-500 hover:underline text-sm">Kirim Ulang Kode</button>
                                    <button type="submit"
                                        class="bg-[#A51D1F] hover:bg-red-800 text-white font-bold py-2 px-8 rounded-md transition duration-300">Reset Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto format verification code input
        document.getElementById('verification_code')?.addEventListener('input', function(e) {
            // Remove any non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Limit to 6 characters
            if (this.value.length > 6) {
                this.value = this.value.slice(0, 6);
            }
        });

        // Auto submit when 6 digits entered
        document.getElementById('verification_code')?.addEventListener('keyup', function(e) {
            if (this.value.length === 6) {
                // Optional: auto submit form when 6 digits are entered
                // this.form.submit();
            }
        });
    </script>
</body>

</html>