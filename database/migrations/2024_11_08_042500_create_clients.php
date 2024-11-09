<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->timestamps();
        });

        Schema::create('devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('model');
            $table->foreignId('client_id')->constrained();
            $table->timestamps();
        });

        Schema::create('emails', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->foreignId('client_id')->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emails');
        Schema::dropIfExists('devices');
        Schema::dropIfExists('clients');
    }
};
