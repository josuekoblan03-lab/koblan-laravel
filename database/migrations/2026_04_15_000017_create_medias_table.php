<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestation_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->enum('type', ['image', 'video'])->default('image');
            $table->boolean('is_main')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('medias'); }
};
