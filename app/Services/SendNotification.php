<?php

namespace App\Services;

use Google\Client;
use Illuminate\Support\Facades\Http;

class SendNotification
{
    public function sendNotification($fcmToken, $title, $body)
    {
        // Load Google Client with service account
        $client = new Client();
        $client->setAuthConfig(base_path(env('FIREBASE_CREDENTIALS')));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        // Generate access token
        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

        // Your Firebase Project ID
        $projectId = env('FIREBASE_PROJECT_ID');

        // Call FCM v1 API
        $response = Http::withToken($accessToken)
            ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                "message" => [
                    "token" => $fcmToken,
                    "notification" => [
                        "title" => $title,
                        "body"  => $body,
                    ],
                ],
            ]);

        return $response->json();
    }
}
