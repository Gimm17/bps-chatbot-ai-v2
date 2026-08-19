<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->nullable()->index();
            $table->string('rating', 20); // helpful, not_helpful
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->string('role', 20); // user, assistant
            $table->text('content');
            $table->json('citations')->nullable();
            $table->string('status', 50)->default('answered');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('feedback');
    }
};
