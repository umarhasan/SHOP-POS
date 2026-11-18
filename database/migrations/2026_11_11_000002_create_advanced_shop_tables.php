<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('unit_conversions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $t->foreignId('from_unit_id')->constrained('units')->restrictOnDelete();
            $t->foreignId('to_unit_id')->constrained('units')->restrictOnDelete();
            $t->decimal('factor',18,6);
            $t->timestamps();
            $t->unique(['product_id','from_unit_id','to_unit_id'], 'unit_conversion_product_units_unique');
        });

        Schema::create('contact_transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $t->string('reference_type');
            $t->unsignedBigInteger('reference_id')->nullable();
            $t->enum('direction',['debit','credit']);
            $t->decimal('amount',14,2);
            $t->string('description');
            $t->dateTime('transacted_at');
            $t->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $t->timestamps();
            $t->index(['contact_id','transacted_at']);
            $t->index(['reference_type','reference_id']);
        });

        Schema::create('expense_allocations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('expense_id')->constrained('expenses')->cascadeOnDelete();
            $t->foreignId('business_unit_id')->constrained('business_units')->cascadeOnDelete();
            $t->decimal('amount',14,2);
            $t->timestamps();
            $t->index(['expense_id','business_unit_id']);
        });

        Schema::create('stock_adjustments', function (Blueprint $t) {
            $t->id();
            $t->string('adjustment_no')->unique();
            $t->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $t->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $t->decimal('system_quantity',18,3);
            $t->decimal('actual_quantity',18,3);
            $t->decimal('difference',18,3);
            $t->string('reason');
            $t->dateTime('adjusted_at');
            $t->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $t->timestamps();
            $t->index(['product_id','adjusted_at']);
        });

        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('action');
            $t->string('table_name')->nullable();
            $t->unsignedBigInteger('record_id')->nullable();
            $t->json('details')->nullable();
            $t->ipAddress('ip_address')->nullable();
            $t->timestamps();
            $t->index(['table_name','record_id']);
            $t->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('expense_allocations');
        Schema::dropIfExists('contact_transactions');
        Schema::dropIfExists('unit_conversions');
    }
};
