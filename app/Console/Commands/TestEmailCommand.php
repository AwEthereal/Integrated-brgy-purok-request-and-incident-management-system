<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? config('mail.from.address');
        
        $this->info('Testing email configuration...');
        $this->info('Sending test email to: ' . $email);
        
        try {
            Mail::raw('This is a test email from Barangay Kalawag II System. If you received this, your email configuration is working correctly!', function($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - Barangay Kalawag II System');
            });
            
            $this->info('✅ Email sent successfully!');
            $this->info('Check your inbox at: ' . $email);
            $this->info('');
            $this->info('If you don\'t see the email:');
            $this->info('1. Check your spam/junk folder');
            $this->info('2. Wait a few minutes for delivery');
            $this->info('3. Verify your email credentials in .env file');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email!');
            $this->error('Error: ' . $e->getMessage());
            $this->error('');
            $this->error('Common issues:');
            $this->error('1. Invalid Gmail App Password - regenerate it');
            $this->error('2. 2-Step Verification not enabled on Gmail');
            $this->error('3. SMTP credentials are incorrect');
            $this->error('4. Firewall blocking port 587');
            $this->error('');
            $this->error('Run: php artisan config:clear');
            $this->error('Then check your .env file settings');
            
            return Command::FAILURE;
        }
    }
}
