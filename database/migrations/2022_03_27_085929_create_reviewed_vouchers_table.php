<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewedVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviewed_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voucher_id')->unique();
            $table->foreign('voucher_id')
                ->references('id')
                ->on('vouchers')
                ->onDelete('cascade');
            $table->text('remarks')->nullable();
            $table->date('endorsed_at')->nullable();
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
        Schema::dropIfExists('reviewed_vouchers');
    }
}
