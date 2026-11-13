<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elasticsearch_alert_events', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 100)->index();
            $table->string('severity', 20)->index();
            $table->string('title');
            $table->text('message')->nullable();
            $table->json('value')->nullable();
            $table->json('threshold')->nullable();
            $table->timestamp('first_seen_at')->index();
            $table->timestamp('last_seen_at')->index();
            $table->timestamp('resolved_at')->nullable()->index();
            $table->unsignedInteger('occurrences')->default(1);
            $table->timestamps();
            $table->index(['resolved_at', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elasticsearch_alert_events');
    }
};
