<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailController extends Controller
{
    public function sendTestEmail(Request $request)
    {
        try {
            $toEmail = $request->email ?? 'sabijonedjoshua@gmail.com';
            
            Log::info('Test email attempt to: ' . $toEmail);
            Log::info('Mail configuration: ', [
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'username' => config('mail.mailers.smtp.username'),
            ]);

            Mail::raw('This is a test email from FlexiSpace', function ($message) use ($toEmail) {
                $message->to($toEmail)
                        ->subject('Test Email - FlexiSpace')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info('Test email sent successfully to: ' . $toEmail);

            return response()->json([
                'message' => 'Test email sent successfully to ' . $toEmail,
                'config' => [
                    'mailer' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption'),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Test email failed: ' . $e->getMessage());
            Log::error('Full error: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Test email failed',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
}
