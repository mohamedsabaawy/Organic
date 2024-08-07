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
        Schema::create('invoice_item', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('item_id');
            $table->bigInteger('offer_id');
            $table->foreignId('invoice_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->tinyInteger('count');
            $table->double('price',10,2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_item');
    }
};
