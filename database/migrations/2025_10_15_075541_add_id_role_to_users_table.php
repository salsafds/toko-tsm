<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom id_role
            $table->string('id_role', 10)->nullable()->after('id_user');
            
            // Tambahkan foreign key ke tabel role
            $table->foreign('id_role')->references('id_role')->on('role')->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus relasi dan kolom saat rollback
            $table->dropForeign(['id_role']);
            $table->dropColumn('id_role');
        });
    }
};
