<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('command_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('server_id')->nullable();
            $table->string('command');
            $table->longText('output')->nullable();
            $table->integer('exit_code')->default(0);
            $table->timestamps();

            // Add foreign key constraint after table creation
            $table->foreign('server_id')
                  ->references('id')
                  ->on('remote_servers')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('command_histories');
    }
};