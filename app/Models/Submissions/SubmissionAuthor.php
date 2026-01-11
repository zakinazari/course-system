<?php

namespace App\Models\Submissions;

use Illuminate\Database\Eloquent\Model;
use App\Models\Settings\Country;
use App\Models\Settings\Province;
use App\Models\Settings\EducationDegree;
use App\Models\Settings\AcademicRank;
use App\Models\User;
class SubmissionAuthor extends Model
{
    protected $fillable = [
        'submission_id',
        'user_id',
        'type_id',
        'country_id',
        'province_id',
        'given_name_fa',
        'given_name_en',
        'family_name_fa',
        'family_name_en',
        'education_degree_id',
        'academic_rank_id',
        'department_fa',
        'department_en',
        'preferred_research_area_fa',
        'preferred_research_area_en',
        'phone_no',
        'affiliation_fa',
        'affiliation_en',
        'city_fa',
        'city_en',
        'email',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    public function type()
    {
        return $this->belongsTo(AuthorType::class, 'type_id');
    }
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
    public function educationDegree()
    {
        return $this->belongsTo(EducationDegree::class, 'academic_rank_id');
    }
    public function academicRank()
    {
        return $this->belongsTo(AcademicRank::class, 'academic_rank_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
