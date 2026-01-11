<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name_fa')->nullable()->after('name');
            $table->string('name_en')->nullable()->after('name_fa');
            $table->string('family_name_fa')->nullable()->after('name_en');
            $table->string('family_name_en')->nullable()->after('family_name_fa');
            $table->string('phone_no',16)->nullable()->unique()->after('password');
            $table->string('affiliation_fa')->nullable()->after('phone_no');
            $table->string('affiliation_en')->nullable()->after('affiliation_fa');
            $table->integer('country_id')->nullable();
            $table->string('profile_photo')->after('country_id')->nullable();
            $table->integer('province_id')->nullable();
            $table->string('city_fa')->nullable();
            $table->string('city_en')->nullable();
            $table->string('department_fa')->nullable();
            $table->string('department_en')->nullable();
            $table->string('preferred_research_area_fa')->nullable();
            $table->string('preferred_research_area_en')->nullable();
            $table->softDeletes();
            $table->integer('education_degree_id')->nullable();
            $table->integer('academic_rank_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'name_fa',
                'name_en',
                'family_name_fa',
                'family_name_en',
                'phone_no',
                'affiliation_fa',
                'affiliation_en',
                'country_id',
                'profile_photo',
                'deleted_at',
                'education_degree_id',
                'academic_rank_id',
                'province_id',
                'department_fa',
                'department_en',
                'preferred_research_area_fa',
                'preferred_research_area_en',
                'city_fa',
                'city_en',
            ]);
        });
    }
};
