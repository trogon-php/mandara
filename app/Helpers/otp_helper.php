<?php

use Illuminate\Support\Facades\Log;

if (! function_exists('send_sms_otp')) {
    function send_sms_otp(string $countryCode, string $phoneNumber, string $otp)
    {
        $projectId = config('otp.trogon_otp.project_id');
        $projectUrl = config('otp.trogon_otp.project_url');
        
        // Build POST data using function parameters
        $postData = http_build_query(array(
            'otp' => $otp,
            'country_code' => $countryCode,
            'phone' => $phoneNumber,
            'project_id' => $projectId,
            'project_url' => $projectUrl
        ));

        $ch = curl_init('https://otp.trogon.info/api/send-otp');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: PHP-cURL'
        ));
        
        // Disable SSL verification for self-signed certificates
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Check for errors
        if ($error) {
            Log::error('cURL Error in send_sms_otp: ' . $error);
            return false;
        }
        
        if ($httpCode >= 400) {
            Log::error('HTTP Error in send_sms_otp: ' . $httpCode . ' - ' . $response);
            return false;
        }
        
        return $response;
    }
}
if (!function_exists('send_email_otp')) {
    function send_email_otp(string $email, string $otp)
    {
        return send_email($email, 'User :' . $email, 'MANDARA OTP Verification', 'Your OTP is: ' . $otp);
    }
}

// if (!function_exists('send_sms_otp')) {
//     /**
//      * Send OTP via SMS using 2Factor.in API
//      *
//      * @param string $phoneNumber  e.g. "919876543210"
//      * @param string $otp          e.g. "123456"
//      * @return mixed
//      */
//     function send_sms_otp(string $phoneNumber, string $otp)
//     {
//         // Skip sending dummy OTP in local/testing environments
//         if ($otp === '1234') {
//             return false;
//         }

//         try {
//             $apiKey = config('otp.2factor.api_key');
//             $senderName = config('otp.2factor.sender_name');

//             $phoneNumber = '+' . ltrim($phoneNumber, '+');

//             $url = "https://2factor.in/API/V1/{$apiKey}/SMS/{$phoneNumber}/{$otp}/ApplicationOTP";

//             $fields = [
//                 'username'   => config('otp.2factor.username'),
//                 'password'   => config('otp.2factor.password'),
//                 'sendername' => $senderName,
//                 'mobileno'   => $phoneNumber,
//                 'message'    => $otp,
//             ];

//             $response = Http::asForm()->withHeaders([
//                 'Content-Type' => 'multipart/form-data',
//             ])->post($url, $fields);

//             Log::info('OTP sent', [
//                 'phone' => $phoneNumber,
//                 'otp' => $otp,
//                 'response' => $response->body(),
//             ]);

//             return $response->json();
//         } catch (\Throwable $e) {
//             Log::error('OTP sending failed', [
//                 'phone' => $phoneNumber,
//                 'error' => $e->getMessage(),
//             ]);
//             return false;
//         }
//     }
// }
