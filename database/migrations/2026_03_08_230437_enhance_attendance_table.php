<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->string('check_in_method')->nullable()->after('check_in');
            $table->string('check_out_method')->nullable()->after('check_out');
            $table->string('check_in_ip')->nullable();
            $table->string('check_out_ip')->nullable();
            $table->string('check_in_location')->nullable();
            $table->string('check_out_location')->nullable();
            $table->decimal('check_in_lat', 10, 8)->nullable();
            $table->decimal('check_in_lng', 11, 8)->nullable();
            $table->decimal('check_out_lat', 10, 8)->nullable();
            $table->decimal('check_out_lng', 11, 8)->nullable();
            $table->string('device_id')->nullable();
            $table->string('qr_code_used')->nullable();
            $table->string('biometric_template_used')->nullable();
            $table->float('confidence_score')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
        });

        // Add overtime fields
        Schema::table('attendance', function (Blueprint $table) {
            $table->float('overtime_rate')->nullable()->after('overtime_hours');
            $table->boolean('is_weekend')->default(false)->after('is_holiday');
            $table->boolean('is_holiday')->default(false)->change();
        });
    }

    public function down()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_method',
                'check_out_method',
                'check_in_ip',
                'check_out_ip',
                'check_in_location',
                'check_out_location',
                'check_in_lat',
                'check_in_lng',
                'check_out_lat',
                'check_out_lng',
                'device_id',
                'qr_code_used',
                'biometric_template_used',
                'confidence_score',
                'metadata',
                'verified_by',
                'verified_at',
                'overtime_rate',
                'is_weekend',
            ]);
        });
    }
};
