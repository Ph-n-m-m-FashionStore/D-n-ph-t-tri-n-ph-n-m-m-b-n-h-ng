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
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_type', 50)->default('clothing')->after('description');
            $table->string('color', 50)->nullable()->after('size');
            $table->integer('reference_id')->nullable()->after('color');
            // $table->boolean('is_active')->default(true)->after('image_url'); // Already exists
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['product_type', 'color', 'reference_id', 'updated_at']);
            // $table->dropColumn('is_active'); // Do not drop, as it is part of the original table
        });
    }
};
