<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NasabahController extends Controller
{
    public function index(Request $request)
    {
        $nasabah = Nasabah::where('user_id', auth()->id())
            ->with(['pinjaman' => fn($q) => $q->where('status', 'aktif')->latest()])
            ->when($request->search, fn($q) =>
                $q->where('nama_lengkap', 'like', '%'.$request->search.'%')
                  ->orWhere('no_ktp', 'like', '%'.$request->search.'%')
            )
            ->latest()
            ->paginate(15);

        return view('karyawan.nasabah.index', compact('nasabah'));
    }

    public function create()
    {
        return view('karyawan.nasabah.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_ktp'        => ['required', 'string', 'max:20', 'unique:nasabah,no_ktp'],
            'nama_lengkap'  => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tanggal_lahir' => ['required', 'date'],
            'tempat_lahir'  => ['required', 'string', 'max:100'],
            'alamat'        => ['required', 'string'],
            'kota'          => ['required', 'string', 'max:100'],
            'provinsi'      => ['required', 'string', 'max:100'],
            'no_telepon'    => ['required', 'string', 'max:15'],
            'email'         => ['nullable', 'email'],
            'pekerjaan'     => ['nullable', 'string', 'max:100'],
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

        $validated['user_id'] = auth()->id();
        $newNasabah = Nasabah::create($validated);

        return redirect()->route('karyawan.nasabah.show', $newNasabah)
                         ->with('success', 'Data nasabah berhasil disimpan.');
    }

    public function show(Nasabah $nasabah)
    {
        $nasabah->load('pinjaman.pembayaran');
        return view('karyawan.nasabah.show', compact('nasabah'));
    }

    public function edit(Nasabah $nasabah)
    {
        return view('karyawan.nasabah.edit', compact('nasabah'));
    }

    public function update(Request $request, Nasabah $nasabah)
    {
        $validated = $request->validate([
            'no_ktp'        => ['required', 'string', 'max:20', 'unique:nasabah,no_ktp,'.$nasabah->id],
            'nama_lengkap'  => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tanggal_lahir' => ['required', 'date'],
            'tempat_lahir'  => ['required', 'string', 'max:100'],
            'alamat'        => ['required', 'string'],
            'kota'          => ['required', 'string', 'max:100'],
            'provinsi'      => ['required', 'string', 'max:100'],
            'no_telepon'    => ['required', 'string', 'max:15'],
            'email'         => ['nullable', 'email'],
            'pekerjaan'     => ['nullable', 'string', 'max:100'],
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

        $nasabah->update($validated);

        return back()->with('success', 'Data nasabah berhasil diperbarui.');
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

        return redirect()->route('karyawan.nasabah.index')
                         ->with('success', 'Data nasabah berhasil dihapus.');
    }
}