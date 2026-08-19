<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        // Personal Information
        'first_name', 'last_name', 'father_name', 'gender', 'date_of_birth',
        'national_id', 'phone_number', 'whatsapp_number', 'passport_number', 'mother_name',
        'nickname', 'marital_status', 'blood_type',
        // Address
        'province', 'district', 'current_address', 'permanent_address', 'postal_code',
        // Education
        'last_education_level', 'last_school_name', 'graduation_year',
        'field_of_study', 'gpa', 'university_name', 'other_certifications',
        // Emergency Contact
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
        // Additional
        'skills', 'languages', 'about_me', 'profile_photo', 'is_complete',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_complete' => 'boolean',
        'gpa' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
