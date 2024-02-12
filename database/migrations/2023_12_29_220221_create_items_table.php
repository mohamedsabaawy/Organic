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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en');
            $table->longText('details');
            $table->longText('details_en');
            $table->string('icon');
            $table->longText('manual');
            $table->longText('manual_en');
            $table->date('production_date');
            $table->enum('available',['active','nonActive'])->default('active');
            $table->double('price',10,2);
            $table->double('discount',10,2);
            $table->boolean('special')->nullable();
            $table->foreignId('category_id')->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
