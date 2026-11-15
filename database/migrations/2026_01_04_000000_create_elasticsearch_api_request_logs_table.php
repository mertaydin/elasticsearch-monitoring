<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elasticsearch_api_request_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('method', 8);
            $table->string('endpoint', 255);
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->decimal('duration_ms', 10, 2);
            $table->boolean('successful')->index();
            $table->text('error')->nullable();
            $table->timestamps();
            $table->index('created_at');
            $table->index(['endpoint', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elasticsearch_api_request_logs');
    }
};
