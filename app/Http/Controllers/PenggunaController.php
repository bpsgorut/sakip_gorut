<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');

        $query = Pengguna::with('role');

        // Sorting
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'jabatan':
                $query->orderBy('jabatan', $sortOrder);
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        $pengguna = $query->paginate($perPage);
        $roles = Role::all();

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Manajemen Pengguna', 'url' => route('manajemen.pengguna'), 'clickable' => false],
        ];

        return view('manajemen_pengguna', compact('pengguna', 'roles', 'breadcrumbs'));
    }

    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:pengguna'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
            ],
        ]);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create the user
        $user = Pengguna::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 4, // Default role_id for new users
            'jabatan' => 'default', // Default jabatan for new users
        ]);

        // Redirect to login page with success message
        return redirect()->route('login')->with('success', 'Akun berhasil dibuat! Silakan login.');
    }

    /**
     * Store a newly created pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:pengguna'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
            ],
            'role_id' => ['required', 'exists:role,id'],
            'jabatan' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:255', 'unique:pengguna'], // Tambah validasi NIP
            'bidang' => ['required', 'string', 'max:255'], // Tambah validasi Bidang
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ]);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pengguna = Pengguna::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'jabatan' => $request->jabatan,
            'nip' => $request->nip, // Tambah NIP
            'bidang' => $request->bidang, // Tambah Bidang
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengguna berhasil ditambahkan',
                'pengguna' => $pengguna
            ]);
        }

        return redirect()->route('manajemen.pengguna')
            ->with('success', 'Pengguna berhasil ditambahkan');
    }

    /**
     * Get pengguna for editing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pengguna = Pengguna::findOrFail($id);
        return response()->json($pengguna);
    }

    /**
     * Update the specified pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $pengguna = Pengguna::findOrFail($request->user_id);
        
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:pengguna,email,'.$pengguna->id],
            'role_id' => ['required', 'exists:role,id'],
            'jabatan' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:255', 'unique:pengguna,nip,'.$pengguna->id], // Tambah validasi NIP untuk update
            'bidang' => ['required', 'string', 'max:255'], // Tambah validasi Bidang untuk update
        ];
        
        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = [
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
            ];
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ]);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'jabatan' => $request->jabatan,
            'nip' => $request->nip, // Tambah NIP
            'bidang' => $request->bidang, // Tambah Bidang
        ];
        
        // Update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $pengguna->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengguna berhasil diperbarui',
                'pengguna' => $pengguna
            ]);
        }

        return redirect()->route('manajemen.pengguna')
            ->with('success', 'Pengguna berhasil diperbarui');
    }

    /**
     * Remove the specified pengguna(s) from database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $ids = $request->ids;
        
        if (!is_array($ids) || empty($ids)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada pengguna yang dipilih'
                ]);
            }
            
            return redirect()->back()
                ->with('error', 'Tidak ada pengguna yang dipilih');
        }
        
        Pengguna::whereIn('id', $ids)->delete();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => count($ids) . ' pengguna berhasil dihapus'
            ]);
        }
        
        return redirect()->route('manajemen.pengguna')
            ->with('success', count($ids) . ' pengguna berhasil dihapus');
    }

    /**
     * Menampilkan halaman manajemen profil pengguna
     * 
     * @return \Illuminate\View\View
     */
    public function manajemenProfil()
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();

        // Pastikan user sudah login dan load relasi role
        if (!$user) {
            return redirect()->route('login');
        }

        $user->load('role');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Profil Saya', 'url' => route('manajemen.profil'), 'clickable' => false],
        ];

        return view('manajemen_profil', compact('user', 'breadcrumbs'));
    }

    /**
     * Mengubah password pengguna
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();

        // Validasi input
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Password saat ini tidak sesuai');
                }
            }],
            'new_password' => [
                'required', 
                'confirmed', 
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
            ],
        ], [
            'current_password.required' => 'Password saat ini harus diisi',
            'new_password.required' => 'Password baru harus diisi',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Logout user dari semua perangkat (opsional)
        // auth()->logoutOtherDevices($request->new_password);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah'
        ]);
    }

    /**
     * Update foto profil pengguna
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfilePhoto(Request $request)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();

        Log::info('Memulai update foto profil untuk user: ' . $user->id);

        // Validasi input file
        $validator = Validator::make($request->all(), [
            'profile_picture' => [
                'required', 
                'image', 
                'mimes:jpeg,png,jpg,gif', 
                'max:5120' // 5MB
            ]
        ], [
            'profile_picture.required' => 'Pilih foto profil terlebih dahulu',
            'profile_picture.image' => 'File harus berupa gambar',
            'profile_picture.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
            'profile_picture.max' => 'Ukuran gambar maksimal 5MB'
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            Log::error('Validasi gagal saat update foto profil:', ['errors' => $validator->errors()->toArray()]);
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        // Hapus foto lama jika ada
        if ($user->profile_picture) {
            $oldPhotoPath = 'public/profile_pictures/' . $user->profile_picture;
            if (Storage::exists($oldPhotoPath)) {
                Storage::delete($oldPhotoPath);
                Log::info('Foto lama berhasil dihapus: ' . $oldPhotoPath);
            } else {
                Log::warning('Foto lama tidak ditemukan: ' . $oldPhotoPath);
            }
        }

        // Simpan foto baru
        try {
            $file = $request->file('profile_picture');
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            Log::info('Mencoba menyimpan file:', [
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);
            
            // Coba simpan dengan cara yang berbeda untuk debugging
            $destinationPath = storage_path('app/public/profile_pictures');
            
            // Pastikan direktori ada
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
                Log::info('Direktori profile_pictures dibuat: ' . $destinationPath);
            }
            
            // Simpan file secara manual
            $fullPath = $destinationPath . DIRECTORY_SEPARATOR . $filename;
            $moved = $file->move($destinationPath, $filename);
            
            if (!$moved) {
                Log::error('Gagal memindahkan file ke: ' . $fullPath);
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memindahkan file.'
                ]);
            }
            
            $path = 'public/profile_pictures/' . $filename;
            Log::info('File berhasil dipindahkan ke: ' . $fullPath);
            
            Log::info('Hasil storeAs:', [
                'path' => $path,
                'full_path' => storage_path('app/' . $path)
            ]);

            if (!$path) {
                Log::error('Gagal menyimpan file foto ke storage.');
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan file foto.'
                ]);
            }
            
            // Verifikasi file benar-benar tersimpan
            $fullPath = storage_path('app/' . $path);
            if (!file_exists($fullPath)) {
                Log::error('File tidak ditemukan setelah upload:', ['path' => $fullPath]);
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak tersimpan dengan benar.'
                ]);
            }
            
            Log::info('File berhasil tersimpan:', [
                'path' => $fullPath,
                'file_size' => filesize($fullPath)
            ]);

            // Update database
            $user->profile_picture = $filename;
            $user->save();

            Log::info('Foto profil berhasil disimpan dan diupdate di database untuk user: ' . $user->id, ['filename' => $filename]);

            // URL foto untuk response
            $photoUrl = asset('storage/profile_pictures/' . $filename);

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil diperbarui',
                'photo_url' => $photoUrl
            ]);
        } catch (\Exception $e) {
            Log::error('Exception saat mengunggah foto profil: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunggah foto: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Hapus foto profil pengguna
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProfilePhoto(Request $request)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();

        Log::info('Memulai penghapusan foto profil untuk user: ' . $user->id, [
            'current_profile_picture' => $user->profile_picture
        ]);

        try {
            // Hapus foto dari storage jika ada
            if ($user->profile_picture) {
                $photoPath = 'public/profile_pictures/' . $user->profile_picture;
                $fullPath = storage_path('app/' . $photoPath);
                
                Log::info('Mencoba menghapus file:', [
                    'storage_path' => $photoPath,
                    'full_path' => $fullPath,
                    'file_exists_storage' => Storage::exists($photoPath),
                    'file_exists_full' => file_exists($fullPath)
                ]);
                
                if (Storage::exists($photoPath)) {
                    Storage::delete($photoPath);
                    Log::info('Foto profil berhasil dihapus dari storage: ' . $photoPath);
                } else {
                    Log::warning('Foto profil tidak ditemukan di storage: ' . $photoPath);
                }
                
                // Double check dengan file_exists dan unlink jika masih ada
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                    Log::info('File fisik berhasil dihapus dengan unlink: ' . $fullPath);
                }
            } else {
                Log::info('User tidak memiliki foto profil untuk dihapus');
            }

            // Update database - set profile_picture ke null
            $user->profile_picture = null;
            $user->save();

            Log::info('Foto profil berhasil dihapus dari database untuk user: ' . $user->id);

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Exception saat menghapus foto profil: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus foto: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update profil pengguna (nama, email, dan NIP)
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();

        Log::info('Memulai update profil untuk user: ' . $user->id, [
            'request_data' => $request->all(),
            'current_name' => $user->name,
            'current_email' => $user->email,
            'current_nip' => $user->nip
        ]);

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:pengguna,email,' . $user->id,
            'nip' => 'nullable|string|size:18|regex:/^[0-9]{18}$/|unique:pengguna,nip,' . $user->id
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.string' => 'Nama harus berupa teks',
            'name.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain',
            'nip.size' => 'NIP harus terdiri dari 18 digit',
            'nip.regex' => 'NIP harus berupa 18 digit angka',
            'nip.unique' => 'NIP sudah digunakan oleh pengguna lain'
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            Log::warning('Validasi gagal saat update profil:', [
                'errors' => $validator->errors()->toArray()
            ]);
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        try {
            // Update data pengguna
            $user->name = $request->name;
            $user->email = $request->email;
            
            // Update NIP jika ada
            if ($request->filled('nip')) {
                $user->nip = $request->nip;
            }
            
            $user->save();

            Log::info('Profil berhasil diperbarui untuk user: ' . $user->id, [
                'new_name' => $user->name,
                'new_email' => $user->email,
                'new_nip' => $user->nip
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'nip' => $user->nip
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Exception saat update profil: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage()
            ]);
        }
    }
}