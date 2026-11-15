<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elasticsearch_alert_notification_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('alert_event_id')->constrained('elasticsearch_alert_events')->cascadeOnDelete();
            $table->string('alert_code', 100)->index();
            $table->string('channel', 30);
            $table->string('status', 20)->index();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamp('sent_at')->nullable()->index();
            $table->text('error')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
            $table->unique(['alert_event_id', 'channel']);
            $table->index(['alert_code', 'channel', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elasticsearch_alert_notification_deliveries');
    }
};
