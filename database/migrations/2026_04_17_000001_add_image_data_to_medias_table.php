<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute une colonne image_data (LONGBLOB) pour stocker les images
     * directement en base de données — solution pérenne sans stockage externe.
     */
    public function up(): void
    {
        Schema::table('medias', function (Blueprint $table) {
            // LONGTEXT pour stocker le base64 de l'image (jusqu'à 4 GB)
            $table->longText('image_data')->nullable()->after('url');
        });
    }

    public function down(): void
    {
        Schema::table('medias', function (Blueprint $table) {
            $table->dropColumn('image_data');
        });
    }
};
