<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('site');
            $table->string('city');
            $table->integer('area');
            $table->enum('type',['home','villa','warehouse']);
            $table->integer('number_of_room');
            $table->string('description');
            $table->boolean('favorite')->nullable()->default(false);
            $table->integer('price');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
