<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email} {--code=123456}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a reset password email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $code = $this->option('code');
        
        $this->info('Testing email configuration...');
        $this->info('Recipient: ' . $email);
        $this->info('Verification Code: ' . $code);
        $this->newLine();
        
        try {
            // Send test email
            Mail::to($email)->send(new ResetPasswordMail($code, 'Test User'));
            
            $this->info('✅ Email sent successfully!');
            $this->info('Please check the recipient\'s inbox (and spam folder).');
            
            // Show current mail configuration
            $this->newLine();
            $this->info('Current mail configuration:');
            $this->table(
                ['Setting', 'Value'],
                [
                    ['MAIL_MAILER', config('mail.default')],
                    ['MAIL_HOST', config('mail.mailers.smtp.host')],
                    ['MAIL_PORT', config('mail.mailers.smtp.port')],
                    ['MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption')],
                    ['MAIL_FROM_ADDRESS', config('mail.from.address')],
                    ['MAIL_FROM_NAME', config('mail.from.name')],
                ]
            );
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email!');
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            
            $this->warn('Troubleshooting tips:');
            $this->line('1. Check your .env file for correct MAIL_* settings');
            $this->line('2. Verify SMTP credentials are correct');
            $this->line('3. Ensure firewall allows SMTP ports (587/465)');
            $this->line('4. For Gmail, use App Password instead of regular password');
            $this->line('5. Check if 2FA is enabled for Gmail accounts');
            
            return 1;
        }
        
        return 0;
    }
}