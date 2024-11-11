<?php

use App\Models\Values\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('sum', 16, 2);
            $table->string('currency', 3)->default('RUB');
            $table->string('status', 10)->default(PaymentStatus::PENDING);
            $table->string('token');
            $table->jsonb('payload')->nullable(true);
            $table->foreignUuid('order_id')->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
