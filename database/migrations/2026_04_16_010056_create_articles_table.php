<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('contenu');
            $table->string('url_media')->nullable();
            $table->enum('type_media', ['image', 'video'])->default('image');
            $table->string('categorie')->default('Général');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('statut', ['publie', 'brouillon', 'archive'])->default('publie');
            $table->unsignedInteger('vues')->default(0);
            $table->unsignedInteger('nb_likes')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('articles'); }
};
