<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarrantyFieldsToEquipmentContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('equipment_contracts', function (Blueprint $table) {
            $table->string('warranty_type')->default('none');
            $table->integer('warranty_period')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('equipment_contracts', function (Blueprint $table) {
            $table->dropColumn(['warranty_type', 'warranty_period']);
        });
    }
}
