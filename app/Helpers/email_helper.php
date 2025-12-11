<?php

if (!function_exists('send_email'))
{
    function send_email(string $toEmail, string $toName, string $subject, string $content)
    {
        $payload = [
            'to_email' => $toEmail,
            'to_name' => $toName,
            'subject' => $subject,
            'content' => $content,
            'project_id' => config('otp.trogon_otp.project_id')
        ];
        
        $payload = json_encode($payload);
        
        $ch = curl_init('https://otp.trogon.info/api/send-email');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response === false || $httpCode !== 200) {
            return false;
        }
        
        $data = json_decode($response, true);

        return isset($data['success']) && $data['success'] === true;
    }
}