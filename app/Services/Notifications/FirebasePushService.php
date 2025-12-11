<?php

namespace App\Services\Notifications;

use App\Services\Traits\CacheableService;
use App\Services\UserDevices\UserDeviceService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebasePushService
{
    use CacheableService;
    protected string $projectId;
    protected string $credentialsPath;
    // Firebase batch limit (max 500 messages per batch)
    protected const BATCH_SIZE = 500;

    public function __construct(protected UserDeviceService $userDeviceService)
    {
        $this->projectId = config('firebase.project_id');
        $this->credentialsPath = config('firebase.credentials.path');
    }

    public function send($users, string $title, string $body, array $data = []): array
    {
        // Get FCM tokens from users
        $tokens = $this->getFcmTokens($users);
        
        if (empty($tokens)) {
            return ['success' => 0, 'failed' => 0, 'total' => 0];
        }

        // Send in batches (same message to all)
        return $this->sendBatch($tokens, $title, $body, $data);
    }

    public function sendCustom(array $notifications): array
    {
        if (empty($notifications)) {
            return ['success' => 0, 'failed' => 0, 'total' => 0];
        }

        // Get user IDs
        $userIds = array_column($notifications, 'user_id');
        
        // Get all devices for these users
        $devices = $this->userDeviceService->getActiveDevicesForUsers($userIds);
        Log::info('Devices: ' . json_encode($devices));
        // Group notifications by user and prepare messages
        $messages = [];
        foreach ($notifications as $notification) {
            $userId = $notification['user_id'];
            $userDevices = $devices->where('user_id', $userId);
            
            foreach ($userDevices as $device) {
                $messages[] = [
                    'token' => $device->fcm_token,
                    'title' => $notification['title'],
                    'body' => $notification['body'],
                    'data' => $notification['data'] ?? [],
                ];
            }
        }
        Log::info('Messages: ' . json_encode($messages));
        if (empty($messages)) {
            return ['success' => 0, 'failed' => 0, 'total' => 0];
        }

        // Send in batches
        return $this->sendBatchCustom($messages);
    }

    /**
     * Get FCM tokens from users
     */
    protected function getFcmTokens($users): array
    {
        $userIds = is_array($users) 
            ? (is_numeric($users[0]) ? $users : array_column($users, 'id'))
            : [$users->id ?? $users];

        return $this->userDeviceService->getFcmTokensForUsers($userIds);
    }
    /**
     * Send same message to multiple tokens in batches
     */
    protected function sendBatch(array $tokens, string $title, string $body, array $data): array
    {
        $accessToken = $this->getAccessToken();
        $totalSuccess = 0;
        $totalFailed = 0;
        $invalidTokens = [];

        // Split tokens into batches of 500
        $batches = array_chunk($tokens, self::BATCH_SIZE);

        foreach ($batches as $batch) {
            // Prepare messages for this batch
            $messages = [];
            foreach ($batch as $token) {
                $messages[] = [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => $this->formatData($data),
                    ],
                ];
            }

            // Send batch
            $result = $this->sendBatchToFirebase($accessToken, $messages);
            $totalSuccess += $result['success'];
            $totalFailed += $result['failed'];
            $invalidTokens = array_merge($invalidTokens, $result['invalid_tokens']);
        }

        // Mark invalid tokens as inactive
        if (!empty($invalidTokens)) {
            foreach ($invalidTokens as $token) {
                $this->userDeviceService->markDeviceInactive(null, null, $token);
            }
        }

        return [
            'success' => $totalSuccess,
            'failed' => $totalFailed,
            'total' => count($tokens),
            'invalid_tokens' => count($invalidTokens),
        ];
    }

    /**
     * Send custom messages in batches
     */
    protected function sendBatchCustom(array $messages): array
    {
        $accessToken = $this->getAccessToken();
        $totalSuccess = 0;
        $totalFailed = 0;
        $invalidTokens = [];

        // Split messages into batches of 500
        $batches = array_chunk($messages, self::BATCH_SIZE);

        foreach ($batches as $batch) {
            // Prepare Firebase format
            $firebaseMessages = [];
            foreach ($batch as $message) {
                $firebaseMessages[] = [
                    'message' => [
                        'token' => $message['token'],
                        'notification' => [
                            'title' => $message['title'],
                            'body' => $message['body'],
                        ],
                        'data' => $this->formatData($message['data']),
                    ],
                ];
            }

            // Send batch
            $result = $this->sendBatchToFirebase($accessToken, $firebaseMessages);
            $totalSuccess += $result['success'];
            $totalFailed += $result['failed'];
            $invalidTokens = array_merge($invalidTokens, $result['invalid_tokens']);
        }

        // Mark invalid tokens as inactive
        if (!empty($invalidTokens)) {
            foreach ($invalidTokens as $token) {
                $this->userDeviceService->markDeviceInactive(null, null, $token);
            }
        }

        return [
            'success' => $totalSuccess,
            'failed' => $totalFailed,
            'total' => count($messages),
            'invalid_tokens' => count($invalidTokens),
        ];
    }

    /**
     * Send batch to Firebase using sendAll API
     * 
     * @param string $accessToken
     * @param array $messages Array of message objects
     * @return array ['success' => int, 'failed' => int, 'invalid_tokens' => array]
     */
    protected function sendBatchToFirebase(string $accessToken, array $messages): array
    {
        $successCount = 0;
        $failedCount = 0;
        $invalidTokens = [];
        Log::info('Firebase batch send request: ' . json_encode($messages));
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post(
                "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:sendAll",
                [
                    'messages' => $messages,
                ]
            );

            if ($response->successful()) {
                $result = $response->json();
                Log::info('Firebase batch send result: ' . json_encode($result));
                // Process responses
                if (isset($result['responses'])) {
                    foreach ($result['responses'] as $index => $responseItem) {
                        if (isset($responseItem['name'])) {
                            // Success
                            $successCount++;
                        } else {
                            // Error
                            $failedCount++;
                            $error = $responseItem['error'] ?? [];
                            $errorCode = $error['code'] ?? null;
                            
                            // Check if token is invalid
                            if (in_array($errorCode, [404, 400])) {
                                // Extract token from original message
                                $token = $messages[$index]['message']['token'] ?? null;
                                if ($token) {
                                    $invalidTokens[] = $token;
                                }
                            }
                            
                            Log::warning('Firebase batch message failed', [
                                'error' => $error,
                                'index' => $index,
                            ]);
                        }
                    }
                }
            } else {
                // Entire batch failed
                $error = $response->json();
                Log::error('Firebase batch send failed', [
                    'status' => $response->status(),
                    'error' => $error,
                ]);
                $failedCount = count($messages);
            }
        } catch (\Exception $e) {
            Log::error('Firebase batch send exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $failedCount = count($messages);
        }

        return [
            'success' => $successCount,
            'failed' => $failedCount,
            'invalid_tokens' => $invalidTokens,
        ];
    }

    /**
     * Send to Firebase API
     */
    // protected function sendToFirebase(array $tokens, string $title, string $body, array $data): bool
    // {
    //     $accessToken = $this->getAccessToken();
    //     $successCount = 0;

    //     foreach ($tokens as $token) {
    //         try {
    //             $response = Http::withHeaders([
    //                 'Authorization' => 'Bearer ' . $accessToken,
    //                 'Content-Type' => 'application/json',
    //             ])->post(
    //                 "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
    //                 [
    //                     'message' => [
    //                         'token' => $token,
    //                         'notification' => [
    //                             'title' => $title,
    //                             'body' => $body,
    //                         ],
    //                         'data' => $this->formatData($data),
    //                     ],
    //                 ]
    //             );

    //             if ($response->successful()) {
    //                 $successCount++;
    //             } else {
    //                 $error = $response->json();
    //                 // Remove invalid tokens
    //                 if (isset($error['error']['status']) && in_array($error['error']['status'], ['NOT_FOUND', 'INVALID_ARGUMENT'])) {
                        
    //                     $this->userDeviceService->markDeviceInactive(null, null, $token);
    //                 }
    //             }
    //         } catch (\Exception $e) {
    //             Log::error('Firebase push error', ['error' => $e->getMessage()]);
    //         }
    //     }

    //     return $successCount > 0;
    // }

    /**
     * Format data (all values must be strings)
     */
    protected function formatData(array $data): array
    {
        $formatted = [];
        foreach ($data as $key => $value) {
            $formatted[$key] = is_array($value) ? json_encode($value) : (string) $value;
        }
        return $formatted;
    }

    /**
     * Get Firebase access token
     */
    protected function getAccessToken(): string
    {
        return '1234567890';
        return $this->remember('firebase_access_token', function () {
            $credentials = json_decode(file_get_contents($this->credentialsPath), true);
            $jwt = $this->createJWT($credentials);
            
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            return $response->json()['access_token'];
        },50 * 60);
    }

    /**
     * Create JWT for service account
     */
    protected function createJWT(array $credentials): string
    {
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $now = time();
        $payload = [
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now,
        ];

        $headerEncoded = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
        $payloadEncoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
        
        openssl_sign(
            $headerEncoded . '.' . $payloadEncoded,
            $signature,
            $credentials['private_key'],
            OPENSSL_ALGO_SHA256
        );
        
        $signatureEncoded = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
        
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }
}
