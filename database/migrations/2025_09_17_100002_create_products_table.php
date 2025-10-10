<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->string('brand', 100)->nullable(); // Thương hiệu
            $table->string('size', 20)->nullable(); // Size
            $table->string('gender', 10)->nullable(); // Giới tính: nam, nu, unisex
            $table->decimal('price', 12, 2);
            $table->integer('stock')->default(0); // Tồn kho
            $table->string('image_url', 255)->default('images/default.png');
            $table->boolean('is_active')->default(true); // Trạng thái bán
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down() {
        Schema::dropIfExists('products');
    }
};
