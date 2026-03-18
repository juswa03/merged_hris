<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalInformation extends Model
{
    use HasFactory;

    protected $table = 'personal_informations';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'suffix',
        'date_of_birth',
        'place_of_birth',
        'citizenship',
        'dual_citizenship_type',
        'dual_citizenship_country_id',
        'sex',
        'civil_status',
        'height',
        'weight',
        'blood_type',
        'position',
        'email',
        'telephone_no',
        'mobile_no',
        'agency_employee_no',
        'filing_type',
        'department',
    ];

    /**
     * Relationships
     */

    // One-to-One with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Country (if you have a Country model)
    public function dualCitizenshipCountry()
    {
        return $this->belongsTo(Country::class, 'dual_citizenship_country_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function permanentAddress()
    {
        return $this->hasOne(Address::class)->where('address_type', 'permanent');
    }

    public function residentialAddress()
    {
        return $this->hasOne(Address::class)->where('address_type', 'residential');
    }

        // Relationship to Government IDs
    public function governmentIds()
    {
        return $this->hasMany(GovernmentId::class, 'personal_information_id');
    }

    // Relationship to Spouse
    public function spouse()
    {
        return $this->hasOne(Spouse::class);
    }

    // Relationship to Children
    public function children()
    {
        return $this->hasMany(Child::class);

    // Relationship to Parents
    }public function parents()
    {
        return $this->hasOne(ParentInfo::class);
    }
    
    // Relationship to Educational Background
    public function educationalBackgrounds()
    {
        return $this->hasMany(EducationalBackground::class);   
    }

    // Relationship to Civil Service Eligibilities
    public function civilServiceEligibilities()
    {
        return $this->hasMany(CivilServiceEligibility::class);
    }

    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class);
    }

    public function membershipAssociations()
    {
        return $this->hasMany(MembershipAssociation::class);
    }

    public function learningDevelopments()
    {
        return $this->hasMany(LearningDevelopment::class);
    }

    public function specialSkillsHobbies()
    {
        return $this->hasMany(SpecialSkillsHobby::class, 'personal_information_id');
    }

    public function relationshipToAuthority()
    {
        return $this->hasOne(RelationshipToAuthority::class, 'personal_information_id');
    }

    public function legalCase()
    {
        return $this->hasOne(LegalCase::class, 'personal_information_id');
    }

    public function employmentSeparation()
    {
        return $this->hasOne(EmploymentSeparation::class, 'personal_information_id');
    }

    public function politicalActivity()
    {
        return $this->hasOne(PoliticalActivity::class, 'personal_information_id');
    }

    public function immigrationStatus()
    {
        return $this->hasOne(ImmigrationStatus::class);
    }

    public function specialStatus()
    {
        return $this->hasOne(SpecialStatus::class);
    }

        public function agencies()
    {
        return $this->hasMany(Agency::class);
    }

        public function assetsRealProperties()
    {
        return $this->hasMany(AssetsRealProperty::class, 'personal_information_id');
    }

        public function assetsPersonalProperties()
    {
        return $this->hasMany(AssetsPersonalProperty::class);
    }

    public function totalCosts()
    {
        return $this->hasMany(TotalCosts::class, 'personal_information_id');
    }

    public function liabilities()
    {
        return $this->hasMany(Liability::class);
    }

    public function businessInterests()
    {
        return $this->hasMany(BusinessInterest::class);
    }

        public function relativesInGovService()
    {
        return $this->hasMany(RelativeInGovService::class);
    }

    public function salnCertification()
    {
        return $this->hasOne(SalnCertification::class, 'personal_information_id');
    }

    
    
    }
