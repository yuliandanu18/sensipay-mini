
<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ParentManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('parentUser');

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('school_name', 'like', '%' . $search . '%');
            });
        }

        $students = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('sensipay.parents.index', compact('students', 'search'));
    }

    public function edit(Student $student)
    {
        $parent = $student->parentUser;

        return view('sensipay.parents.edit', compact('student', 'parent'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'parent_name'  => ['required', 'string', 'max:255'],
            'parent_email' => ['required', 'email', 'max:255'],
        ]);

        $generatedPassword = null;

        // Cari user parent berdasarkan email
        $user = User::where('email', $data['parent_email'])->first();

        if (! $user) {
            // Buat user baru role parent
            $generatedPassword = Str::random(10);

            $user = User::create([
                'name'     => $data['parent_name'],
                'email'    => $data['parent_email'],
                'password' => bcrypt($generatedPassword),
                'role'     => 'parent',
            ]);
        } else {
            // Update nama & pastikan rolenya parent
            $user->name = $data['parent_name'];
            if ($user->role !== 'parent') {
                $user->role = 'parent';
            }
            $user->save();
        }

        // Pasangkan ke siswa
        $student->parent_user_id = $user->id;
        $student->save();

        $message = 'Akun orang tua berhasil dihubungkan ke siswa.';

        if ($generatedPassword) {
            // Kirim password ke session untuk ditampilkan sekali
            session()->flash('generated_parent_password', $generatedPassword);
            $message .= ' Password baru telah dibuat. Mohon dicatat dan dikirim ke orang tua.';
        }

        return redirect()
            ->route('sensipay.parents.edit', $student)
            ->with('status', $message);
    }
}
