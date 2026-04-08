# E-SAKIP: Sistem Akuntabilitas Kinerja Instansi Pemerintah Berbasis Web

## Informasi Mahasiswa
- **Nama**: Seizra Aulia Salsabila
- **NIM**: 222112355
- **Judul Skripsi**: Rancang Bangun Sistem Akuntabilitas Kinerja Instansi Pemerintah Badan Pusat Statistik Berbasis Web E-SAKIP Studi Kasus BPS Kabupaten Belitung
- **Nama Dosen Pembimbing**: Yunarso Anang, Ph.D.

## Deskripsi Singkat Skripsi

Proyek E-SAKIP (Elektronik Sistem Akuntabilitas Kinerja Instansi Pemerintah) merupakan sistem berbasis web yang dirancang khusus untuk Badan Pusat Statistik (BPS) Kabupaten Belitung. Sistem ini bertujuan untuk meningkatkan efisiensi dan transparansi dalam pengelolaan akuntabilitas kinerja instansi pemerintah melalui digitalisasi proses perencanaan, pengukuran, dan pelaporan kinerja.

Sistem ini menyediakan fitur-fitur utama seperti:
- Manajemen Renstra (Rencana Strategis)
- Pengelolaan Kegiatan dan Program
- Sistem FRA (Framework for Results Assessment)
- Manajemen Dokumen dan Bukti Dukung
- Pelaporan Kinerja Terintegrasi
- Manajemen Pengguna dan Role-based Access Control
- Integrasi dengan Google Drive untuk penyimpanan dokumen
- Dashboard Analytics untuk visualisasi data kinerja

## Teknologi yang Digunakan

- **Backend**: Laravel 11 (PHP Framework)
- **Frontend**: Blade Templates dengan Tailwind CSS
- **Database**: MySQL/MariaDB
- **Build Tools**: Vite untuk asset bundling
- **Package Manager**: Composer (PHP), NPM (JavaScript)
- **Version Control**: Git
- **Cloud Integration**: Google Drive API
- **Deployment**: Vercel (konfigurasi tersedia)

## Struktur Folder Project

```
d:\laragon\www\sakip\
├── .editorconfig                # Konfigurasi editor untuk konsistensi coding style
├── .env.example                 # Template file environment variables
├── .env.email.example           # Template konfigurasi email
├── .gitignore                   # File yang diabaikan oleh Git
├── .gitattributes              # Konfigurasi Git attributes
├── .vercel/                    # Konfigurasi deployment Vercel
│   └── project.json
├── CSRF_TOKEN_FIX.md           # Dokumentasi perbaikan CSRF token
├── README.md                   # File dokumentasi utama (file ini)
├── RESET_PASSWORD_SETUP.md     # Dokumentasi setup reset password
├── app/                        # Direktori utama aplikasi Laravel
│   ├── Console/                # Artisan commands dan kernel console
│   │   ├── Commands/           # Custom artisan commands
│   │   └── Kernel.php         # Console kernel
│   ├── Helpers/                # Helper functions custom
│   │   └── BreadcrumbsHelper.php # Helper untuk navigasi breadcrumbs
│   ├── Http/                   # HTTP layer (Controllers, Middleware, Requests)
│   │   ├── Controllers/        # Controller untuk handling request
│   │   ├── Kernel.php         # HTTP Kernel
│   │   └── Middleware/        # Custom middleware
│   ├── Mail/                   # Kelas untuk pengiriman email
│   │   └── ResetPasswordMail.php # Email template reset password
│   ├── Models/                 # Eloquent Models untuk database
│   │   ├── Bukti_Dukung.php   # Model untuk bukti dukung kegiatan
│   │   ├── Buktidukung_Fra.php # Model untuk bukti dukung FRA
│   │   ├── Dokumen_Kegiatan.php # Model untuk dokumen kegiatan
│   │   ├── Fra.php            # Model untuk Framework Results Assessment
│   │   ├── Kegiatan.php       # Model untuk kegiatan instansi
│   │   ├── Komponen.php       # Model untuk komponen renstra
│   │   ├── Matriks_Fra.php    # Model untuk matriks FRA
│   │   ├── Pengguna.php       # Model untuk pengguna sistem
│   │   ├── Realisasi_Fra.php  # Model untuk realisasi FRA
│   │   ├── Renstra.php        # Model untuk rencana strategis
│   │   ├── Role.php           # Model untuk role pengguna
│   │   ├── Skp.php            # Model untuk SKP (Sasaran Kerja Pegawai)
│   │   ├── Sub_Komponen.php   # Model untuk sub komponen
│   │   ├── Target_Fra.php     # Model untuk target FRA
│   │   ├── Target_Pk.php      # Model untuk target PK
│   │   ├── Template_Fra.php   # Model untuk template FRA
│   │   ├── Template_Jenis.php # Model untuk jenis template
│   │   ├── Triwulan.php       # Model untuk periode triwulan
│   │   └── User.php           # Model default Laravel user
│   ├── Providers/              # Service Providers
│   │   ├── ActivityAutoCreationServiceProvider.php # Provider auto-create activity
│   │   ├── AppServiceProvider.php # Provider utama aplikasi
│   │   └── BreadcrumbsServiceProvider.php # Provider breadcrumbs
│   ├── Rules/                  # Custom validation rules
│   ├── Services/               # Business logic services
│   │   ├── EnhancedFraParser.php # Parser FRA yang ditingkatkan
│   │   ├── GoogleDriveFraService.php # Service integrasi Google Drive
│   │   ├── GoogleDriveOAuthService.php # OAuth Google Drive
│   │   ├── KabKotaFraParser.php # Parser FRA untuk Kab/Kota
│   │   └── UnifiedFraParser.php # Parser FRA terpadu
│   └── Traits/                 # Reusable traits
├── artisan                     # Laravel CLI tool
├── bootstrap/                  # Bootstrap files untuk aplikasi
│   ├── app.php                # Bootstrap aplikasi
│   ├── cache/                 # Cache bootstrap
│   └── providers.php          # Provider bootstrap
├── composer.json               # Dependensi PHP dan metadata project
├── composer.lock               # Lock file untuk dependensi PHP
├── composer.phar               # Composer executable
├── config/                     # File konfigurasi aplikasi
│   ├── app.php                # Konfigurasi aplikasi utama
│   ├── auth.php               # Konfigurasi autentikasi
│   ├── breadcrumbs.php        # Konfigurasi breadcrumbs
│   ├── cache.php              # Konfigurasi cache
│   ├── database.php           # Konfigurasi database
│   ├── filesystems.php        # Konfigurasi filesystem
│   ├── logging.php            # Konfigurasi logging
│   ├── mail.php               # Konfigurasi email
│   ├── queue.php              # Konfigurasi queue
│   ├── services.php           # Konfigurasi services eksternal
│   └── session.php            # Konfigurasi session
├── database/                   # Database related files
│   ├── .gitignore             # Git ignore untuk database
│   ├── factories/             # Model factories
│   │   └── UserFactory.php    # Factory untuk user
│   ├── migrations/            # File migrasi database
│   │   ├── 0001_01_01_000000_create_users_table.php # Migrasi tabel users
│   │   ├── 0001_01_01_000001_create_cache_table.php # Migrasi tabel cache
│   │   ├── 0001_01_01_000002_create_jobs_table.php # Migrasi tabel jobs
│   │   ├── 2025_04_14_122927_create_roles_table.php # Migrasi tabel roles
│   │   ├── 2025_04_14_122928_create_penggunas_table.php # Migrasi tabel pengguna
│   │   ├── 2025_04_18_093943_create_komponens_table.php # Migrasi tabel komponen
│   │   ├── 2025_04_18_095027_create_sub_komponens_table.php # Migrasi sub komponen
│   │   ├── 2025_04_18_095405_create_renstras_table.php # Migrasi tabel renstra
│   │   ├── 2025_04_18_105327_create_kegiatans_table.php # Migrasi tabel kegiatan
│   │   ├── 2025_04_22_190555_create_fras_table.php # Migrasi tabel FRA
│   │   ├── 2025_04_22_191340_create_template_jenis_table.php # Migrasi template jenis
│   │   ├── 2025_04_22_192241_create_template_fras_table.php # Migrasi template FRA
│   │   ├── 2025_04_22_192303_create_matriks_fras_table.php # Migrasi matriks FRA
│   │   ├── 2025_04_24_055455_create_target_fras_table.php # Migrasi target FRA
│   │   ├── 2025_04_24_062955_create_triwulans_table.php # Migrasi triwulan
│   │   ├── 2025_04_26_123221_create_realisasi_fras_table.php # Migrasi realisasi FRA
│   │   ├── 2025_04_26_123315_create_buktidukung_fras_table.php # Migrasi bukti dukung FRA
│   │   ├── 2025_04_27_132935_create_bukti_dukungs_table.php # Migrasi bukti dukung
│   │   ├── 2025_05_21_075033_create_dokumen_kegiatans_table.php # Migrasi dokumen kegiatan
│   │   ├── 2025_06_15_020923_create_target_pks_table.php # Migrasi target PK
│   │   ├── 2025_07_25_145941_add_profile_picture_nip_bidang_to_pengguna_table.php # Update pengguna
│   │   ├── 2025_07_27_000000_create_skps_table.php # Migrasi tabel SKP
│   │   ├── 2025_07_27_000001_add_folder_id_to_kegiatan_table.php # Update kegiatan
│   │   ├── 2025_07_27_000002_add_folder_id_to_renstra_table.php # Update renstra
│   │   └── ... (migrasi lainnya untuk perbaikan dan update)
│   ├── schema/                 # Database schema
│   └── seeders/               # Data seeder untuk database
│       ├── DatabaseSeeder.php  # Seeder utama
│       ├── KomponenSeeder.php  # Seeder komponen
│       ├── PenggunaSeeder.php  # Seeder pengguna
│       ├── RenstraSeeder.php   # Seeder renstra
│       ├── RoleSeeder.php      # Seeder role
│       ├── SubKomponenSeeder.php # Seeder sub komponen
│       └── TemplateJenisSeeder.php # Seeder template jenis
├── package.json                # Dependensi Node.js
├── package-lock.json           # Lock file Node.js
├── phpunit.xml                 # Konfigurasi PHPUnit testing
├── postcss.config.js           # Konfigurasi PostCSS
├── public/                     # Web root directory
│   ├── .htaccess              # Apache configuration
│   ├── index.php              # Entry point aplikasi
│   ├── favicon.ico            # Icon website
│   ├── robots.txt             # Robot crawler rules
│   ├── build/                 # Built assets
│   │   ├── assets/            # Compiled CSS/JS
│   │   └── manifest.json      # Asset manifest
│   ├── img/                   # Gambar dan aset visual
│   │   ├── logo BPS.png       # Logo BPS resmi
│   │   ├── Ilustrasi Login.jpg # Ilustrasi halaman login
│   │   ├── Lupa Password.jpg  # Ilustrasi lupa password
│   │   ├── Renstra.jpeg       # Ilustrasi renstra
│   │   ├── Reviu Renstra.jpeg # Ilustrasi reviu renstra
│   │   ├── Reviu Target Renstra.jpeg # Ilustrasi reviu target
│   │   ├── Capaian Target Renstra.jpeg # Ilustrasi capaian
│   │   ├── Statistics.png     # Icon statistik
│   │   ├── default-avatar.svg # Avatar default pengguna
│   │   ├── bg1.jpg - bg5.jpg  # Background images
│   │   ├── gambar1.jpeg       # Gambar ilustrasi 1
│   │   └── gambar2.jpeg       # Gambar ilustrasi 2
│   ├── fra_template/          # Template dokumen FRA
│   └── js/                    # JavaScript files
├── resources/                  # Resources untuk frontend
│   ├── css/                   # Stylesheet
│   │   └── app.css            # CSS utama aplikasi
│   ├── js/                    # JavaScript files
│   │   ├── app.js             # JavaScript utama
│   │   └── bootstrap.js       # Bootstrap JS
│   └── views/                 # Blade templates
│       ├── components/        # Komponen reusable
│       ├── dashboard.blade.php # Dashboard utama
│       ├── dashboard_detail.blade.php # Detail dashboard
│       ├── login.blade.php    # Halaman login
│       ├── lupa_password.blade.php # Halaman lupa password
│       ├── manajemen_pengguna.blade.php # Manajemen pengguna
│       ├── manajemen_profil.blade.php # Manajemen profil
│       ├── skp_detail.blade.php # Detail SKP
│       ├── unggah_skp.blade.php # Upload SKP
│       ├── welcome.blade.php  # Halaman welcome
│       ├── emails/            # Template email
│       ├── kegiatan/          # Views untuk modul kegiatan
│       ├── perencanaan kinerja/ # Views perencanaan kinerja
│       ├── pengukuran kinerja/ # Views pengukuran kinerja
│       ├── pelaporan kinerja/ # Views pelaporan kinerja
│       └── seeder migrasi/    # Views untuk seeder migrasi
├── routes/                     # Route definitions
│   ├── web.php                # Web routes
│   └── console.php            # Console routes
├── storage/                    # Storage untuk aplikasi
│   ├── app/                   # Application storage
│   │   ├── .gitignore         # Git ignore storage
│   │   ├── private/           # Private storage
│   │   └── public/            # Public storage
│   ├── framework/             # Framework storage
│   │   ├── .gitignore         # Git ignore framework
│   │   ├── cache/             # Cache storage
│   │   ├── sessions/          # Session storage
│   │   ├── testing/           # Testing storage
│   │   └── views/             # Compiled views
│   └── logs/                  # Log files
│       └── .gitignore         # Git ignore logs
├── tests/                      # Test files
│   ├── Feature/               # Feature tests
│   │   └── ExampleTest.php    # Contoh feature test
│   ├── Unit/                  # Unit tests
│   │   └── ExampleTest.php    # Contoh unit test
│   └── TestCase.php           # Base test case
├── tailwind.config.js          # Konfigurasi Tailwind CSS
└── vite.config.js              # Konfigurasi Vite build tool
```

## Penjelasan Struktur Folder Utama

### 1. **app/** - Core Application
Berisi seluruh logic aplikasi utama:
- **Models/**: Representasi tabel database menggunakan Eloquent ORM dengan 20+ model untuk entitas seperti Kegiatan, FRA, Renstra, dll.
- **Http/Controllers/**: Menangani request HTTP dan response untuk semua fitur sistem
- **Services/**: Business logic yang kompleks, termasuk parser FRA dan integrasi Google Drive
- **Mail/**: Kelas untuk pengiriman email (reset password, notifikasi)
- **Providers/**: Service providers untuk bootstrapping aplikasi dan fitur khusus

### 2. **database/** - Database Management
- **migrations/**: 30+ file migrasi yang mengelola skema database secara bertahap
- **seeders/**: Data awal untuk mengisi database dengan data master dan dummy
- Migrasi mencakup tabel utama seperti users, roles, kegiatan, FRA, renstra, dan berbagai tabel pendukung

### 3. **resources/views/** - User Interface
Berisi template Blade untuk rendering halaman web:
- **perencanaan kinerja/**: Interface untuk modul perencanaan strategis
- **pengukuran kinerja/**: Interface untuk modul pengukuran dan monitoring
- **pelaporan kinerja/**: Interface untuk modul pelaporan dan analisis
- **kegiatan/**: Interface untuk manajemen kegiatan instansi
- **components/**: Komponen UI yang dapat digunakan kembali

### 4. **public/** - Web Assets
- **img/**: Aset visual lengkap termasuk logo BPS, ilustrasi untuk berbagai modul, dan background
- **fra_template/**: Template dokumen FRA yang dapat diunduh
- **build/**: Asset yang sudah di-compile oleh Vite (CSS, JS)

### 5. **config/** - Configuration
File konfigurasi lengkap untuk berbagai aspek aplikasi:
- Database, autentikasi, email, cache, logging
- Konfigurasi khusus untuk breadcrumbs dan services eksternal

## Instalasi dan Setup

### Prasyarat
- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Web Server (Apache/Nginx)

### Langkah Instalasi

1. **Clone repository**
   ```bash
   git clone [repository-url]
   cd sakip
   ```

2. **Install dependensi PHP**
   ```bash
   composer install
   ```

3. **Install dependensi Node.js**
   ```bash
   npm install
   ```

4. **Setup environment**
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

5. **Konfigurasi database**
   - Edit file `.env` sesuai dengan konfigurasi database
   - Buat database baru untuk aplikasi
   - Jalankan migrasi: `php artisan migrate`
   - Jalankan seeder: `php artisan db:seed`

6. **Konfigurasi email (opsional)**
   - Salin `.env.email.example` untuk konfigurasi email
   - Sesuaikan pengaturan SMTP

7. **Build assets**
   ```bash
   npm run build
   ```
   Atau untuk development:
   ```bash
   npm run dev
   ```

8. **Jalankan aplikasi**
   ```bash
   php artisan serve
   ```

## Fitur Utama Sistem

### 1. **Manajemen Renstra**
- Perencanaan rencana strategis instansi
- Penetapan visi, misi, dan tujuan strategis
- Pengelolaan komponen dan sub komponen
- Integrasi dengan Google Drive untuk penyimpanan dokumen

### 2. **Manajemen Kegiatan**
- Perencanaan dan penjadwalan kegiatan
- Monitoring progress kegiatan
- Upload dan manajemen dokumen kegiatan
- Tracking realisasi anggaran

### 3. **Sistem FRA (Framework for Results Assessment)**
- Multiple parser untuk berbagai format FRA
- Template FRA yang dapat disesuaikan
- Matriks penilaian kinerja
- Realisasi dan target FRA per triwulan

### 4. **Manajemen Dokumen**
- Upload dan kategorisasi bukti dukung
- Integrasi dengan Google Drive
- Versioning dokumen
- Download template dan panduan

### 5. **Pelaporan Terintegrasi**
- Generate laporan kinerja otomatis
- Visualisasi data dengan chart dan grafik
- Export laporan dalam berbagai format
- Dashboard analytics real-time

### 6. **User Management**
- Role-based access control
- Manajemen profil pengguna
- Sistem autentikasi yang aman
- Reset password via email

### 7. **SKP (Sasaran Kerja Pegawai)**
- Upload dan manajemen SKP
- Tracking pencapaian target individu
- Integrasi dengan sistem kinerja instansi

## Keamanan

- CSRF Protection (dokumentasi perbaikan tersedia)
- Input validation dan sanitization
- Role-based authorization
- Secure password hashing
- Session management yang aman

## Testing

- PHPUnit untuk unit dan feature testing
- Test cases untuk fitur utama
- Continuous integration ready

## Deployment

- Konfigurasi Vercel tersedia
- Support untuk berbagai hosting provider
- Environment-based configuration
- Asset optimization dengan Vite

## Kontribusi dan Pengembangan

Proyek ini merupakan bagian dari skripsi dan dikembangkan untuk keperluan akademis serta implementasi di BPS Kabupaten Belitung. Sistem ini dapat dikembangkan lebih lanjut untuk instansi pemerintah lainnya dengan penyesuaian yang diperlukan.

## Lisensi

Proyek ini dikembangkan untuk keperluan akademis dan implementasi di instansi pemerintah.

---

**Developed by**: Seizra Aulia Salsabila  
**Student ID**: 222112355  
**Supervised by**: Yunarso Anang, Ph.D.  
**Year**: 2025

**Studi Kasus**: BPS Kabupaten Belitung  
**Framework**: Laravel 11  
**Database**: MySQL/MariaDB  
**Frontend**: Blade + Tailwind CSS
