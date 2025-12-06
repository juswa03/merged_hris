<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutoffType extends Model
{
    use HasFactory;
    protected $table = 'tbl_cutoff_types';
        
    protected $fillable = [
        'name',
    ];
}
