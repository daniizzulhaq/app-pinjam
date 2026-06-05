<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfilAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    /**
     * Tampilkan halaman profil admin.
     */
    public function index()
    {
        $profil = ProfilAdmin::profil();

        return view('admin.profil.index', compact('profil'));
    }

    /**
     * Simpan / update profil admin.
     */
    public function update(Request $request)
    {
        $request->validate([
            'nama_lembaga' => 'required|string|max:100',
            'tagline'      => 'nullable|string|max:150',
            'logo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'no_telepon'   => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:100',
            'alamat'       => 'nullable|string|max:500',

            // Rekening: array dinamis dari form
            'rekening'           => 'nullable|array',
            'rekening.*.bank'    => 'required_with:rekening|string|max:50',
            'rekening.*.no_rek'  => 'required_with:rekening|string|max:30',
            'rekening.*.atas_nama' => 'required_with:rekening|string|max:100',
        ], [
            'nama_lembaga.required' => 'Nama lembaga wajib diisi.',
            'logo.image'            => 'File logo harus berupa gambar.',
            'logo.max'              => 'Ukuran logo maksimal 2 MB.',
            'rekening.*.bank.required_with'      => 'Nama bank wajib diisi.',
            'rekening.*.no_rek.required_with'    => 'Nomor rekening wajib diisi.',
            'rekening.*.atas_nama.required_with' => 'Atas nama wajib diisi.',
        ]);

        $profil = ProfilAdmin::profil();

        $data = $request->only([
            'nama_lembaga', 'tagline', 'no_telepon', 'email', 'alamat',
        ]);

        // --- Upload Logo ---
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($profil->logo) {
                Storage::disk('public')->delete($profil->logo);
            }

            $data['logo'] = $request->file('logo')
                ->store('profil', 'public');
        }

        // --- Hapus Logo ---
        if ($request->boolean('hapus_logo') && $profil->logo) {
            Storage::disk('public')->delete($profil->logo);
            $data['logo'] = null;
        }

        // --- Rekening ---
        // Bersihkan baris rekening yang kosong sepenuhnya
        $rekening = collect($request->input('rekening', []))
            ->filter(fn($r) => !empty($r['bank']) || !empty($r['no_rek']))
            ->values()
            ->toArray();

        $data['rekening'] = empty($rekening) ? null : $rekening;

        $profil->update($data);

        return redirect()
            ->route('admin.profil.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}