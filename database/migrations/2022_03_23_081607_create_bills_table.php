<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->date('received_at');
            $table->unsignedBigInteger('payee_id');
            $table->foreign('payee_id')
                ->references('id')
                ->on('payees');
            $table->decimal('amount', 13, 2);
            $table->string('bill_number');
            $table->unsignedBigInteger('po_number');
            $table->string('period_covered');
            $table->date('due_at');
            $table->date('endorsed_at');
            $table->text('particulars');
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

    }
}
