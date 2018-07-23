<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsAllocatedToCustomersAddressbookTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::table('address_books', function (Blueprint $table) {
            $table->boolean('is_used')->default(false);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('is_used')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('address_books', function (Blueprint $table) {
            $table->dropColumn('is_used');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('is_used');
        });
    }
}
