@extends('components.master')

@section('title', 'Profil Pengguna')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-full mx-auto">
        <!-- Header Section -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Profil Saya</h1>
            <p class="text-gray-600">Kelola dan perbarui informasi pribadi Anda</p>
        </div>

        <!-- Profile Container -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 ">
            <!-- Profile Card -->
            <div class="lg:col-span-1 bg-white rounded-xl shadow-md border border-gray-100 p-6 text-center transition-all duration-300 hover:shadow-lg relative overflow-hidden flex flex-col justify-center items-center min-h-full">
                <!-- Background Image -->
                <div class="absolute inset-0 opacity-20 bg-cover bg-center z-0" style="background-image: url('{{ asset('img/bg2.jpg') }}')"></div>
                
                <div class="relative z-10">
                    <div class="relative inline-block mb-6">
                        <div class="w-48 h-48 mx-auto rounded-full border-4 border-red-500 overflow-hidden shadow-md relative group cursor-pointer" id="profileImageContainer">
                            <img id="profileImage" 
                                 src="{{ $user->profile_picture ? asset('storage/profile_pictures/' . $user->profile_picture) : asset('img/default-avatar.svg') }}" 
                                 alt="Foto Profil" 
                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 flex items-center justify-center transition-all duration-300">
                                <span class="text-white opacity-0 group-hover:opacity-100 text-sm">Ganti Foto</span>
                            </div>
                        </div>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-600 mb-4">{{ $user->jabatan }}</p>
                    
                    <div class="inline-flex items-center space-x-2 bg-red-50 px-4 py-1.5 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        <span class="text-sm text-red-700">
                            {{ $user->role->role_name }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Detail Informasi -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-md border border-gray-100 p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informasi Personal -->
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Informasi Personal
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Nama Lengkap</label>
                                <div class="text-sm font-medium text-gray-900" data-field="name">{{ $user->name }}</div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">NIP</label>
                                <div class="text-sm font-medium text-gray-900">{{ $user->nip ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Kontak -->
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Informasi Kontak
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Email</label>
                                <div class="text-sm font-medium text-gray-900" data-field="email">{{ $user->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Pekerjaan -->
                <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Informasi Pekerjaan
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Jabatan</label>
                            <div class="text-sm font-medium text-gray-900">{{ $user->jabatan }}</div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Bidang</label>
                            <div class="text-sm font-medium text-gray-900">{{ $user->bidang ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 mt-6">
                    <button id="btnEditProfil" class="flex items-center bg-red-500 hover:bg-red-600 text-white px-6 py-2.5 rounded-lg transition-colors text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Profil
                    </button>
                    <button id="btnGantiPassword" class="flex items-center bg-gray-200 text-gray-800 hover:bg-gray-300 px-6 py-2.5 rounded-lg transition-colors text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Ganti Password
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ganti Password -->
<div id="modalGantiPassword" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">Ganti Password</h2>
            <button id="btnClosePassword" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="formGantiPassword" action="{{ route('profile.update-password') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
                <div class="relative">
                    <input type="password" id="current_password" name="current_password" required 
                           class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 toggle-password" data-target="current_password">
                        <svg class="h-5 w-5 text-gray-400 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="h-5 w-5 text-gray-400 eye-slash-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                        </svg>
                    </button>
                </div>
                <p id="current_passwordError" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                <div class="relative">
                    <input type="password" id="new_password" name="new_password" required 
                           class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 toggle-password" data-target="new_password">
                        <svg class="h-5 w-5 text-gray-400 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="h-5 w-5 text-gray-400 eye-slash-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-2">Min. 8 karakter, kombinasi huruf besar, kecil, dan angka</p>
                <p id="new_passwordError" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>
            <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" required 
                           class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 toggle-password" data-target="new_password_confirmation">
                        <svg class="h-5 w-5 text-gray-400 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="h-5 w-5 text-gray-400 eye-slash-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                        </svg>
                    </button>
                </div>
                <p id="new_password_confirmationError" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" id="btnBatalPassword" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ganti Foto Profil -->
<div id="modalGantiFoto" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">Ganti Foto Profil</h2>
            <button id="btnCloseFoto" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="formGantiFoto" class="p-6 space-y-4" enctype="multipart/form-data">
            @csrf
            <div class="flex flex-col items-center space-y-4">
                <div class="w-64 h-64 rounded-xl overflow-hidden border-2 border-gray-300">
                    <img id="previewFoto" src="{{ $user->profile_picture ? asset('storage/profile_pictures/' . $user->profile_picture) : asset('img/default-avatar.svg') }}" 
                         alt="Preview Foto" 
                         class="w-full h-full object-cover">
                </div>
                <div class="flex space-x-3">
                    <input type="file" id="inputFoto" name="profile_picture" accept="image/jpeg,image/jpg,image/png,image/gif" class="hidden" />
                    <button type="button" id="btnPilihFoto" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Pilih Foto
                    </button>
                    <button type="button" id="btnHapusFoto" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Hapus Foto
                    </button>
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" id="btnBatalFoto" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Foto -->
<div id="modalKonfirmasiHapus" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">Konfirmasi Hapus Foto</h2>
            <button id="btnCloseKonfirmasi" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="flex items-center space-x-4 mb-6">
                <div class="flex-shrink-0">
                    <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Hapus Foto Profil</h3>
                    <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus foto profil? Foto akan diganti dengan avatar default.</p>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" id="btnBatalHapus" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <button type="button" id="btnKonfirmasiHapus" class="px-4 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profil -->
<div id="modalEditProfil" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">Edit Profil</h2>
            <button id="btnCloseEditProfil" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="formEditProfil" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                <div class="relative">
                    <input type="text" id="edit_name" name="name" value="{{ $user->name }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                </div>
                <p id="nameError" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>
            <div>
                <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <div class="relative">
                    <input type="email" id="edit_email" name="email" value="{{ $user->email }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                </div>
                <p id="emailError" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>
            <div>
                <label for="edit_nip" class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                <div class="relative">
                    <input type="text" id="edit_nip" name="nip" value="{{ $user->nip }}" 
                           maxlength="18" pattern="[0-9]{18}" 
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 18)" 
                           placeholder="Masukkan 18 digit NIP"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                </div>
                <p id="nipError" class="text-red-500 text-xs mt-1 hidden"></p>
                <p class="text-gray-500 text-xs mt-1">NIP harus terdiri dari 18 digit angka</p>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" id="btnBatalEditProfil" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnGantiPassword = document.getElementById('btnGantiPassword');
    const modalGantiPassword = document.getElementById('modalGantiPassword');
    const btnClosePassword = document.getElementById('btnClosePassword');
    const btnBatalPassword = document.getElementById('btnBatalPassword');
    const formGantiPassword = document.getElementById('formGantiPassword');

    // Toggle modal ganti password
    btnGantiPassword.addEventListener('click', () => {
        modalGantiPassword.classList.remove('hidden');
        modalGantiPassword.classList.add('flex');
    });

    // Tutup modal
    [btnClosePassword, btnBatalPassword].forEach(btn => {
        btn.addEventListener('click', () => {
            modalGantiPassword.classList.remove('flex');
            modalGantiPassword.classList.add('hidden');
            formGantiPassword.reset();
        });
    });

    // Tutup modal saat klik di luar
    modalGantiPassword.addEventListener('click', (event) => {
        if (event.target === modalGantiPassword) {
            modalGantiPassword.classList.remove('flex');
            modalGantiPassword.classList.add('hidden');
            formGantiPassword.reset();
        }
    });

    // Toggle Password Visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            const eyeIcon = this.querySelector('.eye-icon');
            const eyeSlashIcon = this.querySelector('.eye-slash-icon');
            
            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                targetInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        });
    });

    // Handle form submit
    formGantiPassword.addEventListener('submit', function(e) {
        e.preventDefault();

        // Reset error messages
        document.querySelectorAll('.text-red-500').forEach(el => el.classList.add('hidden'));

        // Get submit button and disable it
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';

        // Collect form data
        const formData = new FormData(formGantiPassword);

        // Submit form via fetch
        fetch(formGantiPassword.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success - show notification and close modal
                showSuccess('Password berhasil diubah');
                modalGantiPassword.classList.remove('flex');
                modalGantiPassword.classList.add('hidden');
                formGantiPassword.reset();
            } else {
                // Show validation errors
                if (data.errors) {
                    for (const [field, message] of Object.entries(data.errors)) {
                        const errorElement = document.getElementById(`${field}Error`);
                        if (errorElement) {
                            errorElement.textContent = Array.isArray(message) ? message[0] : message;
                            errorElement.classList.remove('hidden');
                        }
                    }
                } else {
                    showError(data.message || 'Terjadi kesalahan');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Terjadi kesalahan pada server');
        })
        .finally(() => {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });

    const profileImageContainer = document.getElementById('profileImageContainer');
    const modalGantiFoto = document.getElementById('modalGantiFoto');
    const btnCloseFoto = document.getElementById('btnCloseFoto');
    const btnBatalFoto = document.getElementById('btnBatalFoto');
    const btnPilihFoto = document.getElementById('btnPilihFoto');
    const btnHapusFoto = document.getElementById('btnHapusFoto');
    const inputFoto = document.getElementById('inputFoto');
    const previewFoto = document.getElementById('previewFoto');
    const formGantiFoto = document.getElementById('formGantiFoto');
    const profileImage = document.getElementById('profileImage');
    const defaultAvatar = '{{ asset("img/default-avatar.svg") }}';

    // Buka modal ganti foto saat foto diklik
    profileImageContainer.addEventListener('click', () => {
        modalGantiFoto.classList.remove('hidden');
        modalGantiFoto.classList.add('flex');
        // Reset preview image to current profile picture when opening modal
        previewFoto.src = profileImage.src;
    });

    // Tutup modal
    [btnCloseFoto, btnBatalFoto].forEach(btn => {
        btn.addEventListener('click', () => {
            modalGantiFoto.classList.remove('flex');
            modalGantiFoto.classList.add('hidden');
            inputFoto.value = ''; // Reset input file
            previewFoto.src = profileImage.src; // Reset preview to current photo
        });
    });

    // Tutup modal saat klik di luar
    modalGantiFoto.addEventListener('click', (event) => {
        if (event.target === modalGantiFoto) {
            modalGantiFoto.classList.remove('flex');
            modalGantiFoto.classList.add('hidden');
            inputFoto.value = ''; // Reset input file
            previewFoto.src = profileImage.src; // Reset preview to current photo
        }
    });

    // Buka file picker saat tombol Pilih Foto diklik
    btnPilihFoto.addEventListener('click', () => {
        inputFoto.click();
    });

    // Event listener untuk tombol hapus foto
    btnHapusFoto.addEventListener('click', function() {
        modalKonfirmasiHapus.classList.remove('hidden');
        modalKonfirmasiHapus.classList.add('flex');
    });

    // Preview foto yang dipilih
    inputFoto.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewFoto.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Submit form ganti foto
    formGantiFoto.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch("{{ route('profile.update-photo') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update foto di halaman
                profileImage.src = data.photo_url;

                // Update foto di navbar
                const navbarProfileImages = document.querySelectorAll('#profile-button img, #dropdown-user img');
                navbarProfileImages.forEach(img => {
                    img.src = data.photo_url;
                });

                // Tutup modal
                modalGantiFoto.classList.remove('flex');
                modalGantiFoto.classList.add('hidden');
                
                // Tampilkan notifikasi sukses
                showSuccess('Foto profil berhasil diperbarui');

                // Reset input file setelah sukses upload
                inputFoto.value = ''; 
                previewFoto.src = data.photo_url; // Set preview to the newly uploaded photo

            } else {
                // Tampilkan error
                showError(data.message || 'Gagal mengunggah foto');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Terjadi kesalahan pada server');
        });
    });

    // Modal konfirmasi hapus foto
    const modalKonfirmasiHapus = document.getElementById('modalKonfirmasiHapus');
    const btnKonfirmasiHapus = document.getElementById('btnKonfirmasiHapus');
    const btnBatalHapus = document.getElementById('btnBatalHapus');
    const btnCloseKonfirmasi = document.getElementById('btnCloseKonfirmasi');

    // Event listeners untuk modal konfirmasi
    [btnBatalHapus, btnCloseKonfirmasi].forEach(btn => {
        btn.addEventListener('click', () => {
            modalKonfirmasiHapus.classList.remove('flex');
            modalKonfirmasiHapus.classList.add('hidden');
        });
    });

    // Tutup modal konfirmasi saat klik di luar
    modalKonfirmasiHapus.addEventListener('click', (event) => {
        if (event.target === modalKonfirmasiHapus) {
            modalKonfirmasiHapus.classList.remove('flex');
            modalKonfirmasiHapus.classList.add('hidden');
        }
    });

    // Konfirmasi hapus foto
    btnKonfirmasiHapus.addEventListener('click', function() {
        hapusFotoProfil();
    });

    // Fungsi untuk menghapus foto profil
    function hapusFotoProfil() {
        fetch('{{ route("profil.hapus-foto") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update foto profil ke default
                profileImage.src = defaultAvatar;
                previewFoto.src = defaultAvatar;
                
                // Update foto di navbar ke default
                const navbarProfileImages = document.querySelectorAll('#profile-button img, #dropdown-user img');
                navbarProfileImages.forEach(img => {
                    img.src = defaultAvatar;
                });
                
                // Reset input file
                inputFoto.value = '';
                
                // Tutup modal konfirmasi dan modal ganti foto
                modalKonfirmasiHapus.classList.remove('flex');
                modalKonfirmasiHapus.classList.add('hidden');
                modalGantiFoto.classList.remove('flex');
                modalGantiFoto.classList.add('hidden');
                
                // Tampilkan pesan sukses menggunakan showSuccess global
                showSuccess('Foto profil berhasil dihapus');
            } else {
                // Tutup modal konfirmasi
                modalKonfirmasiHapus.classList.remove('flex');
                modalKonfirmasiHapus.classList.add('hidden');
                
                // Tampilkan error menggunakan showError global
                showError(data.message || 'Gagal menghapus foto profil');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Tutup modal konfirmasi
            modalKonfirmasiHapus.classList.remove('flex');
            modalKonfirmasiHapus.classList.add('hidden');
            
            // Tampilkan error menggunakan showError global
            showError('Terjadi kesalahan saat menghapus foto profil');
        });
    }

    // Modal Edit Profil
    const btnEditProfil = document.getElementById('btnEditProfil');
    const modalEditProfil = document.getElementById('modalEditProfil');
    const btnCloseEditProfil = document.getElementById('btnCloseEditProfil');
    const btnBatalEditProfil = document.getElementById('btnBatalEditProfil');
    const formEditProfil = document.getElementById('formEditProfil');

    // Toggle modal edit profil
    btnEditProfil.addEventListener('click', () => {
        modalEditProfil.classList.remove('hidden');
        modalEditProfil.classList.add('flex');
    });

    // Tutup modal edit profil
    [btnCloseEditProfil, btnBatalEditProfil].forEach(btn => {
        btn.addEventListener('click', () => {
            modalEditProfil.classList.remove('flex');
            modalEditProfil.classList.add('hidden');
            formEditProfil.reset();
            // Reset form ke nilai asli
            document.getElementById('edit_name').value = '{{ $user->name }}';
            document.getElementById('edit_email').value = '{{ $user->email }}';
            document.getElementById('edit_nip').value = '{{ $user->nip }}';
            // Hide error messages
            document.getElementById('nameError').classList.add('hidden');
            document.getElementById('emailError').classList.add('hidden');
            document.getElementById('nipError').classList.add('hidden');
        });
    });

    // Tutup modal saat klik di luar
    modalEditProfil.addEventListener('click', (event) => {
        if (event.target === modalEditProfil) {
            modalEditProfil.classList.remove('flex');
            modalEditProfil.classList.add('hidden');
            formEditProfil.reset();
            // Reset form ke nilai asli
            document.getElementById('edit_name').value = '{{ $user->name }}';
            document.getElementById('edit_email').value = '{{ $user->email }}';
            document.getElementById('edit_nip').value = '{{ $user->nip }}';
            // Hide error messages
            document.getElementById('nameError').classList.add('hidden');
            document.getElementById('emailError').classList.add('hidden');
            document.getElementById('nipError').classList.add('hidden');
        }
    });

    // Handle form submit edit profil dengan event delegation
    document.addEventListener('click', function(e) {
        if (e.target && e.target.matches('#formEditProfil button[type="submit"]')) {
            e.preventDefault();
            
            console.log('Form edit profil submitted via click'); // Debug log
            
            const form = document.getElementById('formEditProfil');
            submitEditProfile(form, e.target);
        }
    });
    
    // Backup event listener untuk form submit
    formEditProfil.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form edit profil submitted via form submit'); // Debug log
        submitEditProfile(this, this.querySelector('button[type="submit"]'));
    });
    
    function submitEditProfile(form, submitButton) {
        // Reset error messages
        document.getElementById('nameError').classList.add('hidden');
        document.getElementById('emailError').classList.add('hidden');
        document.getElementById('nipError').classList.add('hidden');

        // Show loading overlay
        showLoading('Memperbarui profil...');

        // Disable submit button to prevent double submission
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Menyimpan...';

        // Collect form data
        const formData = new FormData(form);
        
        console.log('Form data:', Object.fromEntries(formData)); // Debug log

        // Submit form via fetch
        fetch('{{ route("profile.update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            console.log('Response status:', response.status); // Debug log
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data); // Debug log
            
            if (data.success) {
                // Update informasi di halaman
                const nameElement = document.querySelector('[data-field="name"]');
                const emailElement = document.querySelector('[data-field="email"]');
                const headerNameElement = document.querySelector('h2.text-xl.font-bold.text-gray-900.mb-2');
                
                if (nameElement) {
                    nameElement.textContent = data.data.name;
                }
                if (emailElement) {
                    emailElement.textContent = data.data.email;
                }
                if (headerNameElement) {
                    headerNameElement.textContent = data.data.name;
                }
                
                // Success - show notification and close modal automatically
                showSuccess('Profil berhasil diperbarui');
                
                // Auto close modal after short delay
                setTimeout(() => {
                    modalEditProfil.classList.remove('flex');
                    modalEditProfil.classList.add('hidden');
                    // Reset form
                    form.reset();
                    document.getElementById('edit_name').value = data.data.name;
                    document.getElementById('edit_email').value = data.data.email;
                    document.getElementById('edit_nip').value = data.data.nip;
                }, 500);
            } else {
                // Show validation errors
                if (data.errors) {
                    for (const [field, message] of Object.entries(data.errors)) {
                        const errorElement = document.getElementById(`${field}Error`);
                        if (errorElement) {
                            errorElement.textContent = Array.isArray(message) ? message[0] : message;
                            errorElement.classList.remove('hidden');
                        }
                    }
                } else {
                    showError(data.message || 'Terjadi kesalahan saat memperbarui profil');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Terjadi kesalahan pada server');
        })
        .finally(() => {
            // Hide loading overlay
            hideLoading();
            
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        });
    }
});
</script>
@endpush