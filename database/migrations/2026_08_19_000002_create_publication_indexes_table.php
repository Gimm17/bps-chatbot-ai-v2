<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('publication_indexes', function (Blueprint $table) {
            $table->string('id')->primary(); // pub_id (e.g. b57c1acc49ac0704b6f08935)
            $table->string('domain_id')->index(); // 7200, 0000, 3200
            $table->string('domain_name');
            $table->string('title')->index();
            $table->date('rl_date')->nullable()->index();
            $table->text('pdf_url')->nullable();
            $table->text('portal_url')->nullable();
            $table->string('file_path')->nullable();
            $table->mediumText('extracted_text')->nullable();
            $table->text('abstract')->nullable();
            $table->integer('page_count')->default(0);
            $table->integer('file_size_kb')->default(0);
            $table->string('status')->default('completed'); // pending, processing, completed, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publication_indexes');
    }
};
