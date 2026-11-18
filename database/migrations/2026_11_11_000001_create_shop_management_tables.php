<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        // Create users before any table that references users.id.
        // If Laravel's default users migration already ran, do not recreate it.
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $t) {
                $t->id();
                $t->string('name');
                $t->string('email')->unique();
                $t->timestamp('email_verified_at')->nullable();
                $t->string('password');
                $t->rememberToken();
                $t->timestamps();
            });
        }

        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $t) {
                $t->string('role')->default('admin');
            });
        }

        Schema::create('business_units', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('code')->unique();
            $t->string('owner_name')->nullable(); $t->boolean('is_active')->default(true); $t->timestamps();
        });

        Schema::create('categories', function (Blueprint $t) {
            $t->id(); $t->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $t->string('name'); $t->string('slug')->unique(); $t->boolean('is_active')->default(true); $t->timestamps();
        });

        Schema::create('units', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('short_name',20)->unique();
            $t->string('dimension')->default('quantity'); $t->decimal('base_factor',18,6)->default(1);
            $t->boolean('is_active')->default(true); $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('business_unit_id')->nullable()->constrained('business_units')->nullOnDelete();
            $t->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $t->string('name'); $t->string('sku')->nullable()->unique(); $t->string('barcode')->nullable()->unique();
            $t->string('product_type')->default('standard'); $t->foreignId('base_unit_id')->constrained('units');
            $t->decimal('purchase_price',14,2)->default(0); $t->decimal('sale_price',14,2)->default(0);
            $t->decimal('alert_quantity',18,3)->default(0); $t->json('attributes')->nullable();
            $t->boolean('track_stock')->default(true); $t->boolean('is_active')->default(true);
            $t->text('description')->nullable(); $t->timestamps();
            $t->index(['category_id','is_active']); $t->index('name'); $t->index('business_unit_id');
        });

        Schema::create('product_variants', function (Blueprint $t) {
            $t->id(); $t->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $t->string('variant_name'); $t->string('sku')->nullable()->unique(); $t->json('attributes')->nullable();
            $t->decimal('purchase_price',14,2)->nullable(); $t->decimal('sale_price',14,2)->nullable();
            $t->boolean('is_active')->default(true); $t->timestamps();
        });

        Schema::create('contacts', function (Blueprint $t) {
            $t->id(); $t->enum('type',['customer','supplier','both'])->default('customer');
            $t->string('name'); $t->string('phone',40)->nullable(); $t->string('email')->nullable();
            $t->text('address')->nullable(); $t->decimal('opening_balance',14,2)->default(0);
            $t->boolean('is_active')->default(true); $t->timestamps();
            $t->index(['type','is_active']); $t->index('name');
        });

        Schema::create('stock_balances', function (Blueprint $t) {
            $t->id(); $t->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $t->foreignId('product_variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
            $t->decimal('quantity',18,3)->default(0); $t->decimal('avg_cost',14,4)->default(0); $t->timestamps();
            $t->unique(['product_id','product_variant_id'], 'stock_balances_product_variant_unique');
        });

        Schema::create('sales', function (Blueprint $t) {
            $t->id(); $t->string('invoice_no')->unique();
            $t->foreignId('business_unit_id')->nullable()->constrained('business_units')->nullOnDelete();
            $t->foreignId('customer_id')->nullable()->constrained('contacts')->nullOnDelete();
            $t->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $t->dateTime('sold_at'); $t->decimal('subtotal',14,2)->default(0); $t->decimal('discount',14,2)->default(0);
            $t->decimal('tax',14,2)->default(0); $t->decimal('total',14,2)->default(0);
            $t->decimal('paid',14,2)->default(0); $t->decimal('due',14,2)->default(0);
            $t->string('payment_status')->default('unpaid'); $t->string('payment_method')->default('cash');
            $t->text('notes')->nullable(); $t->timestamps(); $t->index('sold_at');
            $t->index(['business_unit_id','sold_at']); $t->index('payment_status');
        });

        Schema::create('sale_items', function (Blueprint $t) {
            $t->id(); $t->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $t->foreignId('business_unit_id')->nullable()->constrained('business_units')->nullOnDelete();
            $t->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $t->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $t->string('product_name'); $t->string('unit_name'); $t->decimal('quantity',18,3);
            $t->decimal('unit_price',14,2); $t->decimal('unit_cost',14,4)->default(0);
            $t->decimal('discount',14,2)->default(0); $t->decimal('line_total',14,2); $t->timestamps();
            $t->index('product_id');
        });

        Schema::create('purchases', function (Blueprint $t) {
            $t->id(); $t->string('purchase_no')->unique();
            $t->foreignId('business_unit_id')->nullable()->constrained('business_units')->nullOnDelete();
            $t->foreignId('supplier_id')->nullable()->constrained('contacts')->nullOnDelete();
            $t->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $t->dateTime('purchased_at'); $t->decimal('subtotal',14,2)->default(0);
            $t->decimal('discount',14,2)->default(0); $t->decimal('total',14,2)->default(0);
            $t->decimal('paid',14,2)->default(0); $t->decimal('due',14,2)->default(0);
            $t->string('payment_status')->default('unpaid'); $t->text('notes')->nullable(); $t->timestamps();
            $t->index('purchased_at'); $t->index(['business_unit_id','purchased_at']);
        });

        Schema::create('purchase_items', function (Blueprint $t) {
            $t->id(); $t->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $t->foreignId('business_unit_id')->nullable()->constrained('business_units')->nullOnDelete();
            $t->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $t->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $t->string('product_name'); $t->string('unit_name'); $t->decimal('quantity',18,3);
            $t->decimal('unit_cost',14,4); $t->decimal('line_total',14,2); $t->timestamps();
            $t->index('product_id');
        });

        Schema::create('stock_movements', function (Blueprint $t) {
            $t->id(); $t->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $t->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $t->string('reference_type'); $t->unsignedBigInteger('reference_id')->nullable();
            $t->enum('direction',['in','out','adjustment']); $t->decimal('quantity',18,3);
            $t->decimal('unit_cost',14,4)->default(0); $t->string('unit_name'); $t->text('note')->nullable();
            $t->foreignId('user_id')->constrained('users')->restrictOnDelete(); $t->timestamps();
            $t->index(['reference_type','reference_id']); $t->index(['product_id','created_at']);
        });

        Schema::create('payments', function (Blueprint $t) {
            $t->id(); $t->string('reference_type'); $t->unsignedBigInteger('reference_id');
            $t->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $t->enum('direction',['in','out']); $t->decimal('amount',14,2); $t->string('method')->default('cash');
            $t->dateTime('paid_at'); $t->string('reference_no')->nullable(); $t->text('notes')->nullable();
            $t->foreignId('user_id')->constrained('users')->restrictOnDelete(); $t->timestamps();
            $t->index(['reference_type','reference_id']); $t->index('paid_at');
        });

        Schema::create('expenses', function (Blueprint $t) {
            $t->id(); $t->foreignId('business_unit_id')->nullable()->constrained('business_units')->nullOnDelete();
            $t->string('title'); $t->string('category')->default('other'); $t->decimal('amount',14,2);
            $t->date('expense_date'); $t->string('payment_method')->default('cash'); $t->text('notes')->nullable();
            $t->foreignId('user_id')->constrained('users')->restrictOnDelete(); $t->timestamps();
            $t->index(['business_unit_id','expense_date']);
        });

        Schema::create('cash_transactions', function (Blueprint $t) {
            $t->id(); $t->enum('direction',['in','out']); $t->string('source_type');
            $t->unsignedBigInteger('source_id')->nullable(); $t->decimal('amount',14,2);
            $t->string('description'); $t->dateTime('transacted_at');
            $t->foreignId('user_id')->constrained('users')->restrictOnDelete(); $t->timestamps();
            $t->index(['source_type','source_id']); $t->index('transacted_at');
        });

        Schema::create('returns', function (Blueprint $t) {
            $t->id(); $t->enum('return_type',['sale','purchase']); $t->string('return_no')->unique();
            $t->unsignedBigInteger('original_id')->nullable();
            $t->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $t->decimal('total',14,2); $t->dateTime('returned_at'); $t->text('reason')->nullable();
            $t->foreignId('user_id')->constrained('users')->restrictOnDelete(); $t->timestamps();
            $t->index(['return_type','original_id']);
        });

        Schema::create('return_items', function (Blueprint $t) {
            $t->id(); $t->foreignId('return_id')->constrained('returns')->cascadeOnDelete();
            $t->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $t->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $t->decimal('quantity',18,3); $t->decimal('unit_price',14,2);
            $t->decimal('line_total',14,2); $t->timestamps();
        });

        Schema::create('settings', function (Blueprint $t) {
            $t->id(); $t->string('key')->unique(); $t->longText('value')->nullable(); $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'settings','return_items','returns','cash_transactions','expenses','payments',
            'stock_movements','purchase_items','purchases','sale_items','sales',
            'stock_balances','contacts','product_variants','products','units',
            'categories','business_units'
        ] as $table) {
            Schema::dropIfExists($table);
        }
        // Users is intentionally retained for Laravel authentication.
    }
};
