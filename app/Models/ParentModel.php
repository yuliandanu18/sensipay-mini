<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    use HasFactory;

    /**
     * Tabel yang digunakan oleh model ini.
     */
    protected $table = 'parents';

    /**
     * Kolom yang boleh diisi mass-assignment.
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'notes',
    ];

    /**
     * Relasi: satu orang tua bisa punya banyak siswa.
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }
}
