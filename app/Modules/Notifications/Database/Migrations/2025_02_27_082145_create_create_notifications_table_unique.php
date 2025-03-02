<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->json('meta_data')->nullable();
            $table->string('status')->default('unread');
            $table->string('priority')->default('medium');
            $table->timestamp('expires_at')->nullable();
            $table->string('group_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status', 'created_at']);
            $table->index('type');
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('channels');
            $table->json('notification_types');
            $table->json('quiet_hours')->nullable();
            $table->json('digest_settings')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });


        Schema::create('notification_history', function (Blueprint $table) {
            $table->id();
            $table->uuid('notification_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('channel');
            $table->string('status');
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index(['notification_id', 'user_id', 'channel']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_history');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('notifications');
    }
};


