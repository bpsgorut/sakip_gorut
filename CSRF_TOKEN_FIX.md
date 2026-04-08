# CSRF Token Conflict Fix

## Masalah
Aplikasi mengalami error 419 "Page Expired" saat submit form menggunakan AJAX dengan `fetch()` API. Error ini terjadi karena konflik dalam pengiriman CSRF token.

## Penyebab
Ketika menggunakan `FormData` dengan form yang memiliki `@csrf` directive, Laravel secara otomatis menyertakan field `_token` dalam form data. Namun, jika kita juga mengirim CSRF token melalui header `X-CSRF-TOKEN`, Laravel akan mengalami konflik dalam validasi token.

## Solusi
Menghapus `_token` dari `FormData` sebelum mengirim request, sehingga hanya menggunakan `X-CSRF-TOKEN` header:

```javascript
const formData = new FormData(form);

// 🔧 FIX: Remove _token from FormData to avoid conflict with X-CSRF-TOKEN header
// Laravel expects CSRF token either in form data (_token) OR in header (X-CSRF-TOKEN), not both
// Using both can cause 419 "Page Expired" errors due to token validation conflicts
formData.delete('_token');

fetch(url, {
    method: 'POST',
    body: formData,
    headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest'
    }
});
```

## File yang Diperbaiki
1. `resources/views/pengukuran kinerja/form_target_fra.blade.php`
2. `resources/views/pengukuran kinerja/form_target_pk.blade.php`
3. `resources/views/pengukuran kinerja/form_realisasi_fra.blade.php`
4. `resources/views/pengukuran kinerja/reward_punishment_detail.blade.php`

## Catatan
- Pastikan meta tag `csrf-token` tersedia di layout: `<meta name="csrf-token" content="{{ csrf_token() }}">`
- Gunakan konsisten antara form data (`_token`) ATAU header (`X-CSRF-TOKEN`), jangan keduanya
- Fix ini mencegah error 419 yang sering terjadi pada form AJAX

## Tanggal
" + new Date().toLocaleDateString('id-ID') + "