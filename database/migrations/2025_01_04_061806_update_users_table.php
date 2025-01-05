<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('location')->nullable();
            $table->string('organization_name')->nullable();
            $table->string('recipient_type')->nullable();
            $table->string('donor_type')->nullable();
          //  $table->string('phone')->nullable()->unique();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'location',
                'organization_name',
                'recipient_type',
                'donor_type',
                'phone',
                'address',
                'notes'
            ]);
        });
    }
}
