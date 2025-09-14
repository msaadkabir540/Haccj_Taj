<?php

namespace App\Http\Controllers;

use App\Services\SendNotification;
use Illuminate\Http\Request;

use App\Models\User;

class NotificationController extends Controller
{
    public function testNotification(Request $request, SendNotification $fcm)
    {
        // Either from request or fallback
        $fcmToken = $request->input('token', 'faDxNnehjuzpKuGQYNv7G1:APA91bEWC9ul45rgXlHonKBqJ2JP5hI9RKmOn7x4bOkKMw7BBFpLuy0IzN5G_0E-__BnSP4kwdkGlqOpP7onu6x7Kt8wD8daI3k_8UX6IVVMWeYBUNSRYnQ');

        // Send notification
        $response = $fcm->sendNotification(
            $fcmToken,
            "Test Title",
            "Hello from Laravel via FCM v1 API!"
        );

        return response()->json($response);
    }

  public function broadcastTest(Request $request, SendNotification $fcm)
{
    try {
        $title = $request->input('title', 'Test Title');
        $body  = $request->input('body',  'Hello from Laravel via FCM v1 API!');

        // 1) Collect all non-empty tokens (unique)
        $tokens = User::query()
            ->whereNotNull('fem_token_mobile')
            ->where('fem_token_mobile', '!=', '')
            ->pluck('fem_token_mobile')
            ->unique()
            ->values();

        if ($tokens->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'No FCM tokens found.',
                'sent'    => 0,
                'failed'  => 0,
            ], 200);
        }

        // 2) Send notifications in manageable chunks
        $sent   = 0;
        $failed = 0;
        $errors = [];

        foreach ($tokens->chunk(500) as $chunk) {
            foreach ($chunk as $token) {
                try {
                    $resp = $fcm->sendNotification($token, $title, $body);

                    // If response indicates success (you can check resp if needed)
                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                    $errors[] = [
                        'token' => $token,
                        'error' => $e->getMessage(),
                    ];
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Broadcast completed.',
            'sent'    => $sent,
            'failed'  => $failed,
            'errors'  => $errors, // good for debugging
        ], 200);

    } catch (\Throwable $e) {
        // Catch any top-level exceptions
        return response()->json([
            'status'  => false,
            'message' => 'Broadcast failed due to server error.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

}
