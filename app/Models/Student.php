<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ParentModel;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'whatsapp',
        'school',
        'grade',
        'parent_name',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    
    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

}
