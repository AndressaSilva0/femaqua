<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ferramenta_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('ferramenta_id');
            $table->unsignedBigInteger('tag_id');

            $table->foreign('ferramenta_id')
                  ->references('id')
                  ->on('ferramentas')
                  ->onDelete('cascade');

            $table->foreign('tag_id')
                  ->references('id')
                  ->on('tags')
                  ->onDelete('cascade');

            $table->unique(['ferramenta_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ferramenta_tags');
    }
};
