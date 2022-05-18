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
            $table->enum('classification', ['OPEX', 'CAPEX', 'Power']);
            $table->boolean('petty');
            $table->string('bill_number')->nullable();
            $table->date('billed_at')->nullable();
            $table->string('po_number')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->date('due_at');
            $table->date('endorsed_at')->nullable();
            $table->text('particulars');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->timestamps();
            $table->unique(['payee_id', 'bill_number']);
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
