<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['client', 'prestataire', 'admin'])->default('client')->after('password');
            $table->string('phone')->nullable()->after('role');
            $table->string('avatar')->nullable()->after('phone');
            $table->text('bio')->nullable()->after('avatar');
            $table->string('address')->nullable()->after('bio');
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete()->after('address');
            $table->boolean('is_verified')->default(false)->after('city_id');
            $table->boolean('is_active')->default(true)->after('is_verified');
            $table->decimal('rating_avg', 3, 2)->default(0)->after('is_active');
            $table->integer('total_reviews')->default(0)->after('rating_avg');
            $table->softDeletes();
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropColumn(['role','phone','avatar','bio','address','city_id','is_verified','is_active','rating_avg','total_reviews','deleted_at']);
        });
    }
};
