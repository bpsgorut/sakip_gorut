# 📧 Panduan Setup Fitur Reset Password - SAKIP BPS

## 🎯 Overview
Fitur reset password telah diimplementasikan dengan sistem pengiriman kode verifikasi 6 digit melalui email. Sistem ini menggunakan template email yang profesional dan logging yang komprehensif.

## 🚀 Fitur yang Telah Diimplementasikan

### ✅ Yang Sudah Selesai:
1. **Mailable Class** - `ResetPasswordMail` untuk mengirim email
2. **Template Email** - Design profesional dengan branding BPS
3. **Controller Logic** - Error handling dan logging yang robust
4. **Email Testing Command** - Tool untuk testing konfigurasi email
5. **Security Features**:
   - Kode verifikasi 6 digit random
   - Expiry time 15 menit
   - Session-based verification
   - Input validation dan sanitization

### 📧 Template Email Features:
- ✨ Design responsif dan profesional
- 🎨 Branding BPS dengan warna korporat
- 📱 Mobile-friendly layout
- 🔒 Security warnings dan instructions
- ⏰ Clear expiry information

## ⚙️ Setup Konfigurasi Email

### 1. Konfigurasi .env File

Salin pengaturan dari `.env.email.example` ke file `.env` Anda:

```env
# Untuk Gmail SMTP (Recommended untuk testing)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="SAKIP BPS"
```

### 2. Setup Gmail App Password (Untuk Testing)

1. **Enable 2-Factor Authentication** di akun Gmail
2. **Generate App Password**:
   - Buka: https://myaccount.google.com/apppasswords
   - Pilih "Mail" sebagai app
   - Copy password yang dihasilkan
   - Gunakan password ini di `MAIL_PASSWORD`

### 3. Untuk Production (Email Server BPS)

```env
# Sesuaikan dengan email server resmi BPS
MAIL_MAILER=smtp
MAIL_HOST=mail.bps.go.id
MAIL_PORT=587
MAIL_USERNAME=sakip@bps.go.id
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bps.go.id
MAIL_FROM_NAME="SAKIP BPS"
```

## 🧪 Testing Email Configuration

### Command Line Testing
```bash
# Test dengan email dan kode default
php artisan email:test user@example.com

# Test dengan kode custom
php artisan email:test user@example.com --code=999888
```

### Manual Testing
1. Buka halaman lupa password: `/lupa_password`
2. Masukkan email yang terdaftar
3. Periksa inbox dan folder spam
4. Masukkan kode verifikasi
5. Set password baru

## 📊 Monitoring & Logging

### Log Files Location
```
storage/logs/laravel.log
```

### Log Entries
- ✅ **Success**: `Reset password email sent`
- ❌ **Error**: `Failed to send reset password email`
- 📧 **Details**: Email, timestamp, expiry time

### Monitoring Commands
```bash
# Monitor logs real-time
tail -f storage/logs/laravel.log

# Filter reset password logs
grep "reset password" storage/logs/laravel.log
```

## 🔧 Troubleshooting

### Common Issues & Solutions

#### 1. Email Tidak Terkirim
```bash
# Check konfigurasi
php artisan config:clear
php artisan email:test your-email@domain.com
```

**Possible Causes:**
- ❌ SMTP credentials salah
- ❌ Firewall memblokir port 587/465
- ❌ Gmail App Password tidak digunakan
- ❌ 2FA tidak aktif di Gmail

#### 2. Email Masuk ke Spam
**Solutions:**
- ✅ Gunakan email server resmi BPS
- ✅ Setup SPF, DKIM, DMARC records
- ✅ Gunakan domain yang sama untuk FROM address

#### 3. Kode Verifikasi Expired
**Default**: 15 menit expiry
**Customize**: Edit `AuthController::sendResetCode()`

```php
'reset_code_expires' => now()->addMinutes(30), // 30 menit
```

## 🚀 Deployment Checklist

### Pre-Production
- [ ] Test email configuration dengan `php artisan email:test`
- [ ] Verify email templates tampil dengan benar
- [ ] Test complete reset password flow
- [ ] Check logs untuk error messages
- [ ] Verify email tidak masuk spam

### Production
- [ ] Setup email server resmi BPS
- [ ] Configure proper FROM address
- [ ] Setup email monitoring
- [ ] Configure queue untuk async email (optional)
- [ ] Setup backup email provider (failover)

## 📈 Performance Optimization

### Queue Setup (Optional)
Untuk mengirim email secara asynchronous:

```env
QUEUE_CONNECTION=database
```

```bash
# Run queue worker
php artisan queue:work

# Or use supervisor untuk production
sudo supervisorctl start laravel-worker:*
```

### Implement ShouldQueue
```php
// Di ResetPasswordMail.php
class ResetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    // ...
}
```

## 🔐 Security Best Practices

### Implemented Security Features
- ✅ 6-digit random verification codes
- ✅ 15-minute expiry time
- ✅ Session-based verification
- ✅ Email validation
- ✅ Rate limiting (Laravel default)
- ✅ CSRF protection
- ✅ Input sanitization

### Additional Recommendations
- 🔒 Implement rate limiting untuk reset requests
- 🔒 Add CAPTCHA untuk prevent abuse
- 🔒 Log suspicious activities
- 🔒 Monitor failed attempts

## 📞 Support & Maintenance

### Regular Maintenance
- 📊 Monitor email delivery rates
- 🧹 Clean up expired sessions
- 📈 Review error logs weekly
- 🔄 Update email templates as needed

### Contact Information
**Technical Support**: IT Team BPS  
**Email Configuration**: Administrator Email Server BPS  
**Application Issues**: Development Team SAKIP  

---

## 📝 Changelog

### Version 1.0.0 (Current)
- ✅ Initial implementation
- ✅ Email template design
- ✅ Error handling & logging
- ✅ Testing commands
- ✅ Documentation

### Future Enhancements
- 🔄 SMS backup option
- 📱 Mobile app integration
- 🔒 Advanced rate limiting
- 📊 Analytics dashboard

---

**🎉 Fitur reset password siap digunakan!**  
*Pastikan untuk melakukan testing menyeluruh sebelum deployment ke production.*