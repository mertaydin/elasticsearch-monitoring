<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elasticsearch_metric_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->timestamp('collected_at')->index();
            $table->string('elasticsearch_version', 32);
            $table->json('metrics');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elasticsearch_metric_snapshots');
    }
};
