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
        Schema::create('birthday', function (Blueprint $table) {
            $table->id();
	        $table->bigInteger('external_id')->unsigned()->unique();
	        $table->string('name');
	        $table->date('date');
            $table->timestamps();

	        $table->index(['external_id', 'date', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birthday');
    }
};
