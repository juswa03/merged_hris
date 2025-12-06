<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeductionType extends Model
{
    use HasFactory;

    protected $table = 'tbl_deduction_type';

    protected $fillable = [
        'name',
        'description',
    ];

    public function deductions()
    {
        return $this->hasMany(Deduction::class, 'deduction_type_id');
    }
}
