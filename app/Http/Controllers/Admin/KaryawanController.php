<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $karyawan = User::karyawan()
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
            )
            ->latest()
            ->paginate(15);

        return view('admin.karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        return view('admin.karyawan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                   => ['required', 'string', 'max:255'],
            'email'                  => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'               => ['required', 'confirmed', Rules\Password::defaults()],
            'nama_bank'              => ['nullable', 'string', 'max:100'],
            'no_rekening'            => ['nullable', 'string', 'max:50'],
            'nama_pemilik_rekening'  => ['nullable', 'string', 'max:255'],
        ]);

        User::create([
            'name'                   => $validated['name'],
            'email'                  => $validated['email'],
            'password'               => Hash::make($validated['password']),
            'role'                   => 'karyawan',
            'is_active'              => true,
            'nama_bank'              => $validated['nama_bank'],
            'no_rekening'            => $validated['no_rekening'],
            'nama_pemilik_rekening'  => $validated['nama_pemilik_rekening'],
        ]);

        return redirect()->route('admin.karyawan.index')
                         ->with('success', 'Akun karyawan berhasil dibuat.');
    }

    public function edit(User $karyawan)
    {
        abort_if($karyawan->isAdmin(), 403);
        return view('admin.karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, User $karyawan)
    {
        abort_if($karyawan->isAdmin(), 403);

        $validated = $request->validate([
            'name'                   => ['required', 'string', 'max:255'],
            'email'                  => ['required', 'email', 'unique:users,email,'.$karyawan->id],
            'password'               => ['nullable', 'confirmed', Rules\Password::defaults()],
            'is_active'              => ['boolean'],
            'nama_bank'              => ['nullable', 'string', 'max:100'],
            'no_rekening'            => ['nullable', 'string', 'max:50'],
            'nama_pemilik_rekening'  => ['nullable', 'string', 'max:255'],
        ]);

        $karyawan->update([
            'name'                   => $validated['name'],
            'email'                  => $validated['email'],
            'is_active'              => $request->boolean('is_active'),
            'password'               => $validated['password'] ? Hash::make($validated['password']) : $karyawan->password,
            'nama_bank'              => $validated['nama_bank'],
            'no_rekening'            => $validated['no_rekening'],
            'nama_pemilik_rekening'  => $validated['nama_pemilik_rekening'],
        ]);

        return redirect()->route('admin.karyawan.index')
                         ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(User $karyawan)
    {
        abort_if($karyawan->isAdmin(), 403);
        $karyawan->update(['is_active' => false]);
        return back()->with('success', 'Akun karyawan dinonaktifkan.');
    }
}