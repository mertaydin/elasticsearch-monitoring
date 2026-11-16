<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elasticsearch_dashboard_notes', function (Blueprint $table): void {
            $table->id();
            $table->string('user_id', 191)->nullable()->index();
            $table->string('chart_key', 64)->index();
            $table->timestamp('noted_at')->index();
            $table->text('note');
            $table->timestamps();
            $table->index(['user_id', 'noted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elasticsearch_dashboard_notes');
    }
};
