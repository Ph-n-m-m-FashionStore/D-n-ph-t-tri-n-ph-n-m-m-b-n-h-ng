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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'tax_id')) {
                $table->string('tax_id')->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('users', 'owner_address')) {
                $table->string('owner_address')->nullable()->after('tax_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'owner_address')) {
                $table->dropColumn('owner_address');
            }
            if (Schema::hasColumn('users', 'tax_id')) {
                $table->dropColumn('tax_id');
            }
            if (Schema::hasColumn('users', 'postal_code')) {
                $table->dropColumn('postal_code');
            }
        });
    }
};
