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
        if (!Schema::hasTable('pengguna')) {
            return;
        }

        $addProfilePicture = !Schema::hasColumn('pengguna', 'profile_picture');
        $addNip = !Schema::hasColumn('pengguna', 'nip');
        $addBidang = !Schema::hasColumn('pengguna', 'bidang');

        if (!($addProfilePicture || $addNip || $addBidang)) {
            return;
        }

        Schema::table('pengguna', function (Blueprint $table) use ($addProfilePicture, $addNip, $addBidang) {
            if ($addProfilePicture) {
                $table->string('profile_picture')->nullable()->after('jabatan');
            }
            if ($addNip) {
                $table->string('nip')->nullable()->after('name');
            }
            if ($addBidang) {
                $table->string('bidang')->nullable()->after('jabatan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('pengguna')) {
            return;
        }

        $columnsToDrop = [];
        foreach (['profile_picture', 'nip', 'bidang'] as $column) {
            if (Schema::hasColumn('pengguna', $column)) {
                $columnsToDrop[] = $column;
            }
        }

        if ($columnsToDrop === []) {
            return;
        }

        Schema::table('pengguna', function (Blueprint $table) use ($columnsToDrop) {
            $table->dropColumn($columnsToDrop);
        });
    }
};
