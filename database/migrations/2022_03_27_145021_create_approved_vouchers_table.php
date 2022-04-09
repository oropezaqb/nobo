<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprovedVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approved_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voucher_id')->unique();
            $table->foreign('voucher_id')
                ->references('id')
                ->on('vouchers')
                ->onDelete('cascade');
            $table->text('remarks')->nullable();
            $table->date('approved_at')->nullable();
            $table->date('endorsed_at')->nullable();
            $table->string('batch_number')->nullable();
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
        Schema::dropIfExists('approved_vouchers');
    }
}
