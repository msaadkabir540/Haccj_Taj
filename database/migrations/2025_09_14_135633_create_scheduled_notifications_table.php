<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scheduled_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('employeecode');
            $table->string('title');
            $table->text('body');
            $table->string('day_of_week'); // comma-separated values "1,3,5" or "0,1,2,3,4,5,6"
            $table->time('send_time');
            $table->string('timezone')->default(config('app.timezone', 'UTC'));
            $table->boolean('is_recurring')->default(true);
            $table->timestamp('last_sent_at')->nullable();
            $table->enum('status', ['active','paused'])->default('active');
            $table->timestamps();

            $table->index(['employeecode', 'day_of_week', 'send_time'], 'sn_emp_dow_time_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_notifications');
    }
};