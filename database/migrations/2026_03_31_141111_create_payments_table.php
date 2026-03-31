<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // order_id (Foreign Key → orders.id, unique)
            $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('cascade');

            // payment_method (enum: cash, transfer)
            $table->enum('payment_method', ['cash', 'transfer']);

            // payment_status (enum: pending, paid)
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');

            // paid_amount & change_amount (decimal: 12,2, nullable)
            $table->decimal('paid_amount', 12, 2)->nullable();
            $table->decimal('change_amount', 12, 2)->nullable();

            // approved_by (integer, Foreign Key → users.id, nullable)
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');

            // approved_at (timestamp, nullable)
            $table->timestamp('approved_at')->nullable();

            // created_at, updated_at (timestamps)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
