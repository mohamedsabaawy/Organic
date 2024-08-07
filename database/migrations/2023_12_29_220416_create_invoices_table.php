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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('address_id')->constrained('addresses');
            $table->double('price',10,2)->nullable();
            $table->double('delivery_price',10,2)->nullable();
            $table->enum('payment_type',['cash','visa'])->nullable();
            $table->enum('status',['pending','underPrepare','onTheWay','delivery'])->default('padding');
            $table->integer('payment_code')->nullable();
            $table->double('amount',10,2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
