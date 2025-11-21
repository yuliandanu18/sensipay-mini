<?php

namespace App\Helpers;

use App\Models\Student;

class StudentHelper
{
    /**
     * Cari siswa berdasarkan identifier (biasanya nama),
     * dengan pencarian yang dilonggarkan (case-insensitive + LIKE).
     *
     * @param  string|null  $identifier
     * @return \App\Models\Student|null
     */
    public static function findStudent(?string $identifier): ?Student
    {
        if (! $identifier) {
            return null;
        }

        // Normalisasi
        $identifier = trim(mb_strtolower($identifier));

        // 1. Coba exact match (case-insensitive)
        $student = Student::whereRaw('LOWER(name) = ?', [$identifier])->first();
        if ($student) {
            return $student;
        }

        // 2. Coba LIKE (untuk nama yang kepanjangan / ada tambahan)
        $student = Student::whereRaw('LOWER(name) LIKE ?', ['%' . $identifier . '%'])->first();
        if ($student) {
            return $student;
        }

        // TODO: kalau nanti kamu punya kolom "student_code", bisa ditambah cek di sini

        return null;
    }
}
