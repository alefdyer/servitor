<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('period')->comment('Период действия подписки: P1D, P1W, P1M, P1Y');
            $table->dateTime('start_at')->comment('Время начала действия подписки с точностью до часа');
            $table->dateTime('end_at')->comment('Время окончания действия подписки с точностью до часа');
            $table->foreignId('client_id')->constrained();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
