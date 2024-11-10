<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('item')->comment('Предмет заказа');
            $table->decimal('sum', 16, 2);
            $table->string('currency', 3)->default('RUB');
            $table->jsonb('content')->nullable(true)->comment('Дополнительные параметры заказа');
            $table->foreignId('client_id')->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
