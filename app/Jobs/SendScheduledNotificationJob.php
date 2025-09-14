<?php

namespace App\Jobs;

use App\Models\ScheduledNotification;
use App\Models\User;
use App\Services\SendNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendScheduledNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $scheduledId) {}

    public function handle(SendNotification $fcm): void
    {
        $schedule = ScheduledNotification::find($this->scheduledId);
        if (!$schedule || $schedule->status !== 'active') {
            echo "ScheduledNotification {$this->scheduledId} is inactive or missing.\n";
            \Log::warning("ScheduledNotification {$this->scheduledId} is inactive or missing.");
            return;
        }

        $token = User::where('employeecode', $schedule->employeecode)->value('fem_token_mobile');

        if (empty($token)) {
            echo "No fem_token_mobile found for employeecode {$schedule->employeecode}\n";
            \Log::warning("No fem_token_mobile found for employeecode {$schedule->employeecode}");
            return;
        }

        try {
            $response = $fcm->sendNotification($token, $schedule->title, $schedule->body);

            if (isset($response['name'])) {
                echo "✅ Notification sent to {$schedule->employeecode}\n";
                \Log::info("✅ Notification sent to {$schedule->employeecode}", ['fcm_response' => $response]);
            } else {
                echo "❌ Failed to send to {$schedule->employeecode}\n";
                \Log::error("❌ Failed to send notification to {$schedule->employeecode}", ['fcm_response' => $response]);
            }
        } catch (\Throwable $e) {
            echo "❌ Exception sending to {$schedule->employeecode}: {$e->getMessage()}\n";
            \Log::error("❌ Exception sending notification", [
                'employeecode' => $schedule->employeecode,
                'error' => $e->getMessage(),
            ]);
        }

        $schedule->update(['last_sent_at' => now()]);
    }
}
