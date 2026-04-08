<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SAKIP BPS</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #A51D1F 0%, #8B1A1C 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .verification-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px dashed #A51D1F;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .verification-code {
            font-size: 36px;
            font-weight: bold;
            color: #A51D1F;
            letter-spacing: 8px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        .verification-label {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 10px;
        }
        .instructions {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .instructions h3 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 16px;
        }
        .instructions ul {
            margin: 0;
            padding-left: 20px;
            color: #856404;
        }
        .instructions li {
            margin-bottom: 5px;
        }
        .warning {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .warning p {
            margin: 0;
            color: #721c24;
            font-size: 14px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        .footer p {
            margin: 0;
            font-size: 12px;
            color: #6c757d;
        }
        .logo {
            width: 40px;
            height: 40px;
            background-color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        .contact-info {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 5px;
            }
            .content {
                padding: 20px 15px;
            }
            .verification-code {
                font-size: 28px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="#A51D1F" stroke-width="2" stroke-linejoin="round"/>
                    <path d="M2 17L12 22L22 17" stroke="#A51D1F" stroke-width="2" stroke-linejoin="round"/>
                    <path d="M2 12L12 17L22 12" stroke="#A51D1F" stroke-width="2" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1>Reset Password</h1>
            <p>Sistem Akuntabilitas Kinerja Instansi Pemerintah</p>
        </div>

        <div class="content">
            <div class="greeting">
                Halo{{ $userName ? ', ' . $userName : '' }}!
            </div>

            <p>Kami menerima permintaan untuk mereset password akun SAKIP BPS Anda. Gunakan kode verifikasi berikut untuk melanjutkan proses reset password:</p>

            <div class="verification-box">
                <div class="verification-label">Kode Verifikasi Anda</div>
                <div class="verification-code">{{ $verificationCode }}</div>
                <p style="margin: 10px 0 0 0; font-size: 14px; color: #6c757d;">Berlaku selama 15 menit</p>
            </div>

            <div class="instructions">
                <h3>📋 Cara Menggunakan Kode:</h3>
                <ul>
                    <li>Kembali ke halaman reset password</li>
                    <li>Masukkan kode verifikasi di atas</li>
                    <li>Buat password baru yang kuat</li>
                    <li>Konfirmasi password baru Anda</li>
                </ul>
            </div>

            <div class="warning">
                <p><strong>⚠️ Penting:</strong> Jika Anda tidak meminta reset password, abaikan email ini. Kode akan kedaluwarsa dalam 15 menit untuk keamanan akun Anda.</p>
            </div>

            <p>Jika Anda mengalami kesulitan, silakan hubungi administrator sistem atau tim IT BPS.</p>
        </div>

        <div class="footer">
            <p><strong>Badan Pusat Statistik</strong></p>
            <p>Sistem Akuntabilitas Kinerja Instansi Pemerintah (SAKIP)</p>
            
            <div class="contact-info">
                <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
                <p>© {{ date('Y') }} BPS. Semua hak dilindungi undang-undang.</p>
            </div>
        </div>
    </div>
</body>
</html>