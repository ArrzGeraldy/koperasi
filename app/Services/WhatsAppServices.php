<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppservices
{
    protected static $token;
    protected static $noAdmin;

    private static function init()
    {
        if (!self::$token) {
            // Sangat disarankan lewat config(), tapi env() langsung juga bisa jika tidak di-cache
            self::$token = env('FONNTE_API_TOKEN');
        }
        if (!self::$noAdmin) {
            self::$noAdmin = env('NO_ADMIN');
        }
    }

    private static function normalizePhoneNumber($no): string
    {
        $no = (string) $no;

        // Keep digits only
        $digits = preg_replace('/\D+/', '', $no) ?? '';

        if ($digits === '') {
            return '';
        }

        // Handle common Indonesian formats:
        // - 08xxxxxxxxxx  -> 62xxxxxxxxxxx
        // - 8xxxxxxxxxx   -> 62xxxxxxxxxxx
        // - 62xxxxxxxxxx  -> 62xxxxxxxxxx
        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        // Fallback: return digits as-is (could be non-ID number)
        return $digits;
    }

    public static function send($no, $message)
    {
        self::init();
        try {
            //code...
            $target = self::normalizePhoneNumber($no);

            if ($target === '') {
                Log::warning('WhatsAppservices::send called with empty/invalid target', ['no' => $no]);
                return;
            }

            $res = Http::withHeaders([
                'Authorization' => self::$token
            ])->post('https://api.fonnte.com/send',[
                "target" => $target,
                "message" => $message,
            ]);
            Log::info($res->body());
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
        }

    }
    public static function sendToAdmin($message)
    {
        self::init();
        try {
            //code...
            $noAdmin = self::$noAdmin;
            $target = self::normalizePhoneNumber($noAdmin);

            if ($target === '') {
                Log::warning('WhatsAppservices::sendToAdmin called with empty/invalid target', ['no' => $noAdmin]);
                return;
            }

            $res = Http::withHeaders([
                'Authorization' => self::$token
            ])->post('https://api.fonnte.com/send',[
                "target" => $target,
                "message" => $message,
            ]);
            Log::info($res->body());
        } catch (\Exception $e) {
            //throw $th;
            Log::error($e->getMessage());
        }

    }
}