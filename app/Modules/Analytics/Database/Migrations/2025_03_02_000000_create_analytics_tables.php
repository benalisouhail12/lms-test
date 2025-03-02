<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('value');
            $table->float('previous_value')->nullable();
            $table->string('unit')->nullable();
            $table->string('period');
            $table->datetime('date_recorded');
            $table->timestamps();

            $table->index(['name', 'period', 'date_recorded']);
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->json('metrics');
            $table->string('period');
            $table->json('data')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_metrics');
        Schema::dropIfExists('reports');
    }
};
