<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('phone_no',16)->nullable()->unique()->after('password');
            $table->string('profile_photo')->nullable()->after('remember_token');
            $table->foreignId('role_id')->nullable()->constrained('access_roles')->nullOnDelete()->after('role_id');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('branch_id');
            $table->softDeletes();
            
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            
            $table->dropForeign(['role_id']);
            $table->dropForeign(['branch_id']);

            
            $table->dropColumn([
                'phone_no',
                'profile_photo',
                'role_id',
                'branch_id',
            ]);

            $table->dropColumn('is_active');

            
            $table->dropSoftDeletes();
        });
    }
};
