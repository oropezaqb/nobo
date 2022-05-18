<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voucher_id')->unique();
            $table->foreign('voucher_id')
                ->references('id')
                ->on('vouchers')
                ->onDelete('cascade');
            $table->string('check_number')->nullable();
            $table->date('check_date')->nullable();
            $table->string('cancelled_checks')->nullable();
            $table->text('remarks')->nullable();
            $table->date('paid_at')->nullable();
            $table->date('cleared_at')->nullable();
            $table->decimal('cleared_amount', 13, 2)->default(0);
            $table->decimal('service_charge', 13, 2)->default(0);
            $table->string('receipt_number')->nullable();
            $table->date('receipt_received_at')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
