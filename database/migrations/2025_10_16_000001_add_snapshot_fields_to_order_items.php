<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // If the order_items table exists, add missing snapshot columns.
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'product_name')) {
                    $table->string('product_name')->nullable()->after('product_id');
                }
                if (!Schema::hasColumn('order_items', 'product_image')) {
                    $table->string('product_image')->nullable()->after('product_name');
                }
                if (!Schema::hasColumn('order_items', 'product_type')) {
                    $table->string('product_type')->nullable()->after('product_image');
                }
                if (!Schema::hasColumn('order_items', 'product_reference')) {
                    $table->string('product_reference')->nullable()->after('product_type');
                }
                if (!Schema::hasColumn('order_items', 'product_color_name')) {
                    $table->string('product_color_name')->nullable()->after('product_reference');
                }
                if (!Schema::hasColumn('order_items', 'product_size')) {
                    $table->string('product_size')->nullable()->after('product_color_name');
                }
            });
            return;
        }

        // If the table doesn't exist yet (e.g., running this migration on a fresh DB),
        // create it with essential columns and snapshots so migration can run safely.
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 2);
            // snapshot columns
            $table->string('product_name')->nullable();
            $table->string('product_image')->nullable();
            $table->string('product_type')->nullable();
            $table->string('product_reference')->nullable();
            $table->string('product_color_name')->nullable();
            $table->string('product_size')->nullable();
            $table->timestamps();
            // foreign keys (best-effort; if orders/products tables exist they will be created)
            if (Schema::hasTable('orders')) {
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            }
            if (Schema::hasTable('products')) {
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            }
        });
    }

    public function down() {
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (Schema::hasColumn('order_items', 'product_name')) {
                    $table->dropColumn('product_name');
                }
                if (Schema::hasColumn('order_items', 'product_image')) {
                    $table->dropColumn('product_image');
                }
                if (Schema::hasColumn('order_items', 'product_type')) {
                    $table->dropColumn('product_type');
                }
                if (Schema::hasColumn('order_items', 'product_reference')) {
                    $table->dropColumn('product_reference');
                }
                if (Schema::hasColumn('order_items', 'product_color_name')) {
                    $table->dropColumn('product_color_name');
                }
                if (Schema::hasColumn('order_items', 'product_size')) {
                    $table->dropColumn('product_size');
                }
            });
        }
    }
};
