<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('address');
            $table->string('city');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('check_in_radius')->default(100); // meters
            $table->string('api_key')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add branch_id to existing tables
        Schema::table('printers', function (Blueprint $table) {
            $table->foreignId('branch_id')->after('id')->nullable()->constrained();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('branch_id')->after('id')->nullable()->constrained();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')->after('id')->nullable()->constrained();
        });
    }

    public function down()
    {
        Schema::dropIfExists('branches');
    }
};
