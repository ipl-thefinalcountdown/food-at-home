<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Initial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('type', ['C', 'EC', 'ED', 'EM']);
            // C "Customer"; EC "Employee - Cook"; ED "Employee - Deliveryman"; EM "Employee - Manager";
            $table->boolean('blocked')->default(false);
            $table->string('photo_url')->nullable();
            // Date and time when employee has logged in (last login).
            // null if user is not currently logged
            $table->dateTime('logged_at')->nullable();
            // Date and time when employee became available for the last time
            // null if user is not currently available
            $table->dateTime('available_at')->nullable();
            $table->softDeletes();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->primary();
            $table->foreign('id')->references('id')->on('users');
            $table->string('address');
            $table->string('phone', 20);
            $table->string('nif', 9)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('type', ['hot dish', 'cold dish', 'drink', 'dessert']);
            $table->string('description');
            $table->string('photo_url');
            $table->decimal('price', 8, 2);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['H', 'P', 'R', 'T', 'D', 'C']);
            // H "Holding", P "Preparing", R "Ready", T "in Transit", D "Delivered", C "Cancelled"
            $table->bigInteger('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->text('notes')->nullable();
            $table->decimal('total_price', 8, 2);
            $table->date('date');                          // Order date (only the day)

            // The cook that prepared the order
            // null if order was not prepared (cancelled before being ready)
            $table->bigInteger('prepared_by')->unsigned()->nullable();
            $table->foreign('prepared_by')->references('id')->on('users');

            // The deliveryman that delivered the order
            // null if order was not delivered (cancelled before being delivered)
            $table->bigInteger('delivered_by')->unsigned()->nullable();
            $table->foreign('delivered_by')->references('id')->on('users');

            // Time related information about the order
            $table->dateTime('opened_at');                   // Date and Time when order was opened (status = "H" or "P")
            $table->dateTime('current_status_at');            // Date and Time when order has entered current status
            $table->dateTime('closed_at')->nullable();       // Date and Time when order was closed (status = "D" or "C")
            $table->integer('preparation_time')->nullable(); // Time (in seconds) to prepare the order (from Prepared to Ready)
            $table->integer('delivery_time')->nullable();    // Time (in seconds) to deliver the order (from In Transit to Delivered)
            $table->integer('total_time')->nullable();       // Total time (in seconds) to handle the order (closed_time - opened_time)

            $table->timestamps();

            // Index by date & by status for faster queries
            $table->index('date');
            $table->index('status');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->bigInteger('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 8, 2);
            $table->decimal('sub_total_price', 8, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('customers');
        Schema::table('users', function ($table) {
            $table->dropColumn(['type', 'blocked', 'photo_url', 'logged_at', 'deleted_at']);
        });
    }
}
