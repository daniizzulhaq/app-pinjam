<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NasabahController extends Controller
{
    public function index(Request $request)
    {
        $nasabah = Nasabah::with('karyawan')
            ->when($request->search, fn($q) =>
                $q->where('nama_lengkap', 'like', '%'.$request->search.'%')
                  ->orWhere('no_ktp', 'like', '%'.$request->search.'%')
            )
            ->latest()
            ->paginate(15);

        return view('admin.nasabah.index', compact('nasabah'));
    }

    public function create()
    {
        $karyawan = User::where('role', 'karyawan')
                        ->where('is_active', 1)
                        ->orderBy('name')
                        ->get();

        return view('admin.nasabah.create', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap'  => ['required', 'string', 'max:255'],
            'no_ktp'        => ['required', 'string', 'size:16', 'unique:nasabah,no_ktp'],
            'tempat_lahir'  => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'no_telepon'    => ['required', 'string', 'max:15'],
            'pekerjaan'     => ['nullable', 'string', 'max:100'],
            'alamat'        => ['required', 'string'],
            'kota'          => ['nullable', 'string', 'max:100'],
            'provinsi'      => ['nullable', 'string', 'max:100'],
            'karyawan_id'   => ['required', 'exists:users,id'],
            'foto_ktp'      => ['nullable', 'image', 'max:2048'],
            'foto_nasabah'  => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto_ktp')) {
            $validated['foto_ktp'] = $request->file('foto_ktp')
                ->store('nasabah/ktp', 'public');
        }

        if ($request->hasFile('foto_nasabah')) {
            $validated['foto_nasabah'] = $request->file('foto_nasabah')
                ->store('nasabah/foto', 'public');
        }

        $validated['user_id'] = $validated['karyawan_id'];
        unset($validated['karyawan_id']);

        $nasabah = Nasabah::create($validated);

        return redirect()->route('admin.nasabah.show', $nasabah)
                         ->with('success', 'Data nasabah berhasil disimpan.');
    }

    public function show(Nasabah $nasabah)
    {
        $nasabah->load(['pinjaman.pembayaran', 'karyawan']);
        return view('admin.nasabah.show', compact('nasabah'));
    }

    public function edit(Nasabah $nasabah)
    {
        $karyawan = User::where('role', 'karyawan')
                        ->where('is_active', 1)
                        ->orderBy('name')
                        ->get();

        return view('admin.nasabah.edit', compact('nasabah', 'karyawan'));
    }

    public function update(Request $request, Nasabah $nasabah)
    {
        $validated = $request->validate([
            'nama_lengkap'  => ['required', 'string', 'max:255'],
            'no_ktp'        => ['required', 'string', 'size:16', 'unique:nasabah,no_ktp,'.$nasabah->id],
            'tempat_lahir'  => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'no_telepon'    => ['required', 'string', 'max:15'],
            'pekerjaan'     => ['nullable', 'string', 'max:100'],
            'alamat'        => ['required', 'string'],
            'kota'          => ['nullable', 'string', 'max:100'],
            'provinsi'      => ['nullable', 'string', 'max:100'],
            'karyawan_id'   => ['required', 'exists:users,id'],
            'foto_ktp'      => ['nullable', 'image', 'max:2048'],
            'foto_nasabah'  => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto_ktp')) {
            if ($nasabah->foto_ktp) {
                Storage::disk('public')->delete($nasabah->foto_ktp);
            }
            $validated['foto_ktp'] = $request->file('foto_ktp')
                ->store('nasabah/ktp', 'public');
        } else {
            unset($validated['foto_ktp']);
        }

        if ($request->hasFile('foto_nasabah')) {
            if ($nasabah->foto_nasabah) {
                Storage::disk('public')->delete($nasabah->foto_nasabah);
            }
            $validated['foto_nasabah'] = $request->file('foto_nasabah')
                ->store('nasabah/foto', 'public');
        } else {
            unset($validated['foto_nasabah']);
        }

        $validated['user_id'] = $validated['karyawan_id'];
        unset($validated['karyawan_id']);

        $nasabah->update($validated);

        return redirect()->route('admin.nasabah.show', $nasabah)
                         ->with('success', 'Data nasabah berhasil diperbarui.');
    }

    public function destroy(Nasabah $nasabah)
    {
        if ($nasabah->foto_ktp) {
            Storage::disk('public')->delete($nasabah->foto_ktp);
        }
        if ($nasabah->foto_nasabah) {
            Storage::disk('public')->delete($nasabah->foto_nasabah);
        }

        $nasabah->delete();

        return redirect()->route('admin.nasabah.index')
                         ->with('success', 'Data nasabah berhasil dihapus.');
    }
}