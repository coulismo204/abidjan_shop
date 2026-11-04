<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Types de chaussures : baskets, mocassins, sandales…
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description', 255)->nullable();
            $table->string('silhouette', 20)->default('basket'); // dessin affiché quand un produit n'a pas de photo
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('hex', 7);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        // Un produit = un modèle dans un coloris. Les pointures et leur stock sont dans product_sizes.
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('color_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('price'); // FCFA
            $table->unsignedInteger('compare_at_price')->nullable(); // ancien prix, affiché barré
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();
            $table->index(['is_visible', 'price']);
        });

        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('size'); // pointure européenne
            $table->unsignedSmallInteger('stock')->default(0);
            $table->timestamps();
            $table->unique(['product_id', 'size']);
            $table->index(['size', 'stock']);
        });

        Schema::create('product_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();
            $table->string('status', 20)->default('new')->index();
            $table->string('customer_name', 120);
            $table->string('phone', 20);
            $table->string('commune', 60);
            $table->string('address', 255);
            $table->string('payment_method', 20);
            $table->string('note', 1000)->nullable();
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('delivery_fee')->nullable(); // null = à confirmer avec le client
            $table->unsignedInteger('total');
            $table->timestamp('whatsapp_opened_at')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });

        // Les noms et prix sont copiés : la commande reste lisible même si le produit change ou disparaît.
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_size_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('brand_name')->nullable();
            $table->string('color_name')->nullable();
            $table->unsignedTinyInteger('size');
            $table->unsignedInteger('unit_price');
            $table->unsignedSmallInteger('quantity');
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('product_photos');
        Schema::dropIfExists('product_sizes');
        Schema::dropIfExists('products');
        Schema::dropIfExists('colors');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
    }
};
